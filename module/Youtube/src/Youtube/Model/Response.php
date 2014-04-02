<?php
/**
 * Created by PhpStorm.
 * User: Josh
 * Date: 29/3/2014
 * Time: 7:28 μμ
 */

namespace Youtube\Model;


class Response
{
    private $nextPageToken;

    private $previousPageToken;

    private $videos;

    public function __construct($response)
    {
        $this->nextPageToken = isset($response["nextPageToken"]) ? $response["nextPageToken"] : null;
        $this->previousPageToken = isset($response["previousPageToken"]) ? $response["previousPageToken"] : null;
        foreach ($response["items"] as $result)
            $this->videos[] = new Video($result, "searchItem");
    }

    /**
     * @param mixed $nextPageToken
     */
    public function setNextPageToken($nextPageToken)
    {
        $this->nextPageToken = $nextPageToken;
    }

    /**
     * @return mixed
     */
    public function getNextPageToken()
    {
        return $this->nextPageToken;
    }

    /**
     * @param mixed $previousPageToken
     */
    public function setPreviousPageToken($previousPageToken)
    {
        $this->previousPageToken = $previousPageToken;
    }

    /**
     * @return mixed
     */
    public function getPreviousPageToken()
    {
        return $this->previousPageToken;
    }

    /**
     * @param mixed $videos
     */
    public function setVideos($videos)
    {
        $this->videos = $videos;
    }

    /**
     * @param null $full
     * @return array
     */
    public function getVideos($full = false)
    {
        if ($full) {
            $yt = new \Youtube\Service\Youtube();
            $videos = array();
            foreach ($this->videos as $video) {
                $videos[] = $yt->findVideoById($video->getId());
            }
            return $videos;
        } else {
            return $this->videos;
        }
    }


} 