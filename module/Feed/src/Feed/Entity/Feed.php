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
     * @ORM\Column(type="string")
     */
    private $author;

    /**
     * @ORM\OneToMany(targetEntity="Comment", mappedBy="feed")
     */
    private $comments;

    /**
     * @ORM\Column(type="text")
     */
    private $description;

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
     * @ORM\Column(type="smallint")
     * @ORM\Column(name="is_related")
     */
    private $isRelated;

    /**
     * @ORM\Column(type="smallint")
     * @ORM\Column(name="is_rising")
     */
    private $isRising;

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
     * @ORM\ManyToMany(targetEntity="Feed", inversedBy="relatedFeedsToMe")
     * @ORM\JoinTable(name="related_feeds",
     *      joinColumns={@ORM\JoinColumn(name="feed_id", referencedColumnName="feed_id")},
     *      inverseJoinColumns={@ORM\JoinColumn(name="related_feed_id", referencedColumnName="feed_id")}
     *      )
     */
    private $relatedFeeds;

    /**
     * @ORM\ManyToMany(targetEntity="Feed", mappedBy="relatedFeeds")
     */
    private $relatedFeedsToMe;

    /**
     * @ORM\Column(type="string")
     */
    private $title;

    /**
     * @ORM\Column(type="string")
     * @ORM\Column(name="video_id")
     */
    private $videoId;

    /**
     * @ORM\OneToMany(targetEntity="Account\Entity\AccountsHistory", mappedBy="feed")
     */
    private $watchedHistory;

    /**
     * Creates and returns a new Feed entity.
     *
     * @param Game $game
     * @param int $videoId
     * @param string $title
     * @param string $author
     * @param string $description
     * @param int $related
     * @param int $rising
     * @return Feed
     */
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
        $this->comments = new ArrayCollection();
        $this->accounts = new ArrayCollection();
        $this->ratings = new ArrayCollection();
        $this->relatedFeeds = new ArrayCollection();
        $this->relatedFeedsToMe = new ArrayCollection();
    }

    /**
     * Returns an array with the feed's og tags.
     *
     * @return array
     */
    public function getOgTags(){
        $ogTags = array();
        $ogTags["title"] = $this->title;
        $ogTags["description"] = "Watch the best League of Legends videos on Leetfeed.";
        $ogTags["image"] = 'http://img.youtube.com/vi/' . $this->videoId . '/default.jpg';
        $ogTags["url"] = "http://www.leetfeed.com/feed/" . $this->feedId;
        return $ogTags;
    }

    /**
     * Returns the thumbnail url of the Feed, given the quality.
     *
     * @param string $quality
     * @return string
     */
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
     * @param ArrayCollection $accounts
     */
    public function setAccounts($accounts)
    {
        $this->accounts = $accounts;
    }

    /**
     * @return ArrayCollection
     */
    public function getAccounts()
    {
        return $this->accounts;
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
     * @param mixed $comments
     */
    public function setComments($comments)
    {
        $this->comments = $comments;
    }

    /**
     * @return ArrayCollection
     */
    public function getComments()
    {
        return $this->comments;
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
     * @return ArrayCollection
     */
    public function getRatings()
    {
        return $this->ratings;
    }

    /**
     * @param Feed|array $relatedFeeds
     */
    public function addRelatedFeeds($relatedFeeds){
        if(is_array($relatedFeeds)){
            foreach($relatedFeeds as $feed)
                $this->relatedFeeds->add($feed);
        }else{
            $this->relatedFeeds->add($relatedFeeds);
        }
    }

    /**
     * @param mixed $relatedFeeds
     */
    public function setRelatedFeeds($relatedFeeds)
    {
        $this->relatedFeeds = $relatedFeeds;
    }

    /**
     * @return mixed
     */
    public function getRelatedFeeds()
    {
        return $this->relatedFeeds;
    }

    /**
     * @param mixed $relatedFeedsToMe
     */
    public function setRelatedFeedsToMe($relatedFeedsToMe)
    {
        $this->relatedFeedsToMe = $relatedFeedsToMe;
    }

    /**
     * @return mixed
     */
    public function getRelatedFeedsToMe()
    {
        return $this->relatedFeedsToMe;
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
     * @param mixed $watchedHistory
     */
    public function setWatchedHistory($watchedHistory)
    {
        $this->watchedHistory = $watchedHistory;
    }

    /**
     * @return mixed
     */
    public function getWatchedHistory()
    {
        return $this->watchedHistory;
    }





} 