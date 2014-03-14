<?php
/**
 * User: Josh
 * Date: 19/11/2013
 * Time: 5:39 μμ
 */

namespace Feed\Model;


use Account\Entity\Account;
use Doctrine\ORM\EntityManager;
use Zend\ServiceManager\ServiceManager;
use Zend\ServiceManager\ServiceManagerAwareInterface;

class FeedGenerator implements ServiceManagerAwareInterface
{

    private $entityManager;

    private $serviceManager;

    private $account;

    private $feedRepository;

    private $feedService;

    private $gameRepository;

    private $accountRepository;

    private $youtuberRepository;

    private $youtubeService;

    private $startIndex;

    public function __construct()
    {
        $this->startIndex = -49;
    }

//    public function getRisingFeeds($requiredFeeds)
//    {
//        $feedRepository = $this->getFeedRepository();
//        $risingFeeds = $feedRepository->findBy(array("isRising" => "1"));
//        $feedNum = count($risingFeeds);
//        $em = $this->getEntityManager();
//        $game = $this->getGameRepository()->find(1);
//        $startIndex = 1;
//        $count = 0;
//        $i = 0;
//        if ($feedNum < $requiredFeeds) {
//            do {
//                $videoFeed = $this->getFeedsByQuery(50, "this_week", $startIndex);
//                foreach ($videoFeed as $video) {
//                    if ($feed = $feedRepository->findOneBy(array("videoId" => $video->getVideoId()))) {
//                        if ($feed->getIsRising() == 1) continue;
//                        $feed->setIsRising(1);
//                    } else {
//                        $entry = new YoutubeEntry($video);
//                        $feed = \Feed\Entity\Feed::create($game,
//                            $entry->getVideoId(),
//                            $entry->getTitle(),
//                            $entry->getAuthor(),
//                            $entry->getDescription(), 1, 0);
//                    }
//                    $em->persist($feed);
//                    $risingFeeds[] = $feed;
//                    $count++;
//                }
//                $startIndex+=50;
//            } while ($count < $requiredFeeds - $feedNum);
//        }
//        return $risingFeeds;
//    }

    public function getRelatedFeeds($feed, $game = 1)
    {
        $em = $this->getEntityManager();
        $yt = $this->getYoutubeService();
        $feedRepository = $this->getFeedRepository();
        $game = $this->getGameRepository()->find($game);
        $videoFeeds = $yt->findRelatedToId($feed->getVideoId());
        $feedList = array();
        $flush = false;
        /**
         * @var $video \Youtube\Model\Video
         */
        foreach ($videoFeeds as $video) {
            if (!$newFeed = $feedRepository->findOneBy(array("videoId" => $video->getId()))) {
                $newFeed = \Feed\Entity\Feed::create($game,
                    $video->getId(),
                    $video->getTitle(),
                    $video->getChannel()->getTitle(),
                    $video->getDescription(), 1, 0);
                $isPersisted = \Doctrine\ORM\UnitOfWork::STATE_MANAGED === $em->getUnitOfWork()->getEntityState($newFeed);
                if (!$isPersisted) $em->persist($newFeed);
                $flush = true;
            }
            $feedList[] = $newFeed;
        }
        $feed->addRelatedFeeds($feedList);
        $em->persist($feed);
        if ($flush) $em->flush();
        return $feedList;
    }

    public function getRandomFeed($gameId = 1)
    {
        $account = $this->getAccount();
        $em = $this->getEntityManager();
        // get a random video feed list
        $feeds = $this->getRandomFeedList();
        $feedRepository = $this->getFeedRepository();
        $game = $this->getGameRepository()->find($gameId);

        // select a random one from the above list
        /**
         * @var $randFeed \Youtube\Model\Video
         */
        $randFeed = $feeds[rand(0, count($feeds) - 1)];
        $checkedFeeds = array();
        // while the account has seen the feed, search for an other one
        if($account){
            while ($account->hasWatched($randFeed)) {
                if (count($checkedFeeds) == count($feeds)) {
                    $checkedFeeds = array();
                    $feeds = $this->getRandomFeedList();
                }
                if (!in_array($randFeed, $checkedFeeds)) $checkedFeeds[] = $randFeed;
                $randFeed = $feeds[rand(0, count($feeds) - 1)];
            }
        }

        // save the feed to accounts feeds
        $added = false;
        if(!$feed = $feedRepository->findOneBy(array("videoId"=>$randFeed->getId()))){
            $feed = \Feed\Entity\Feed::create($game, $randFeed->getId(), $randFeed->getTitle(), $randFeed->getChannel()->getTitle(), $randFeed->getDescription());
            $added = true;
            $em->persist($feed);
        }
        if($account){
            $this->getFeedService()->addFeedToWatched($feed->getFeedId());
//            $watchedFeed = \Account\Entity\AccountsHistory::create($account,$feed);
//            $account->addFeeds($watchedFeed);
//            $em->persist($watchedFeed);
//            $em->persist($account);
        }
        $em->flush();
        return $added ? $feedRepository->findOneBy(array("videoId"=>$randFeed->getId())) : $feed;
    }

    private function getRandomFeedList()
    {
        if (rand(1, 10) < 5) {
            $youtubers = $this->getYoutuberRepository()->findAll();
            $youtuber = $youtubers[rand(0, count($youtubers) - 1)];
            $feeds = $this->getYoutubeService()->findChannelByUsername($youtuber->getName())->getUploads();
        } else {
            $this->startIndex += 50;
            $feeds = $this->getYoutubeService()->findByQuery("League of legends game");
        }
        return $feeds;
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
     * Retrieve the feed service.
     *
     * @return \Feed\Service\Feed
     */
    public function getFeedService()
    {
        if (null === $this->feedService)
            $this->feedService = $this->getServiceManager()->get('feed_service');
        return $this->feedService;
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
     * Set the doctrine entity manager
     *
     * @param EntityManager $entityManager
     * @return FeedGenerator
     */
    public function setEntityManager(EntityManager $entityManager)
    {
        $this->entityManager = $entityManager;
        return $this;
    }

    /**
     * @return Account
     */
    public function getAccount()
    {
        if (null === $this->account) {
            $this->account = $this->getServiceManager()->get('ControllerPluginManager')->get('account')->getAccount();
        }
        return $this->account;
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
     * @return FeedGenerator
     */
    public function setServiceManager(ServiceManager $serviceManager)
    {
        $this->serviceManager = $serviceManager;
        return $this;
    }

    /**
     * Get the account repository.
     *
     * @return \Doctrine\ORM\EntityRepository
     */
    public function getAccountRepository()
    {
        if (!$this->accountRepository) {
            $this->accountRepository = $this->getEntityManager()->getRepository('Account\Entity\Account');
        }
        return $this->accountRepository;
    }

    /**
     * Get the account repository.
     *
     * @return \Doctrine\ORM\EntityRepository
     */
    public function getYoutuberRepository()
    {
        if (!$this->youtuberRepository) {
            $this->youtuberRepository = $this->getEntityManager()->getRepository('Feed\Entity\Youtuber');
        }
        return $this->youtuberRepository;
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
     * Get the account repository.
     *
     * @return \Doctrine\ORM\EntityRepository
     */
    public function getFeedRepository()
    {
        if (!$this->feedRepository) {
            $this->feedRepository = $this->getEntityManager()->getRepository('Feed\Entity\Feed');
        }
        return $this->feedRepository;
    }

}