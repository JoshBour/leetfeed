<?php
/**
 * Created by PhpStorm.
 * User: Josh
 * Date: 27/2/2014
 * Time: 4:25 μμ
 */

namespace Account\Entity;

use Doctrine\ORM\Mapping as ORM;
use Doctrine\Common\Collections\ArrayCollection;

/**
 * Class Account
 * @package Feed\Entity
 * @ORM\Entity
 * @ORM\Table(name="accounts")
 */
class Account {

    /**
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="AUTO")
     * @ORM\Column(type="integer")
     * @ORM\Column(length=11)
     * @ORM\Column(name="account_id")
     */
    private $accountId;

    /**
     * @ORM\Column(type="string")
     * @ORM\Column(length=50)
     * @ORM\Column(nullable=true)
     */
    private $username;

    /**
     * @ORM\Column(type="string")
     * @ORM\Column(length=128)
     * @ORM\Column(nullable=true)
     */
    private $password;

    /**
     * @ORM\Column(type="string")
     * @ORM\Column(length=255)
     * @ORM\Column(nullable=true)
     */
    private $email;

    /**
     * @ORM\Column(type="string")
     * @ORM\Column(length=50)
     */
    private $ip;

    /**
     * @ORM\Column(type="datetime")
     * @ORM\Column(name="first_seen")
     */
    private $firstSeen;

    /**
     * @ORM\ManyToMany(targetEntity="\Feed\Entity\Feed", inversedBy="accounts")
     * @ORM\JoinTable(name="accounts_feeds",
     *      joinColumns={@ORM\JoinColumn(name="account_id", referencedColumnName="account_id")},
     *      inverseJoinColumns={@ORM\JoinColumn(name="feed_id", referencedColumnName="feed_id")}
     *      )
     */
    private $feeds;

    /**
     * @ORM\Column(type="timestamp")
     * @ORM\Column(name="last_seen")
     */
    private $lastSeen;

    /**
     * @ORM\OneToMany(targetEntity="\Feed\Entity\Rating", mappedBy="account")
     */
    private $ratings;

    /**
     * Hash the password.
     *
     * @param string $password
     * @return string
     */
    public static function getHashedPassword($password)
    {
        return crypt($password . 'leetfeedpenbour');
    }

    /**
     * Check if the user's password is the same as the provided one.
     *
     * @param Account $account
     * @param string $password
     * @return bool
     */
    public static function hashPassword($account, $password)
    {
        return ($account->getPassword() === crypt($password . 'leetfeedpenbour', $account->getPassword()));
    }

    public function __construct(){
        $this->feeds = new ArrayCollection();
        $this->ratings = new ArrayCollection();
    }

    public function getLikedFeeds(){
        $liked = array();
        foreach ($this->ratings->toArray() as $rating)
            if ($rating->getRating() == 1) $liked[] = $rating->getFeed();
        return $liked;
    }

    /**
     * Check if the user has rated a feed.
     *
     * @param \Feed\Entity\Feed $feed
     * @return null|\Feed\Entity\Rating
     */
    public function getRated($feed)
    {
        foreach ($this->ratings->toArray() as $rating)
            if ($rating->getFeed()->getFeedId() == $feed) return $rating;
        return null;
    }

    public function hasWatched($feed){
        $id = $feed->getVideoId();
        if($this->feeds)
            foreach($this->feeds as $watchedFeed){
                if($watchedFeed->getVideoId() == $id) return true;
            }
        return false;
    }

    /**
     * @param mixed $accountId
     */
    public function setAccountId($accountId)
    {
        $this->accountId = $accountId;
    }

    /**
     * @return mixed
     */
    public function getAccountId()
    {
        return $this->accountId;
    }

    /**
     * @param mixed $email
     */
    public function setEmail($email)
    {
        $this->email = $email;
    }

    /**
     * @return mixed
     */
    public function getEmail()
    {
        return $this->email;
    }

    /**
     * @param mixed $feeds
     */
    public function setFeeds($feeds)
    {
        $this->feeds = $feeds;
    }

    public function addFeeds($feeds){
        if(is_array($feeds)){
            foreach($feeds as $feed)
                $this->feeds->add($feed);
        }else{
            $this->feeds->add($feeds);
        }
    }

    /**
     * @return mixed
     */
    public function getFeeds()
    {
        return $this->feeds;
    }

    /**
     * @param mixed $firstSeen
     */
    public function setFirstSeen($firstSeen)
    {
        $this->firstSeen = $firstSeen;
    }

    /**
     * @return mixed
     */
    public function getFirstSeen()
    {
        return $this->firstSeen;
    }

    /**
     * @param mixed $ip
     */
    public function setIp($ip)
    {
        $this->ip = $ip;
    }

    /**
     * @return mixed
     */
    public function getIp()
    {
        return $this->ip;
    }

    /**
     * @param mixed $lastSeen
     */
    public function setLastSeen($lastSeen)
    {
        $this->lastSeen = $lastSeen;
    }

    /**
     * @return mixed
     */
    public function getLastSeen()
    {
        return $this->lastSeen;
    }

    /**
     * @param mixed $password
     */
    public function setPassword($password)
    {
        $this->password = $password;
    }

    /**
     * @return mixed
     */
    public function getPassword()
    {
        return $this->password;
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
     * @param mixed $username
     */
    public function setUsername($username)
    {
        $this->username = $username;
    }

    /**
     * @return mixed
     */
    public function getUsername()
    {
        return $this->username;
    }



} 