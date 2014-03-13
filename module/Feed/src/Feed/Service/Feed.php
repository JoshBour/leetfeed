<?php
/**
 * Created by PhpStorm.
 * User: Josh
 * Date: 27/2/2014
 * Time: 4:46 μμ
 */

namespace Feed\Service;


use Zend\ServiceManager\ServiceManagerAwareInterface;
use Zend\ServiceManager\ServiceManager;
use Doctrine\ORM\EntityManager;
use Doctrine\ORM\EntityRepository;

class Feed implements ServiceManagerAwareInterface{

    /**
     * var EntityManager
     */
    private $entityManager;

    /**
     * @var ServiceManager
     */
    private $serviceManager;

    /**
     * @var EntityRepository
     */
    private $accountPlugin;

    /**
     * @var EntityRepository
     */
    private $feedRepository;

    /**
     * @var EntityRepository
     */
    private $gameRepository;

    /**
     * @var \Youtube\Service\Youtube
     */
    private $youtubeService;

    /**
     * @var EntityRepository
     */
    private $watchedFeedRepository;

    public function getYoutuberUploads($youtuber){
        $feeds = $youtuber->getFeeds();
        $channel = $this->getYoutubeService()->findChannelByUsername($youtuber->getName());
        $feedRepository = $this->getFeedRepository();
        $game = $this->getGameRepository()->find(1);
        $em = $this->getEntityManager();
        $flush = false;
        $persistYoutuber = false;
        $uploads = $channel->getUploads();

        if(count($feeds) != count($uploads)){
            $feeds = array();
            /**
             * @var $video \Youtube\Model\Video
             */
            foreach($uploads as $video){
                if (!$feed = $feedRepository->findOneBy(array("videoId" => $video->getId()))) {
                    $feed = \Feed\Entity\Feed::create($game,
                        $video->getId(),
                        $video->getTitle(),
                        $channel->getTitle(),
                        $video->getDescription(),1,0);
                    $isPersisted = \Doctrine\ORM\UnitOfWork::STATE_MANAGED === $em->getUnitOfWork()->getEntityState($feed);
                    if(!$isPersisted) $em->persist($feed);
                    $flush = true;
                }
                if(!in_array($feed,$feeds)){
                    $feeds[] = $feed;
                    $youtuber->addFeeds($feed);
                    $persistYoutuber = true;
                }
            }
            if($persistYoutuber) $em->persist($youtuber);
            if($flush) $em->flush();
        }
        return $feeds;
    }

    /**
     * @param \Youtube\Model\Video $video
     * @param int $related
     * @param bool $flush
     * @return \Feed\Entity\Feed
     */
    public function createFromEntry($video,$related = 0, $flush = true){
        $game = $this->getGameRepository()->find(1);
        // save the feed to accounts feeds
        $accountFeed = \Feed\Entity\Feed::create($game, $video->getId(), $video->getTitle(), $video->getChannel()->getTitle(), $video->getDescription(),$related);
        $this->getEntityManager()->persist($accountFeed);
        return $accountFeed;
    }

    /**
     * @param int $feedId
     * @return bool
     */
    public function addFeedToWatched($feedId){
        $feed = $this->getFeedRepository()->find($feedId);
        if ($feed) {
            $em = $this->getEntityManager();
            $account = $this->getAccountPlugin()->getAccount();
            if (!$this->getWatchedFeedRepository()->findOneBy(array("feed"=>$feed,"account"=>$account))) {
                $watchedFeed = \Account\Entity\AccountsHistory::create($account,$feed);
                $account->addFeeds($watchedFeed);
                $em->persist($watchedFeed);
                $em->persist($account);
                $em->flush();
            }
            return true;
        }
        return false;
    }

    /**
     * Rate a feed.
     *
     * @param int $id
     * @param string $rating
     * @return bool|\Feed\Entity\Feed
     */
    public function rate($id, $rating)
    {
        $em = $this->getEntityManager();
        $feed = $this->getFeedRepository()->find($id);
        $account = $this->getAccountPlugin()->getAccount();
        $rating = ($rating == 'thumbUp') ? 1 : 0;
        $rateEntity = $em->getRepository('Feed\Entity\Rating')->findOneBy(array(
                'account' => $account->getAccountId(),
                'feed' => $feed->getFeedId()
            )
        );
        try {
            if ($rateEntity) {
                // check if the rating happens to be the same with the existing one, if so exit
                if ($rateEntity->getRating() != $rating) {
                    $rateEntity->setRating($rating);
                }
                $newRating = ($rating > 0) ? 2 : -2;
            } else {
                $rateEntity = new \Feed\Entity\Rating($account, $feed, $rating);
                $newRating = ($rating > 0) ? 1 : -1;
            }

            $feed->setRating($feed->getRating() + $newRating);
            $em->persist($rateEntity);
            $em->persist($feed);
            $em->flush();

            return $feed;
        } catch (\Exception $e) {
            return false;
        }
    }


    /**
     * Retrieve the account repository
     *
     * @return EntityRepository
     */
    public function getWatchedFeedRepository()
    {
        if (null === $this->watchedFeedRepository)
            $this->watchedFeedRepository = $this->getEntityManager()->getRepository('\Account\Entity\AccountsHistory');
        return $this->watchedFeedRepository;
    }

    /**
     * Retrieve the youtube service.
     *
     * @return \Youtube\Service\Youtube
     */
    public function getYoutubeService()
    {
        if (null === $this->youtubeService)
            $this->youtubeService = $this->getServiceManager()->get('youtube_service');
        return $this->youtubeService;
    }

    /**
     * Retrieve the doctrine entity manager
     *
     * @return \Doctrine\ORM\EntityManager
     */
    public function getEntityManager(){
        if(null === $this->entityManager){
            $this->entityManager = $this->getServiceManager()->get('Doctrine\ORM\EntityManager');
        }
        return $this->entityManager;
    }

    /**
     * Set the doctrine entity manager
     *
     * @param EntityManager $entityManager
     * @return Feed
     */
    public function setEntityManager(EntityManager $entityManager){
        $this->entityManager = $entityManager;
        return $this;
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
     * Get the game repository.
     *
     * @return \Doctrine\ORM\EntityRepository
     */
    public function getGameRepository()
    {
        if (!$this->gameRepository) {
            $this->gameRepository = $this->getEntityManager()->getRepository('Feed\Entity\Game');
        }
        return $this->gameRepository;
    }

    /**
     * Retrieve the account plugin
     *
     * @return \Account\Plugin\ActiveAccount
     */
    public function getAccountPlugin(){
        if(null === $this->accountPlugin)
            $this->accountPlugin = $this->getServiceManager()->get('ControllerPluginManager')->get('account');
        return $this->accountPlugin;
    }

    /**
     * Retrieve the feed repository
     *
     * @return EntityRepository
     */
    public function getFeedRepository(){
        if(null === $this->feedRepository)
            $this->feedRepository = $this->getEntityManager()->getRepository('\Feed\Entity\Feed');
        return $this->feedRepository;
    }
} 