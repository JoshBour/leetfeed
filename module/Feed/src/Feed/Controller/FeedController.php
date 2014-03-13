<?php
/**
 * Zend Framework (http://framework.zend.com/)
 *
 * @link      http://github.com/zendframework/ZendSkeletonApplication for the canonical source repository
 * @copyright Copyright (c) 2005-2014 Zend Technologies USA Inc. (http://www.zend.com)
 * @license   http://framework.zend.com/license/new-bsd New BSD License
 */

namespace Feed\Controller;

use Doctrine\ORM\EntityRepository;
use Zend\Mvc\Controller\AbstractActionController;
use Zend\View\Model\JsonModel;
use Zend\View\Model\ViewModel;

class FeedController extends AbstractActionController
{
    const ROUTE_RANDOM = "random";

    const MESSAGE_RATE_SUCCESS = 'The rating has been saved successfully.';
    const MESSAGE_RATE_FAIL = 'Something went wrong when saving the rating, please try again.';

    private $accountService;

    private $entityManager;

    private $feedRepository;

    private $feedService;

    private $generator;

    private $translator;

    private $youtuberRepository;

    private $youtubeService;

    public function famousAction()
    {
        $youtubers = $this->getYoutuberRepository()->findBy(array(),array("igName" => "ASC"));
        $rand = rand(0,count($youtubers)-1);
        $randomYoutuber = $youtubers[$rand];
        $feeds = $this->getYoutubeService()->getYoutuberUploads($randomYoutuber->getName());

        return new ViewModel(array(
            "youtubers" => $youtubers,
            "feeds" =>  $feeds,
            "randomYoutuber" => $randomYoutuber));
    }

    public function getYoutuberFeedsAction(){
        $request = $this->getRequest();
        if($request->isXmlHttpRequest()){
            $youtuberName = $this->params()->fromRoute("youtuberName");
            $feeds = $this->getYoutubeService()->getYoutuberUploads($youtuberName);
            $view = new ViewModel();
            $view->setVariable("feeds", $feeds);
            $view->setTerminal(true);
            return $view;
        }else{
            $this->getResponse()->setStatusCode(404);
            return;
        }
    }

    public function leetAction()
    {
        $account = $this->user();
        return new ViewModel(array("feeds" => $account->getLikedFeeds()));
    }

    public function historyAction(){
        $account = $this->user();
        return new ViewModel(array("feeds" => $account->getFeeds()));
    }

    public function viewAction()
    {
        $feedId = $this->params()->fromRoute("feedId", null);
        if ($feedId) {
            $feed = $this->getFeedRepository()->find($feedId);
            if ($feed) {
                if($feed->getIsRelated() == 1){
                    $em = $this->getEntityManager();
                    $feed->setIsRelated(0);
                    $em->persist($feed);
                    $em->flush();
                }
                $related = $this->getGenerator()->getRelatedFeeds($feed->getVideoId());
                $ogTags = $feed->getOgTags();
                return new ViewModel(array(
                    "feed" => $feed,
                    "account" => $this->user(),
                    'ogTags' => $ogTags,
                    "pageTitle" => $feed->getTitle(),
                    "relatedFeeds" => $related));
            } else {
                $this->redirect()->toRoute(self::ROUTE_RANDOM);
            }
        } else {
            $this->getResponse()->setStatusCode(404);
            return;
        }
    }

    public function rateAction()
    {
        if ($this->getRequest()->isXmlHttpRequest()) {
            $id = $this->params()->fromRoute('id');
            $rating = $this->params()->fromRoute('rating');
            $feed = $this->getFeedService()->rate($id, $rating);

            if ($feed) {
                $success = 1;
                $message = $this->getTranslator()->translate(self::MESSAGE_RATE_SUCCESS);
            } else {
                $success = 0;
                $message = $this->getTranslator()->translate(self::MESSAGE_RATE_FAIL);
            }
            return new JsonModel(array(
                    'success' => $success,
                    'message' => $message
                )
            );
        } else {
            $this->getResponse()->setStatusCode(404);
            return;
        }
    }

    /**
     * Retrieve the youtuber repository
     *
     * @return EntityRepository
     */
    public function getYoutuberRepository()
    {
        if (null === $this->youtuberRepository)
            $this->youtuberRepository = $this->getEntityManager()->getRepository('\Feed\Entity\Youtuber');
        return $this->youtuberRepository;
    }

    /**
     * @return \Feed\Service\Youtube
     */
    public function getYoutubeService()
    {
        if (null === $this->youtubeService)
            $this->youtubeService = $this->getServiceLocator()->get("youtube_service");
        return $this->youtubeService;
    }

    /**
     * @return \Feed\Model\FeedGenerator
     */
    public function getGenerator()
    {
        if (null === $this->generator)
            $this->generator = $this->getServiceLocator()->get("generator");
        return $this->generator;
    }

    /**
     * @return \Account\Service\Account
     */
    public function getAccountService()
    {
        if (null === $this->accountService)
            $this->accountService = $this->getServiceLocator()->get("account_service");
        return $this->accountService;
    }

    /**
     * Retrieve the doctrine entity manager
     *
     * @return \Doctrine\ORM\EntityManager
     */
    public function getEntityManager()
    {
        if (null === $this->entityManager) {
            $this->entityManager = $this->getServiceLocator()->get('Doctrine\ORM\EntityManager');
        }
        return $this->entityManager;
    }

    /**
     * Retrieve the account repository
     *
     * @return EntityRepository
     */
    public function getFeedRepository()
    {
        if (null === $this->feedRepository)
            $this->feedRepository = $this->getEntityManager()->getRepository('\Feed\Entity\Feed');
        return $this->feedRepository;
    }

    /**
     * Retrieve the feed service
     *
     * @return \Feed\Service\Feed
     */
    public function getFeedService()
    {
        if (null === $this->feedService)
            $this->feedService = $this->getServiceLocator()->get('feed_service');
        return $this->feedService;
    }

    /**
     * Retrieve the translator instance.
     *
     * @return \Zend\I18n\Translator\Translator
     */
    public function getTranslator()
    {
        if (null === $this->translator)
            $this->translator = $this->getServiceLocator()->get('translator');
        return $this->translator;
    }
}
