<?php
namespace Feed\Service;

use Doctrine\ORM\EntityRepository;
use Zend\ServiceManager\ServiceManager;
use Zend\ServiceManager\ServiceManagerAwareInterface;

class Comment implements ServiceManagerAwareInterface
{

    /**
     * @var EntityRepository
     */
    private $accountPlugin;

    /**
     * @var EntityRepository
     */
    private $commentRepository;

    /**
     * var EntityManager
     */
    private $entityManager;

    /**
     * @var \Feed\Repository\FeedRepository
     */
    private $feedRepository;

    /**
     * @var ServiceManager
     */
    private $serviceManager;

    /**
     * Creates a new comment entity.
     *
     * @param int $feedId
     * @param string $content
     * @return bool|\Feed\Entity\Comment
     */
    public function create($feedId, $content)
    {
        $comment = new \Feed\Entity\Comment();
        $em = $this->getEntityManager();
        /**
         * @var $feed bool|\Feed\Entity\Feed
         */
        $feed = $this->getFeedRepository()->find($feedId);
        if ($feed) {
            $comment->setAccount($this->getAccountPlugin()->getAccount());
            $comment->setFeed($feed);
            $comment->setContent($content);
            $comment->setPostTime(date("Y-m-d H:i:s"));
            try {
                $em->persist($comment);
                $em->flush();
                return $comment;
            } catch (\Exception $e) {
                return false;
            }
        }
        return false;
    }

    /**
     * Removes a comment.
     *
     * @param int $commentId
     * @return bool
     */
    public function remove($commentId)
    {
        $em = $this->getEntityManager();
        $commentRepository = $this->getCommentRepository();
        if ($comment = $commentRepository->find($commentId)) {
            try {
                $em->remove($comment);
                $em->flush();
                return true;
            } catch (\Exception $e) {
                return false;
            }
        }
        return false;
    }


    /**
     * Retrieve the doctrine entity manager
     *
     * @return \Doctrine\ORM\EntityManager
     */
    public function getEntityManager()
    {
        if (null === $this->entityManager) {
            $this->entityManager = $this->getServiceManager()->get('Doctrine\ORM\EntityManager');
        }
        return $this->entityManager;
    }

    /**
     * Retrieve service manager instance
     *
     * @return ServiceManager
     */
    public function getServiceManager()
    {
        return $this->serviceManager;
    }

    /**
     * Set service manager instance
     *
     * @param ServiceManager $serviceManager
     * @return Feed
     */
    public function setServiceManager(ServiceManager $serviceManager)
    {
        $this->serviceManager = $serviceManager;
        return $this;
    }

    /**
     * Retrieve the account plugin
     *
     * @return \Account\Plugin\ActiveAccount
     */
    public function getAccountPlugin()
    {
        if (null === $this->accountPlugin)
            $this->accountPlugin = $this->getServiceManager()->get('ControllerPluginManager')->get('account');
        return $this->accountPlugin;
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
} 