<?php
/**
 * Zend Framework (http://framework.zend.com/)
 *
 * @link      http://github.com/zendframework/ZendSkeletonApplication for the canonical source repository
 * @copyright Copyright (c) 2005-2014 Zend Technologies USA Inc. (http://www.zend.com)
 * @license   http://framework.zend.com/license/new-bsd New BSD License
 */

namespace Feed\Controller;

use Doctrine\ORM\EntityManager;
use Doctrine\ORM\EntityRepository;
use Zend\Mvc\Controller\AbstractActionController;
use Zend\View\Model\JsonModel;
use Zend\View\Model\ViewModel;

class CommentController extends AbstractActionController
{

    const ROUTE_HOME = 'home';
    const MESSAGE_RATE_SUCCESS = 'The rating has been saved successfully.';
    const MESSAGE_RATE_FAIL = 'Something went wrong when saving the rating, please try again.';

    const ERROR_FEED_NOT_FOUND = "The feed could not be found.";

    /**
     * @var EntityRepository
     */
    private $commentRepository;

    /**
     * @var \Feed\Service\Comment
     */
    private $commentService;

    /**
     * @var EntityManager
     */
    private $entityManager;

    /**
     * @var \Feed\Repository\FeedRepository
     */
    private $feedRepository;

    /**
     * @var \Zend\I18n\Translator\Translator
     */
    private $translator;

    public function addAction()
    {
        if ($this->getRequest()->isXmlHttpRequest()) {
            $feedId = $this->params()->fromPost("feedId");
            $content = $this->params()->fromPost("content");
            $comment = $this->getCommentService()->create($feedId, $content);
            $model = new ViewModel();
            $model->setTerminal(true);
            $model->setVariable("comment", $comment);
            return $model;
        }else{
            $this->getResponse()->setStatusCode(404);
            return;
        }
    }

    public function removeAction()
    {
        if($this->getRequest()->isXmlHttpRequest()){
            $commentId = $this->params()->fromPost("commentId");
            $result = $this->getCommentService()->remove($commentId);
            $success = ($result) ? 1 : 0;
            return new JsonModel(array(
                "success" => $success
            ));
        }else{
            $this->getResponse()->setStatusCode(404);
            return;
        }
    }

    public function listAction()
    {
        $feedId = $this->params()->fromRoute("feedId");
        $feed = $this->getFeedRepository()->find($feedId);
        if ($feed) {
            $comments = $feed->getComments();
            $model = new ViewModel();
            $model->setTerminal($this->getRequest()->isXmlHttpRequest());
            $model->setVariable("comments", $comments);
            return $model;
        } else {
            $this->flashMessenger()->addMessage(self::ERROR_FEED_NOT_FOUND);
            return $this->redirect()->toRoute(self::ROUTE_HOME);
        }
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
     * Retrieve the comment repository
     *
     * @return EntityRepository
     */
    public function getCommentRepository()
    {
        if (null === $this->commentRepository)
            $this->commentRepository = $this->getEntityManager()->getRepository('\Feed\Entity\Comment');
        return $this->commentRepository;
    }

    /**
     * Retrieve the feed repository
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
     * Retrieve the comment service
     *
     * @return \Feed\Service\Comment
     */
    public function getCommentService()
    {
        if (null === $this->commentService)
            $this->commentService = $this->getServiceLocator()->get('comment_service');
        return $this->commentService;
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
