<?php
/**
 * Created by PhpStorm.
 * User: Josh
 * Date: 12/3/2014
 * Time: 6:51 μμ
 */

namespace Feed\Model;

use Zend\ServiceManager\ServiceManagerAwareInterface;
use Zend\ServiceManager\ServiceManager;
use Doctrine\ORM\EntityManager;
use Feed\Model\YoutubeEntry;
use Doctrine\ORM\EntityRepository;

class Youtube implements ServiceManagerAwareInterface
{
    private $youtube;

    private $serviceManager;

    private $entityManager;

    public function __construct()
    {
        $client = new \Google_Client();
        $client->setApplicationName("Leetfeed");
        $client->setDeveloperKey($this->getServiceManager()->get('Config')["youtube"]["api_key"]);
        $this->youtube = new \Google_Service_YouTube($client);
    }

    public function findVideoById($id){
        $videoList = $this->youtube->videos->listVideos("id,snippet,statistics",array(
         "id" => $id
        ));
        $video = $videoList['items'][0];
        return new YoutubeEntry($video);
    }

    public function findChannelByUsername($username){
        $channelList = $this->youtube->channels->listChannels("id,snippet",array(
            "forUsername" => $username
        ));
        return $channelList['items'][0];

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
} 