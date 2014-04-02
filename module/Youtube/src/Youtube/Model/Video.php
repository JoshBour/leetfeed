<?php
namespace Youtube\Model;


class Video
{
    private $id;

    private $uri;

    private $title;

    private $description;

    private $channel;

    private $channelId;

    private $thumbnail;

    private $tags;

    private $duration;

    private $rating;

    private $views;

    private $relatedVideos;

    public function __construct($video, $type = "normal")
    {
        if ($type == "normal") {
            $this->id = $video["id"];
            $this->views = $video['statistics']['viewCount'];
            $this->rating = array(
                "likes" => $video['statistics']['likeCount'],
                "dislikes" => $video['statistics']['dislikeCount']
            );
            $this->duration = $video['contentDetails']['duration'];
        } else {
            $this->id = ($type == "searchItem") ? $video["id"]["videoId"] : $video["snippet"]["resourceId"]["videoId"];
        }

        $this->title = $video['snippet']['title'];
        $this->description = $video['snippet']['description'];
        $this->tags = isset($video['snippet']['tags']) ? $video['snippet']['tags'] : null;
        $this->channelId = $video["snippet"]["channelId"];
        $this->uri = "http://www.youtube.com/watch?v=" . $this->id;
        $this->thumbnail = array(
            "default" => $video['snippet']['thumbnails']['default']["url"],
            "medium" => $video['snippet']['thumbnails']['medium']["url"],
            "high" => $video['snippet']['thumbnails']['high']["url"],
        );
    }

    public function getScore()
    {
        $x = ($this->getTotalRating() !== null && $this->getTotalRating() > 0) ? $this->getTotalRating() : 1;
        return log10($x + ($this->views / 2));
    }

    public function getTotalRating()
    {
        return (null !== $this->rating) ? $this->rating['likes']+$this->rating['dislikes'] : null;
    }

    /**
     * @param \Youtube\Model\Channel $channel
     */
    public function setChannel($channel)
    {
        $this->channel = $channel;
    }

    /**
     * @return \Youtube\Model\Channel
     */
    public function getChannel()
    {
        if (null === $this->channel) {
            $yt = new \Youtube\Service\Youtube();
            $this->channel = $yt->findChannelById($this->channelId);
        }
        return $this->channel;
    }

    /**
     * @param mixed $channelId
     */
    public function setChannelId($channelId)
    {
        $this->channelId = $channelId;
    }

    /**
     * @return mixed
     */
    public function getChannelId()
    {
        return $this->channelId;
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
     * @param string $duration
     */
    public function setDuration($duration)
    {
        $this->duration = $duration;
    }

    /**
     * @return string
     */
    public function getDuration()
    {
        return $this->duration;
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
     * @param array $rating
     */
    public function setRating($rating)
    {
        $this->rating = $rating;
    }

    /**
     * @return array
     */
    public function getRating()
    {
        return $this->rating;
    }

    /**
     * @param mixed $relatedVideos
     */
    public function setRelatedVideos($relatedVideos)
    {
        $this->relatedVideos = $relatedVideos;
    }

    /**
     * @return mixed
     */
    public function getRelatedVideos()
    {
        if (null === $this->relatedVideos) {
            $yt = new \Youtube\Service\Youtube();
            $this->relatedVideos = $yt->findRelatedToId($this->id);
        }
        return $this->relatedVideos;
    }


    /**
     * @param array $tags
     */
    public function setTags($tags)
    {
        $this->tags = $tags;
    }

    /**
     * @return array
     */
    public function getTags()
    {
        return $this->tags;
    }

    /**
     * @param array $thumbnail
     */
    public function setThumbnail($thumbnail)
    {
        $this->thumbnail = $thumbnail;
    }

    /**
     * @return array
     */
    public function getThumbnail()
    {
        return $this->thumbnail;
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
     * @param string $uri
     */
    public function setUri($uri)
    {
        $this->uri = $uri;
    }

    /**
     * @return string
     */
    public function getUri()
    {
        return $this->uri;
    }

    /**
     * @param int $views
     */
    public function setViews($views)
    {
        $this->views = $views;
    }

    /**
     * @return int
     */
    public function getViews()
    {
        return $this->views;
    }


}