<?php
/**
 * Created by PhpStorm.
 * User: Josh
 * Date: 28/2/2014
 * Time: 4:18 μμ
 */

namespace Feed\Entity;

use Doctrine\ORM\Mapping as ORM;
use Doctrine\Common\Collections\ArrayCollection;

/**
 * Class Feed
 * @package Feed\Entity
 * @ORM\Entity(repositoryClass="\Feed\Repository\FeedRepository")
 * @ORM\Table(name="feeds")
 */
class Feed {
    /**
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="AUTO")
     * @ORM\Column(type="integer")
     * @ORM\Column(length=11)
     * @ORM\Column(name="feed_id")
     */
    private $feedId;

    /**
     * @ORM\ManyToOne(targetEntity="Game", inversedBy="feeds")
     * @ORM\JoinColumn(name="game_id", referencedColumnName="game_id")
     */
    private $game;

    /**
     * @ORM\OneToMany(targetEntity="Account\Entity\AccountsHistory", mappedBy="feed")
     */
    private $watchedHistory;

    /**
     * @ORM\Column(type="string")
     * @ORM\Column(name="video_id")
     */
    private $videoId;

    /**
     * @ORM\Column(type="string")
     */
    private $title;

    /**
     * @ORM\Column(type="string")
     */
    private $author;

    /**
     * @ORM\Column(type="text")
     */
    private $description;

    /**
     * @ORM\Column(type="integer")
     * @ORM\Column(length=11)
     */
    private $rating;

    /**
     * @ORM\OneToMany(targetEntity="Rating", mappedBy="feed")
     */
    private $ratings;

    /**
     * @ORM\Column(type="smallint")
     * @ORM\Column(name="is_related")
     */
    private $isRelated;

    /**
     * @ORM\Column(type="smallint")
     * @ORM\Column(name="is_rising")
     */
    private $isRising;

    public static function create($game,$videoId,$title,$author,$description,$related = 0,$rising=0){
        $feed = new Feed();
        $feed->setGame($game);
        $feed->setVideoId($videoId);
        $feed->setTitle($title);
        $feed->setAuthor($author);
        $feed->setDescription($description);
        $feed->setRating(0);
        $feed->setIsRising($rising);
        $feed->setIsRelated($related);
        return $feed;
    }

    public function __construct(){
        $this->accounts = new ArrayCollection();
        $this->ratings = new ArrayCollection();
    }

    public function getOgTags(){
        $ogtags = array();
        $ogtags["title"] = $this->title;
        $ogtags["description"] = "Watch the best League of Legends videos on Leetfeed.";
        $ogtags["image"] = 'http://img.youtube.com/vi/' . $this->videoId . '/default.jpg';
        $ogtags["url"] = "http://www.leetfeed.com/feed/" . $this->feedId;
        return $ogtags;
    }

    public function getThumbnail($quality = "default"){
        $prefix = "http://img.youtube.com/vi/{$this->videoId}";
        switch($quality){
            case "0":
                return $prefix . "/0.jpg";
            case "1":
                return $prefix . "/1.jpg";
            case "2":
                return $prefix . "/2.jpg";
            case "3":
                return $prefix . "3.jpg";
            case "high":
                return $prefix . "/hqdefault.jpg";
            case "medium":
                return $prefix . "/mqdefault.jpg";
            case "standard":
                return $prefix . "/sddefault.jpg";
            default:
                return $prefix . "/default.jpg";
        }
    }

    /**
     * @param mixed $author
     */
    public function setAuthor($author)
    {
        $this->author = $author;
    }

    /**
     * @return mixed
     */
    public function getAuthor()
    {
        return $this->author;
    }

    /**
     * @param mixed $description
     */
    public function setDescription($description)
    {
        $this->description = $description;
    }

    /**
     * @return mixed
     */
    public function getDescription()
    {
        return $this->description;
    }


    /**
     * @param mixed $feedId
     */
    public function setFeedId($feedId)
    {
        $this->feedId = $feedId;
    }

    /**
     * @return mixed
     */
    public function getFeedId()
    {
        return $this->feedId;
    }

    /**
     * @param mixed $game
     */
    public function setGame($game)
    {
        $this->game = $game;
    }

    /**
     * @return mixed
     */
    public function getGame()
    {
        return $this->game;
    }

    /**
     * @param mixed $rating
     */
    public function setRating($rating)
    {
        $this->rating = $rating;
    }

    /**
     * @return mixed
     */
    public function getRating()
    {
        return $this->rating;
    }

    /**
     * @param mixed $ratings
     */
    public function setRatings($ratings)
    {
        $this->ratings = $ratings;
    }

    /**
     * @return mixed
     */
    public function getRatings()
    {
        return $this->ratings;
    }

    /**
     * @param mixed $isRelated
     */
    public function setIsRelated($isRelated)
    {
        $this->isRelated = $isRelated;
    }

    /**
     * @return mixed
     */
    public function getIsRelated()
    {
        return $this->isRelated;
    }

    /**
     * @param mixed $isRising
     */
    public function setIsRising($isRising)
    {
        $this->isRising = $isRising;
    }

    /**
     * @return mixed
     */
    public function getIsRising()
    {
        return $this->isRising;
    }

    /**
     * @param mixed $title
     */
    public function setTitle($title)
    {
        $this->title = $title;
    }

    /**
     * @return mixed
     */
    public function getTitle()
    {
        return $this->title;
    }

    /**
     * @param mixed $videoId
     */
    public function setVideoId($videoId)
    {
        $this->videoId = $videoId;
    }

    /**
     * @return mixed
     */
    public function getVideoId()
    {
        return $this->videoId;
    }

    /**
     * @param mixed $accounts
     */
    public function setAccounts($accounts)
    {
        $this->accounts = $accounts;
    }

    /**
     * @return mixed
     */
    public function getAccounts()
    {
        return $this->accounts;
    }




} 