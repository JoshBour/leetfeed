<?php
/**
 * Created by PhpStorm.
 * User: Josh
 * Date: 27/2/2014
 * Time: 4:46 μμ
 */

namespace Feed\Service;


use Doctrine\ORM\EntityManager;
use Doctrine\ORM\EntityRepository;
use Zend\ServiceManager\ServiceManager;
use Zend\ServiceManager\ServiceManagerAwareInterface;

class Feed implements ServiceManagerAwareInterface
{

    /**
     * var EntityManager
     */
    private $entityManager;

    /**
     * @var ServiceManager
     */
    private $serviceManager;

    /**
     * @var \Account\Entity\Account
     */
    private $account;

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

    /**
     * @var \League\Service\League
     */
    private $leagueService;

    /**
     * @var EntityRepository
     */
    private $championRepository;

    public function add($data)
    {
        $youtubeService = $this->getYoutubeService();
        $em = $this->getEntityManager();
        parse_str(parse_url($data["feed"]["url"], PHP_URL_QUERY), $vars);
        $video = $youtubeService->findVideoById($vars['v']);
        $title = $data["feed"]["title"] ? $data["feed"]["title"] : $video->getTitle();
        $feed = $this->createFromEntry($video, 0, false);
        $feed->setTitle($title);
        try {
            $em->persist($feed);
            $em->flush();
        } catch (\Exception $e) {
            return false;
        }
        return $feed;
    }

    /**
     * @param $summoner
     * @param $page
     * @return array
     */
    public function getSummonerFeeds(&$summoner, $page = 0)
    {
        $leagueService = $this->getLeagueService();
        $youtubeService = $this->getYoutubeService();
        $championRepository = $this->getChampionRepository();
        $em = $this->getEntityManager();
        $champions = array();
        $feeds = array();

        $champions = $leagueService->getLatestChampions($summoner);
        // we calculate how many feeds we need per champion to have 50 as total
        $championCount = count($champions);
        if ($championCount > 0) {
            $requiredFeedPerChampion = intval(floor(100 / $championCount));
            foreach ($champions as $champion) { // we iterate through the champions
                // first we get the feeds for the specific champion from the db
                $championEntity = $championRepository->find($champion);
                $championFeeds = $championEntity->getFeeds();
                $championFeedsCount = $championFeeds->count();
                if ($championFeedsCount < $requiredFeedPerChampion) { // we check if they are enough
                    $videos = $youtubeService->findByQuery("league of legends " . $champion . ' gameplay', null, intval($requiredFeedPerChampion - $championFeedsCount), "this_year");
                    foreach ($videos as $video) {
                        $feed = $this->createFromEntry($video, 1, false);
                        $isPersisted = \Doctrine\ORM\UnitOfWork::STATE_MANAGED === $em->getUnitOfWork()->getEntityState($feed);
                        if (!$championFeeds->contains($feed)) {
                            # $championFeeds[] = $feed;
                            $championEntity->addFeeds($feed);
                        }
                    }
                } else {
                    if ($championFeeds->count() > $page * $requiredFeedPerChampion) {
                        $championFeeds = array_slice($championFeeds->toArray(), $page * $requiredFeedPerChampion, $requiredFeedPerChampion);
                    } else {
                        $championFeeds = array_slice($championFeeds->toArray(), 0, $requiredFeedPerChampion);
                    }
                }
                foreach ($championFeeds as $feed) {
                    $feeds[] = $feed;
                }
                $em->persist($championEntity);

            }
            $em->flush();

            shuffle($feeds);
        }
        return $feeds;
    }

    public function getYoutuberUploads($youtuber, $nextToken = null)
    {
        # $feeds = $youtuber->getFeeds();
        $channel = $this->getYoutubeService()->findChannelByUsername($youtuber->getName());
        $feedRepository = $this->getFeedRepository();
        $game = $this->getGameRepository()->find(1);
        $em = $this->getEntityManager();
        $flush = false;
        $persistYoutuber = false;
        $uploadsPlaylist = $channel->getUploadsPlaylist(50, $nextToken);
        $uploads = $uploadsPlaylist->getVideos();
        #  if (count($feeds) != count($uploads)) {
        $feeds = array();
        /**
         * @var $video \Youtube\Model\Video
         */
        foreach ($uploads as $video) {
            if (!$feed = $feedRepository->findOneBy(array("videoId" => $video->getId()))) {
                $feed = \Feed\Entity\Feed::create($game,
                    $video->getId(),
                    $video->getTitle(),
                    $channel->getTitle(),
                    $video->getDescription(), 1, 0);
                $isPersisted = \Doctrine\ORM\UnitOfWork::STATE_MANAGED === $em->getUnitOfWork()->getEntityState($feed);
                if (!$isPersisted) $em->persist($feed);
                $flush = true;
            }
            if (!$youtuber->hasFeed($feed)) {
                $feeds[] = $feed;
                #  $youtuber->addFeeds($feed);
                #   $persistYoutuber = true;
            }
        }
        #   if ($persistYoutuber) $em->persist($youtuber);
        if ($flush) $em->flush();
        # }
        return array("feeds" => $feeds,
            "nextToken" => $uploadsPlaylist->getNextPageToken());
    }

    /**
     * @param \Youtube\Model\Video $video
     * @param int $related
     * @param bool $flush
     * @param bool $persist
     * @return \Feed\Entity\Feed
     */
    public function createFromEntry($video, $related = 1, $flush = true, $persist = true)
    {
        $game = $this->getGameRepository()->find(1);
        $feedRepository = $this->getFeedRepository();
        if (!$feed = $feedRepository->findOneBy(array("videoId" => $video->getId()))) {
         #   $feed = \Feed\Entity\Feed::create($game, $video->getId(), $video->getTitle(), $video->getChannel()->getTitle(), $video->getDescription(), $related);
            if ($flush || $persist) {
                $em = $this->getEntityManager();
                $isPersisted = \Doctrine\ORM\UnitOfWork::STATE_MANAGED === $em->getUnitOfWork()->getEntityState($feed);
                if (!$isPersisted && $persist) $em->persist($feed);
                if ($flush) $em->flush();
            }
        }
        return $feed;
    }

    /**
     * @param int $feedId
     * @return bool
     */
    public function addFeedToWatched($feedId)
    {
        $feed = $this->getFeedRepository()->find($feedId);
        if ($feed) {
            $em = $this->getEntityManager();
            $account = $this->getAccount();
            if (!$this->getWatchedFeedRepository()->findOneBy(array("feed" => $feed, "account" => $account))) {
                $watchedFeed = \Account\Entity\AccountsHistory::create($account, $feed);
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
        $account = $this->getAccount();
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
     * @param \League\Entity\Champion $champion
     * @param bool $flush
     * @return array
     */
    public function getLolProFeeds($champions = null, $flush = true)
    {
        $pattern = '/\s*[a-zA-Z]*\s*\-\s*([a-zA-Z\s]*)/';
        $channel = $this->getYoutubeService()->findChannelByUsername("TheFMGreen");
        $playlist = $channel->getUploadsPlaylist(50, "CIQHEAA");
        $newFeeds = array();
        $index = 50;
        $count = intval($playlist->getTotalResults() / 3);
        while ($index < $count) {
            foreach ($playlist->getVideos() as $video) {
                preg_match($pattern, $video->getTitle(), $result);
                if (!empty($result)) {
                    $name = explode('vs', join('', explode(' ', $result[1])));
                    $name = explode('support', $name[0]);
                    $name = explode('Bruiser', $name[0]);
                    if (isset($name[0])) {
                        echo $name[0] . '<br />';
                        $feed = $this->createFromEntry($video, 1, false);
                        $newFeeds[$name[0]][] = $feed;
                    }
                }
            }
            $index += 50;
            $playlist = $channel->getUploadsPlaylist(50, $playlist->getNextPageToken());
            echo $playlist->getNextPageToken() . "<br />";
        }
        foreach ($newFeeds as $name => $feed) {
            $champion = $this->getChampionRepository()->find($name);
            if ($champion) {
                foreach ($feed as $inner) {
                    if (!$champion->getFeeds()->contains($inner)) {
                        $champion->addFeeds($inner);
                    }
                }
                $this->getEntityManager()->persist($champion);
            }
        }
        if ($flush) $this->getEntityManager()->flush();
        return $newFeeds;
    }

    /**
     * @param string $name
     * @param bool $createFeeds
     * @param bool $forceFlush
     * @return array
     */
    private function getFeedsByYoutuberName($name, $createFeeds = true, $forceFlush = true)
    {
        $channel = $this->getYoutubeService()->findChannelByUsername($name);
        $em = $this->getEntityManager();
        $uploads = $channel->getUploads();
        if ($createFeeds) {
            $feeds = array();
            /**
             * @var $video \Youtube\Model\Video
             */
            foreach ($uploads as $video) {
                $feeds[] = $this->createFromEntry($video, 1, false);
            }
            if ($forceFlush) $em->flush();
            return $feeds;
        } else {
            return $uploads;
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
     * Retrieve the League service.
     *
     * @return \League\Service\League
     */
    public function getLeagueService()
    {
        if (null === $this->leagueService)
            $this->leagueService = $this->getServiceManager()->get('league_service');
        return $this->leagueService;
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
     * @return Feed
     */
    public function setEntityManager(EntityManager $entityManager)
    {
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
    public function getChampionRepository()
    {
        if (!$this->championRepository) {
            $this->championRepository = $this->getEntityManager()->getRepository('League\Entity\Champion');
        }
        return $this->championRepository;
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
     * Retrieve the active account
     *
     * @return \Account\Entity\Account
     */
    public function getAccount()
    {
        if (null === $this->account)
            $this->account = $this->getServiceManager()->get('ControllerPluginManager')->get('account')->getAccount();
        return $this->account;
    }

    /**
     * Retrieve the feed repository
     *
     * @return EntityRepository
     */
    public function getFeedRepository()
    {
        if (null === $this->feedRepository)
            $this->feedRepository = $this->getEntityManager()->getRepository('\Feed\Entity\Feed');
        return $this->feedRepository;
    }
} 