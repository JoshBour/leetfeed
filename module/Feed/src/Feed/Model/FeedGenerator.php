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

    public function getRisingFeeds($requiredFeeds)
    {
        $feedRepository = $this->getFeedRepository();
        $risingFeeds = $feedRepository->findBy(array("isRising" => "1"));
        $feedNum = count($risingFeeds);
        $em = $this->getEntityManager();
        $game = $this->getGameRepository()->find(1);
        $startIndex = 1;
        $count = 0;
        $i = 0;
        if ($feedNum < $requiredFeeds) {
            do {
                $videoFeed = $this->getFeedsByQuery(50, "this_week", $startIndex);
                foreach ($videoFeed as $video) {
                    if ($feed = $feedRepository->findOneBy(array("videoId" => $video->getVideoId()))) {
                        if ($feed->getIsRising() == 1) continue;
                        $feed->setIsRising(1);
                    } else {
                        $entry = new YoutubeEntry($video);
                        $feed = \Feed\Entity\Feed::create($game,
                            $entry->getVideoId(),
                            $entry->getTitle(),
                            $entry->getAuthor(),
                            $entry->getDescription(), 1, 0);
                    }
                    $em->persist($feed);
                    $risingFeeds[] = $feed;
                    $count++;
                }
                $startIndex+=50;
            } while ($count < $requiredFeeds - $feedNum);
        }
        return $risingFeeds;
    }

    public function getRelatedFeeds($url, $game = 1)
    {
        $em = $this->getEntityManager();
        $yt = $this->getYoutubeService()->getYoutubeInstance();
        $feedRepository = $this->getFeedRepository();
        $game = $this->getGameRepository()->find(1);
        $videoFeeds = $yt->getRelatedVideoFeed($url);
        $feedList = array();
        $flush = false;
        foreach ($videoFeeds as $video) {
            $entry = new YoutubeEntry($video);
            if (!$feed = $feedRepository->findOneBy(array("videoId" => $entry->getVideoId()))) {
                $feed = \Feed\Entity\Feed::create($game,
                    $entry->getVideoId(),
                    $entry->getTitle(),
                    $entry->getAuthor(),
                    $entry->getDescription(), 1, 0);
                $isPersisted = \Doctrine\ORM\UnitOfWork::STATE_MANAGED === $em->getUnitOfWork()->getEntityState($feed);
                if (!$isPersisted) $em->persist($feed);
                $flush = true;
            }
            $feedList[] = $feed;
        }
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
        do {
            $randFeed = $feeds[rand(0, count($feeds) - 1)];
        } while ($randFeed == null);

        $entry = new YoutubeEntry($randFeed);
        $checkedFeeds = array();
        // while the account has seen the feed, search for an other one
        while ($account->hasWatched($entry)) {
            if (count($checkedFeeds) == count($feeds)) {
                $checkedFeeds = array();
                $feeds = $this->getRandomFeedList();
            }
            if (!in_array($entry, $checkedFeeds)) $checkedFeeds[] = $entry;
            $entry = new YoutubeEntry($feeds[rand(0, count($feeds) - 1)]);
        }

        // save the feed to accounts feeds
        $added = false;
        if(!$feed = $feedRepository->findOneBy(array("videoId"=>$entry->getVideoId()))){
            $feed = \Feed\Entity\Feed::create($game, $entry->getVideoId(), $entry->getTitle(), $entry->getAuthor(), $entry->getDescription());
            $added = true;
            $em->persist($feed);
        }
        $account->addFeeds($feed);
        $em->persist($account);
        $em->flush();
        return $added ? $feedRepository->findOneBy(array("videoId"=>$entry->getVideoId())) : $feed;
    }

    private function getRandomFeedList()
    {
        if (rand(1, 10) < 5) {
            $youtubers = $this->getYoutuberRepository()->findAll();
            $youtuber = $youtubers[rand(0, count($youtubers) - 1)];
            $feeds = $this->getFeedsByYoutuber($youtuber);
        } else {
            $this->startIndex += 50;
            $feeds = $this->getFeedsByQuery();
        }
        return $feeds;
    }


    /**
     * @param int $maxResults
     * @param string $time
     * @param int $index
     * @return \ZendGData\YouTube\VideoFeed
     */
    private function getFeedsByQuery($maxResults = 50, $time = "this_month", $index = 1)
    {
        $yt = $this->getYoutubeService()->getYoutubeInstance();
        $query = $yt->newVideoQuery();
        $query->setOrderBy('viewCount')
            ->setStartIndex($index)
            ->setTime($time)
            ->setMaxResults($maxResults)
            ->setVideoQuery("League of legends game");
        return $yt->getVideoFeed($query);
    }

    /**
     * @param $youtuber
     * @return \ZendGData\YouTube\VideoFeed
     */
    private function getFeedsByYoutuber($youtuber)
    {
        $yt = $this->getYoutubeService()->getYoutubeInstance();
        return $yt->getUserUploads($youtuber->getName());
    }

    /**
     * Retrieve the youtube service.
     *
     * @return \Feed\Service\Youtube
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
            $this->account = $this->getServiceManager()->get('account_service')->getActiveAccount();
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