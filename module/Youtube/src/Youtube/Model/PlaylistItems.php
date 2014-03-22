<?php
/**
 * Created by PhpStorm.
 * User: Josh
 * Date: 19/3/2014
 * Time: 4:33 μμ
 */

namespace Youtube\Model;


class PlaylistItems
{

    private $nextPageToken;

    private $previousPageToken;

    private $totalResults;

    private $videos;

    public function __construct($playlistItems)
    {
        $this->nextPageToken = isset($playlistItems["nextPageToken"]) ? $playlistItems["nextPageToken"] : null;
        $this->previousPageToken = isset($playlistItems["previousPageToken"]) ? $playlistItems["previousPageToken"] : null;
        $this->totalResults = $playlistItems["pageInfo"]["totalResults"];
        foreach ($playlistItems["items"] as $item)
            $this->videos[] = new Video($item, "playlistItem");
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
     * @param mixed $totalResults
     */
    public function setTotalResults($totalResults)
    {
        $this->totalResults = $totalResults;
    }

    /**
     * @return mixed
     */
    public function getTotalResults()
    {
        return $this->totalResults;
    }

    /**
     * @param mixed $videos
     */
    public function setVideos($videos)
    {
        $this->videos = $videos;
    }

    /**
     * @return mixed
     */
    public function getVideos()
    {
        return $this->videos;
    }


} 