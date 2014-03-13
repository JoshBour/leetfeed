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


    public function saveFeedList($feedList, $limit = null, $flush = false){
        $count = 0;
        $game = $this->getGameRepository()->find(1);
        $em = $this->getEntityManager();
        $relatedFeeds = array();
        foreach ($feedList as $ytFeed) {
            if(!is_null($limit) && $count > $limit) break;
            $entry = new \Feed\Model\YoutubeEntry($ytFeed);
            $feed = \Feed\Entity\Feed::create($game,
                $entry->getVideoId(),
                $entry->getTitle(),
                $entry->getAuthor(),
                $entry->getDescription(),1);
            $relatedFeeds[] = $feed;
            $em->persist($feed);
            $count++;
        }
        if($flush) $em->flush();
        return $relatedFeeds;
    }

    public function createFromEntry($entry,$related = 0, $flush = true){
        $game = $this->getGameRepository()->find(1);
        // save the feed to accounts feeds
        $feed = new \Feed\Model\YoutubeEntry($entry);
        $accountFeed = \Feed\Entity\Feed::create($game, $feed->getVideoId(), $feed->getTitle(), $feed->getAuthor(), $feed->getDescription(),$related);
        $this->getEntityManager()->persist($accountFeed);
        return $accountFeed;
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
     * @return Account
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
     * @return Account
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
            $this->accountPlugin = $this->getServiceManager()->get('ControllerPluginManager')->get('user');
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