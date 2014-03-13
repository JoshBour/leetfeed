<?php
/**
 * Created by PhpStorm.
 * User: Josh
 * Date: 12/3/2014
 * Time: 7:39 μμ
 */

namespace Youtube\Model;


class Channel {
    private $id;

    private $title;

    private $description;

    private $thumbnails;

    private $viewCount;

    private $subscriberCount;

    private $uploads;

    private $uploadsPlaylistId;

    private $videoCount;

    public function __construct($channel){
        $this->id = $channel["id"];
        $this->title = $channel["snippet"]["title"];
        $this->description = $channel["snippet"]["description"];
        $this->thumbnails = array(
            "default" => $channel['snippet']['thumbnails']['default']['url'],
            "medium" => $channel['snippet']['thumbnails']['medium']['url'],
            "high" => $channel['snippet']['thumbnails']['high']['url'],
        );
        $this->uploadsPlaylistId = $channel["contentDetails"]["relatedPlaylists"]["uploads"];
        $this->viewCount = $channel["statistics"]["viewCount"];
        $this->subscriberCount = $channel["statistics"]["subscriberCount"];
        $this->videoCount = $channel["statistics"]["videoCount"];
    }

    /**
     * @param string $description
     */
    public function setDescription($description)
    {
        $this->description = $description;
    }

    /**
     * @return string
     */
    public function getDescription()
    {
        return $this->description;
    }

    /**
     * @param string $id
     */
    public function setId($id)
    {
        $this->id = $id;
    }

    /**
     * @return string
     */
    public function getId()
    {
        return $this->id;
    }

    /**
     * @param int $subscriberCount
     */
    public function setSubscriberCount($subscriberCount)
    {
        $this->subscriberCount = $subscriberCount;
    }

    /**
     * @return int
     */
    public function getSubscriberCount()
    {
        return $this->subscriberCount;
    }

    /**
     * @param array $thumbnails
     */
    public function setThumbnails($thumbnails)
    {
        $this->thumbnails = $thumbnails;
    }

    /**
     * @return array
     */
    public function getThumbnails()
    {
        return $this->thumbnails;
    }

    /**
     * @param string $title
     */
    public function setTitle($title)
    {
        $this->title = $title;
    }

    /**
     * @return string
     */
    public function getTitle()
    {
        return $this->title;
    }

    /**
     * @return array
     */
    public function getUploads(){
        if(null === $this->uploads){
            $yt = new \Youtube\Service\Youtube();
            $this->uploads = $yt->findPlaylistById($this->uploadsPlaylistId);
        }
        return $this->uploads;
    }

    /**
     * @param mixed $uploadsPlaylistId
     */
    public function setUploadsPlaylistId($uploadsPlaylistId)
    {
        $this->uploadsPlaylistId = $uploadsPlaylistId;
    }

    /**
     * @return mixed
     */
    public function getUploadsPlaylistId()
    {
        return $this->uploadsPlaylistId;
    }

    /**
     * @param int $videoCount
     */
    public function setVideoCount($videoCount)
    {
        $this->videoCount = $videoCount;
    }

    /**
     * @return int
     */
    public function getVideoCount()
    {
        return $this->videoCount;
    }

    /**
     * @param int $viewCount
     */
    public function setViewCount($viewCount)
    {
        $this->viewCount = $viewCount;
    }

    /**
     * @return int
     */
    public function getViewCount()
    {
        return $this->viewCount;
    }


} 