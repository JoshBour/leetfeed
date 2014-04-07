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
     * @ORM\Column(name="is_ignored")
     */
    private $isIgnored;

    /**
     * @ORM\Column(type="smallint")
     * @ORM\Column(name="is_related")
     */
    private $isRelated;

    /**
     * @ORM\Column(type="integer")
     * @ORM\Column(length=11)
     */
    private $rating;

    /**
     * @ORM\Column(type="datetime")
     * @ORM\Column(name="post_date")
     */
    private $postDate;

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
     * @param int $ignored
     * @return Feed
     */
    public static function create($game,$videoId,$title,$author,$description,$related = 1,$ignored=0){
        $feed = new Feed();
        $feed->setGame($game);
        $feed->setVideoId($videoId);
        $feed->setTitle($feed->sanitize($title,80));
        $feed->setAuthor($author);
        $feed->setDescription($feed->sanitize($description,200));
        $feed->setRating(0);
        $feed->setPostDate(date("Y-m-d H:i:s"));
        $feed->setIsIgnored($ignored);
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

    public function updatePostDate(){
        $this->postDate = date("Y-m-d H:i:s");
    }

    public function getCleanDescription($maxLength = 200){
        return $this->sanitize($this->description,$maxLength);
    }

    public function sanitize($data,$length){
        $data = strlen($data) > $length ? substr($data, 0, $length) . ".." : $data;
        return htmlentities($data);
    }

    public function getKeywords(){
        $keywords = array('league','of','legends','video','feed');
        $title = explode(" ",htmlentities($this->title));
        if(count($title) > 5){
            $title = array_slice($title,0,7);
        }
        return join(',',array_merge($keywords,$title));
    }

    /**
     * Returns an array with the feed's og tags.
     *
     * @return array
     */
    public function getOgTags(){
        $ogTags = array();
        $ogTags["title"] = $this->title;
        $ogTags["description"] = $this->getCleanDescription(200);
        $ogTags["image"] = $this->getThumbnail("medium");
        $ogTags["url"] = "http://www.leetfeed.com/feed/" . $this->feedId;
        $ogTags["type"] = "video.movie";
        $ogTags["video"] = "http://www.youtube.com/v/" . $this->videoId . '?version=3&amp;autohide=1';
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
     * @param mixed $isIgnored
     */
    public function setIsIgnored($isIgnored)
    {
        $this->isIgnored = $isIgnored;
    }

    /**
     * @return mixed
     */
    public function getIsIgnored()
    {
        return $this->isIgnored;
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
     * @param mixed $postDate
     */
    public function setPostDate($postDate)
    {
        $this->postDate = $postDate;
    }

    /**
     * @return mixed
     */
    public function getPostDate()
    {
        return $this->postDate;
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
     * @return ArrayCollection
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