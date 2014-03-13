<?php
namespace Feed\Model;

use ZendGData\YouTube\VideoEntry;

class YoutubeEntry
{
    /**
     * @var string
     */
    private $title;

    /**
     * @var null|string
     */
    private $uri;

    /**
     * @var string
     */
    private $author;

    /**
     * @var null|string
     */
    private $description;

    /**
     * @var string
     */
    private $thumbnail;

    /**
     * @var array
     */
    private $tags;

    /**
     * @var string
     */
    private $videoId;

    /**
     * @var null|string
     */
    private $duration;

    /**
     * @var Rating
     */
    private $rating;

    /**
     * @var string
     */
    private $views;

    public function __construct($entry)
    {
        $snippet = $entry['snippet'];

        $this->title = $snippet['title'];
        $this->uri = $entry->getVideoWatchPageUrl();
        $this->description = $snippet['description'];
        $this->duration = $entry->getVideoDuration();
        $this->videoId = $entry['id'];
        $this->tags = $snippet['tags'];
        $this->author = $snippet['channelId'];
        $this->thumbnail = array(
            "default" => $snippet['thumbnails']['default'],
            "medium" => $snippet['thumbnails']['medium'],
            "high" => $snippet['thumbnails']['high'],
        );
        $this->rating = array(
            "likes" => $entry['statistics']['likeCount'],
            "dislikes" => $entry['statistics']['dislikeCount']
        );
        $this->views = $entry['statistics']['viewCount'];

    }

    public function getScore()
    {
        $x = ($this->getTotalRating() !== null && $this->getTotalRating() > 0) ? $this->getTotalRating() : 1;
        return log10($x + ($this->views / 2));
    }

    public function getTotalRating()
    {
        return (null !== $this->rating) ? $this->rating->getNumRaters() : null;
    }

    /**
     * @param string $author
     * @return YoutubeEntry
     */
    public function setAuthor($author)
    {
        $this->author = $author;
        return $this;
    }

    /**
     * @return string
     */
    public function getAuthor()
    {
        return $this->author;
    }

    /**
     * @param null|string $description
     * @return YoutubeEntry
     */
    public function setDescription($description)
    {
        $this->description = $description;
        return $this;
    }

    /**
     * @return null|string
     */
    public function getDescription()
    {
        return $this->description;
    }

    /**
     * @param null|string $duration
     * @return YoutubeEntry
     */
    public function setDuration($duration)
    {
        $this->duration = $duration;
        return $this;
    }

    /**
     * @return null|string
     */
    public function getDuration()
    {
        return $this->duration;
    }

    /**
     * @param \ZendGData\Extension\Rating $rating
     * @return YoutubeEntry
     */
    public function setRating($rating)
    {
        $this->rating = $rating;
        return $this;
    }

    /**
     * @return \ZendGData\Extension\Rating
     */
    public function getRating()
    {
        return $this->rating;
    }

    /**
     * @param array $tags
     * @return YoutubeEntry
     */
    public function setTags($tags)
    {
        $this->tags = $tags;
        return $this;
    }

    /**
     * @return array
     */
    public function getTags()
    {
        return $this->tags;
    }

    /**
     * @param string $thumbnail
     * @return YoutubeEntry
     */
    public function setThumbnail($thumbnail)
    {
        $this->thumbnail = $thumbnail;
        return $this;
    }

    /**
     * @return string
     */
    public function getThumbnail()
    {
        return $this->thumbnail;
    }

    /**
     * @param string $title
     * @return YoutubeEntry
     */
    public function setTitle($title)
    {
        $this->title = $title;
        return $this;
    }

    /**
     * @return string
     */
    public function getTitle()
    {
        return $this->title;
    }

    /**
     * @param null|string $uri
     * @return YoutubeEntry
     */
    public function setUri($uri)
    {
        $this->uri = $uri;
        return $this;
    }

    /**
     * @return null|string
     */
    public function getUri()
    {
        return $this->uri;
    }

    /**
     * @param string $videoId
     * @return YoutubeEntry
     */
    public function setVideoId($videoId)
    {
        $this->videoId = $videoId;
        return $this;
    }

    /**
     * @return string
     */
    public function getVideoId()
    {
        return $this->videoId;
    }

    /**
     * @param string $views
     * @return YoutubeEntry
     */
    public function setViews($views)
    {
        $this->views = $views;
        return $this;
    }

    /**
     * @return string
     */
    public function getViews()
    {
        return $this->views;
    }


}