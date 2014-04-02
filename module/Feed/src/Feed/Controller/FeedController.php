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
    const ROUTE_ACCOUNT_SUMMONERS = "account/summoners";
    const ROUTE_LOGIN = "login";
    const ROUTE_HOME = "home";
    const ROUTE_ADD_FEED = "feed/add";

    const MESSAGE_RATE_SUCCESS = 'The rating has been saved successfully.';
    const MESSAGE_RATE_FAIL = 'Something went wrong when saving the rating, please try again.';
    const MESSAGE_ADD_SUCCESS = "The feed has been added successfully.";

    const ERROR_STORE_FEED = "There was an error when storing the feed.";

    /**
     * The add feed form
     *
     * @var \Zend\Form\Form
     */
    private $addFeedForm;

    /**
     * The entity manager
     *
     * @var \Doctrine\ORM\EntityManager
     */
    private $entityManager;

    /**
     * The feed repository
     *
     * @var \Feed\Repository\FeedRepository
     */
    private $feedRepository;

    /**
     * The feed service
     *
     * @var \Feed\Service\Feed
     */
    private $feedService;

    /**
     * The feed generator
     *
     * @var \Feed\Model\FeedGenerator
     */
    private $generator;

    /**
     * The summoner repository
     *
     * @var EntityRepository
     */
    private $summonerRepository;

    /**
     * The zend translator
     *
     * @var \Zend\I18n\Translator\Translator
     */
    private $translator;

    /**
     * The youtuber repository
     *
     * @var EntityRepository
     */
    private $youtuberRepository;

    /**
     * The youtube service
     *
     * @var \Youtube\Service\Youtube
     */
    private $youtubeService;

    /**
     * The improve action.
     * Route: /improve
     *
     * @return \Zend\Http\Response|ViewModel
     */
    public function improveAction()
    {
        if ($this->identity()) {
            $summoners = $this->account() ? $this->account()->getSummoners() : array();
            if ($summoners->count() <= 0) {
                return $this->redirect()->toRoute(self::ROUTE_ACCOUNT_SUMMONERS);
            } else {
                $summonerId = $this->params()->fromRoute("summonerId", null);
                $summoner = $summonerId ? $this->getSummonerRepository()->find($summonerId) : $summoners[0];
                if ($summoner) {
                    $feeds = $this->getFeedService()->getSummonerFeeds($summoner);
                    return new ViewModel(array(
                        "feeds" => $feeds,
                        "summoners" => $summoners,
                        "activeSummonerName" => $summoner->getName(),
                        "pageTitle" => "Improve your League of Legends play skills"
                    ));
                } else {
                    return $this->notFoundAction();
                }
            }
        } else {
            return $this->redirect()->toRoute(self::ROUTE_LOGIN);
        }
    }

    /**
     * The random action.
     * Route: /random
     *
     * @return ViewModel
     */
    public function randomAction()
    {
        return new ViewModel(array(
            "includeProgressBar" => true,
            "pageTitle" => "Find a random League of Legends feed!"
        ));
    }

    /**
     * The get random feed action.
     * Only accessed via xmlHttpRequest
     * Route: /feed/get-random-feed
     *
     * @return array|JsonModel
     */
    public function getRandomFeedAction()
    {
        /**
         * @var $request \Zend\Http\Request
         */
        $request = $this->getRequest();
        if ($request->isXmlHttpRequest()) {
            $generator = $this->getGenerator();
            $feed = $generator->getRandomFeed();
            $feedId = $feed->getFeedId();
            return new JsonModel(array(
                "feedId" => $feedId
            ));
        } else {
            return $this->notFoundAction();
        }
    }

    /**
     * The add to watched action.
     * Only access via xmlHttpRequest
     * Route: /feed/add-to-watched
     *
     * @return array|JsonModel
     */
    public function addToWatchedAction()
    {
        /**
         * @var $request \Zend\Http\Request
         */
        $request = $this->getRequest();
        if ($request->isXmlHttpRequest()) {
            $success = 0;
            $message = '';
            if ($this->identity()) {
                $feedId = $this->params()->fromRoute("feedId");
                $feed = $this->getFeedService()->addFeedToWatched($feedId);
                if ($feed) {
                    $success = 1;
                } else {
                    $message = self::ERROR_STORE_FEED;
                }
            }
            return new JsonModel(array(
                "success" => $success,
                "message" => $message
            ));

        } else {
            return $this->notFoundAction();
        }
    }

    /**
     * The famous action.
     * Route: /famous
     *
     * @return ViewModel
     */
    public function famousAction()
    {
        $youtubers = $this->getYoutuberRepository()->findBy(array(), array("igName" => "ASC"));
        $rand = rand(0, count($youtubers) - 1);
        $randomYoutuber = $youtubers[$rand];
        $feeds = $this->getFeedService()->getYoutuberUploads($randomYoutuber);

        return new ViewModel(array(
            "youtubers" => $youtubers,
            "feeds" => $feeds["feeds"],
            "nextToken" => $feeds["nextToken"],
            "bodyClass" => "famousPage",
            "noAds" => true,
            "randomYoutuber" => $randomYoutuber,
            "pageTitle" => "Feeds from the League of Legends pros and famous youtubers"
        ));
    }

    /**
     * The get youtubers feeds action.
     * Only accessed via xmlHttpRequest.
     * Route: /feed/get-youtubers-feeds
     *
     * @return array|ViewModel
     */
    public function getYoutuberFeedsAction()
    {
        /**
         * @var $request \Zend\Http\Request
         */
        $request = $this->getRequest();
        if ($request->isXmlHttpRequest()) {
            $returnFeeds = $this->params()->fromRoute("returnFeeds");
            $nextToken = $this->params()->fromRoute("nextToken");
            $youtuberName = $this->params()->fromRoute("youtuberName");
            $youtuber = $this->getYoutuberRepository()->findOneBy(array('name' => $youtuberName));
            $feeds = $this->getFeedService()->getYoutuberUploads($youtuber, $nextToken);
            if ($returnFeeds == "return") {
                $view = new ViewModel();
                $view->setVariable("feeds", $feeds["feeds"]);
                $view->setTerminal(true);
                return $view;
            } else {
                return new JsonModel(array(
                    "nextToken" => $feeds["nextToken"]
                ));
            }
        } else {
            return $this->notFoundAction();
        }
    }

    /**
     * The leet action.
     * Route: /leet
     *
     * @return array|ViewModel
     */
    public function leetAction()
    {
        if (!$this->identity()) {
            return $this->notFoundAction();
        }
        $account = $this->account();
        return new ViewModel(array(
            "feeds" => $account->getLikedFeeds(),
            "pageTitle" => "Leeted feeds",
            "noAds" => true
        ));
    }

    /**
     * The history action.
     * Route: /history
     *
     * @return array|ViewModel
     */
    public function historyAction()
    {
        if (!$this->identity()) {
            return $this->notFoundAction();
        }
        $sort = $this->params()->fromRoute("sort");
        $account = $this->account();
        $feeds = $this->getFeedRepository()->findFeedsByDate($account, $sort);
        return new ViewModel(array(
            "feeds" => $feeds,
            "sort" => $sort,
            "pageTitle" => "The feeds you have watched",
            "noAds" => true
        ));
    }

    public function addAction()
    {
        if ($this->account()->hasSuperPrivileges()) {
            $form = $this->getAddFeedForm();
            $request = $this->getRequest();
            if ($request->isPost()) {
                $data = $request->getPost();
                $form->setData($data);
                if($form->isValid()){
                    /**
                     * @var $feed \Feed\Entity\Feed
                     */
                    parse_str(parse_url($data["feed"]["url"], PHP_URL_QUERY), $vars);
                    $feed = $this->getFeedRepository()->findOneBy(array("videoId" => $vars['v']));
                    if(!$feed){
                        $feed = $this->getFeedService()->add($data);
                    }else{
                        $em = $this->getEntityManager();
                        $feed->setIsRelated(0);
                        $feed->updatePostDate();
                        $em->persist($feed);
                        $em->flush();
                    }
                    $this->flashMessenger()->addMessage(self::MESSAGE_ADD_SUCCESS);
                    return $this->redirect()->toRoute(self::ROUTE_ADD_FEED);
                }
            } else {
                $feedId = $this->params()->fromRoute("feedId", null);
                if ($feedId) {
                    $type = $this->params()->fromRoute("type");
                    $em = $this->getEntityManager();
                    if ($type == "exists") {
                        /**
                         * @var $feed \Feed\Entity\Feed
                         */
                        $feed = $this->getFeedRepository()->find($feedId);
                        $feed->setIsRelated(0);
                        $feed->setIsIgnored(0);
                        $feed->updatePostDate();
                        $em->persist($feed);
                        $em->flush();
                        return $this->redirect()->toRoute(self::ROUTE_HOME);
                    }
                }
            }
            return new ViewModel(array(
                "form" => $form,
                "noAds" => true
            ));
        }
        return $this->notFoundAction();

    }

    public function generateAction(){
        if ($this->identity() && $this->account()->hasSuperPrivileges()) {
            $nextToken = $this->params()->fromRoute("nextToken");
            $response = $this->getYoutubeService()->findByQuery("league of legends", null, 50, "this_week",$nextToken);
            $feedService = $this->getFeedService();
            $videos = $response->getVideos();
            $feeds = array();
            $em = $this->getEntityManager();
            foreach ($videos as $video) {
                $feeds[] = $feedService->createFromEntry($video,1,false,true,$em);
            }
            $em->flush();
            return new ViewModel(array(
                "nextToken" => $response->getNextPageToken(),
                "feeds" => $feeds,
                "noAds" => true,
                "bodyClass" => "generatePage"
            ));
        }
        return $this->notFoundAction();
    }

    public function removeAction()
    {
        $feedId = $this->params()->fromRoute("feedId", null);
        $type = $this->params()->fromRoute("type");
        if ($feedId) {
            if ($this->account()->hasSuperPrivileges()) {
                /**
                 * @var $feed \Feed\Entity\Feed
                 */
                $feed = $this->getFeedRepository()->find($feedId);
                $em = $this->getEntityManager();
                if ($type == "delete") {
                    $em->remove($feed);
                } else {
                    $feed->setIsIgnored(1);
                    $em->persist($feed);
                }
                $em->flush();
                return $this->redirect()->toRoute('home');
            } else {
                return $this->notFoundAction();
            }
        }
    }

    /**
     * The view action.
     * Route: /feed/:feedId
     *
     * @return array|\Zend\Http\Response|ViewModel
     */
    public function viewAction()
    {
        $feedId = $this->params()->fromRoute("feedId", null);
        if ($feedId) {
            /**
             * @var $feed \Feed\Entity\Feed
             */
            $feed = $this->getFeedRepository()->find($feedId);
            if ($feed) {
                if (!$feed->getAuthor()) {
                    $em = $this->getEntityManager();
                    $video = $this->getYoutubeService()->findVideoById($feed->getVideoId());
                    $feed->setAuthor($video->getChannel()->getTitle());
                    $em->persist($feed);
                    $em->flush();
                }
                if ($this->identity()) $this->getFeedService()->addFeedToWatched($feedId);
                $related = $this->getGenerator()->getRelatedFeeds($feed);
                $metaInfo = array();
                $metaInfo["keywords"] = $feed->getKeywords();
                $metaInfo['description'] = $feed->getCleanDescription();
                return new ViewModel(array(
                    "feed" => $feed,
                    'ogTags' => $feed->getOgTags(),
                    "pageTitle" => $feed->getTitle(),
                    "relatedFeeds" => $related,
                    "metaInfo" => $metaInfo,
                    "bodyClass" => "feedPage"
                ));
            } else {
                return $this->redirect()->toRoute(self::ROUTE_RANDOM);
            }
        } else {
            return $this->notFoundAction();
        }
    }

    /**
     * The rate action.
     * Only accessed via xmlHttpRequest
     * Route: /feed/rate/:rating/id/:id
     *
     * @return array|JsonModel
     */
    public function rateAction()
    {
        /**
         * @var $request \Zend\Http\Request
         */
        $request = $this->getRequest();
        if ($request->isXmlHttpRequest() && $this->identity()) {
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
            return $this->notFoundAction();
        }
    }

    /**
     * Retrieve the add feed form
     *
     * @return \Zend\Form\Form
     */
    public function getAddFeedForm(){
        if(null === $this->addFeedForm)
            $this->addFeedForm = $this->getServiceLocator()->get('add_feed_form');
        return $this->addFeedForm;
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
     * @return \Feed\Repository\FeedRepository
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
     * @return \Feed\Model\FeedGenerator
     */
    public function getGenerator()
    {
        if (null === $this->generator)
            $this->generator = $this->getServiceLocator()->get("generator");
        return $this->generator;
    }

    /**
     * Retrieve the summoner repository
     *
     * @return EntityRepository
     */
    public function getSummonerRepository()
    {
        if (null === $this->summonerRepository)
            $this->summonerRepository = $this->getEntityManager()->getRepository('\League\Entity\Summoner');
        return $this->summonerRepository;
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
     * Retrieve the youtube service
     *
     * @return \Youtube\Service\Youtube
     */
    public function getYoutubeService()
    {
        if (null === $this->youtubeService)
            $this->youtubeService = $this->getServiceLocator()->get('youtube_service');
        return $this->youtubeService;
    }
}
