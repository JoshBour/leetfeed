<?php
/**
 * Created by PhpStorm.
 * User: Josh
 * Date: 12/3/2014
 * Time: 6:51 μμ
 */

namespace Youtube\Service;

use Youtube\Model\Channel;
use Youtube\Model\Video;
use Youtube\Model\PlaylistItems;
use Zend\ServiceManager\ServiceManager;
use Zend\ServiceManager\ServiceManagerAwareInterface;

class Youtube implements ServiceManagerAwareInterface
{
    /**
     * @var \Google_Service_YouTube
     */
    private $youtube;

    /**
     * @var ServiceManager
     */
    private $serviceManager;

    public function __construct()
    {
        $client = new \Google_Client();
        $client->setApplicationName("Leetfeed");
        $client->setDeveloperKey("AIzaSyBSvLVzCvRyhf4wXYwFo0Otudl86GBeyFM");
        $this->youtube = new \Google_Service_YouTube($client);
    }

    public function findByQuery($query,$extraParams = null,$maxResults = 50, $time = "this_month", $nextPageToken = "")
    {
        if ($time == "this_month") {
            $datetime = new \DateTime("-1 month");
        } else if ($time == "this_week") {
            $datetime = new \DateTime("-1 year");
        }else if($time == "this_year"){
            $datetime = new \DateTime("-1 week");
        }
        if($maxResults>50)$maxResults = 5;
        $time = $datetime->format(\DateTime::RFC3339);
        $options = array(
            "type" => "video",
            "maxResults" => $maxResults,
            "publishedAfter" => $time,
        );
        if($query != null) $options["q"] = $query;
        if($extraParams) $options = array_merge($options,$extraParams);
        if ($nextPageToken) $options["pageToken"] = $nextPageToken;
        $response = $this->youtube->search->listSearch("snippet", $options);
        $videos = array();
        foreach ($response["items"] as $result)
            $videos[] = new Video($result, "searchItem");
        return $videos;
    }

    public function findRelatedToId($id)
    {
        return $this->findByQuery(null,array("relatedToVideoId"=>$id));
    }

    public function findVideoById($id)
    {
        $videoList = $this->youtube->videos->listVideos("id,snippet,statistics,contentDetails", array(
            "id" => $id,
        ));
        return new Video($videoList['items'][0]);
    }

    public function findPlaylistById($id,$maxResults = 50,$pageToken = null){
        $params = array(
            'playlistId' => $id,
            'maxResults' => $maxResults
        );
        if($pageToken) $params['pageToken'] = $pageToken;
        $playlistItems = $this->youtube->playlistItems->listPlaylistItems('snippet',$params);
        return new PlaylistItems($playlistItems);
    }

    public function findChannelById($id)
    {
        $channelList = $this->youtube->channels->listChannels("id,snippet,statistics,contentDetails", array(
            "id" => $id,
        ));
        return new Channel($channelList['items'][0]);
    }

    public function findChannelByUsername($username)
    {
        $channelList = $this->youtube->channels->listChannels("id,snippet,statistics,contentDetails", array(
            "forUsername" => $username,
        ));
        return new Channel($channelList['items'][0]);

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
     * @return Youtube
     */
    public function setServiceManager(ServiceManager $serviceManager)
    {
        $this->serviceManager = $serviceManager;
        return $this;
    }
} 