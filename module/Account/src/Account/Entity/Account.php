<?php
namespace Account\Entity;

use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\ORM\Mapping as ORM;

/**
 * Class Account
 * @package Feed\Entity
 * @ORM\Entity(repositoryClass="\Account\Repository\AccountRepository")
 * @ORM\Table(name="accounts")
 */
class Account
{

    /**
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="AUTO")
     * @ORM\Column(type="integer")
     * @ORM\Column(length=11)
     * @ORM\Column(name="account_id")
     */
    private $accountId;

    /**
     * @ORM\OneToMany(targetEntity="Feed\Entity\Comment", mappedBy="account")
     */
    private $comments;

    /**
     * @ORM\Column(type="string")
     * @ORM\Column(length=255)
     * @ORM\Column(nullable=true)
     */
    private $email;

    /**
     * @ORM\OneToMany(targetEntity="AccountsHistory", mappedBy="account")
     */
    private $feeds;

    /**
     * @ORM\Column(type="datetime")
     * @ORM\Column(name="first_seen")
     */
    private $firstSeen;

    /**
     * @ORM\Column(type="string")
     * @ORM\Column(length=50)
     */
    private $ip;

    /**
     * @ORM\Column(type="timestamp")
     * @ORM\Column(name="last_seen")
     */
    private $lastSeen;

    /**
     * @ORM\Column(type="string")
     * @ORM\Column(length=128)
     * @ORM\Column(nullable=true)
     */
    private $password;

    /**
     * @ORM\OneToMany(targetEntity="\Feed\Entity\Rating", mappedBy="account")
     */
    private $ratings;

    /**
     * @ORM\OneToMany(targetEntity="\League\Entity\Summoner", mappedBy="account")
     */
    private $summoners;

    /**
     * @ORM\Column(type="string")
     * @ORM\Column(length=50)
     * @ORM\Column(nullable=true)
     */
    private $username;


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

    public function __construct()
    {
        $this->comments = new ArrayCollection();
        $this->feeds = new ArrayCollection();
        $this->ratings = new ArrayCollection();
        $this->summoners = new ArrayCollection();
    }

    /**
     * Returns an array including the account's leet'ed feeds.
     *
     * @return array
     */
    public function getLikedFeeds()
    {
        $liked = array();
        /**
         * @var $rating \Feed\Entity\Rating
         */
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
        /**
         * @var $rating \Feed\Entity\Rating
         */
        foreach ($this->ratings->toArray() as $rating)
            if ($rating->getFeed()->getFeedId() == $feed) return $rating;
        return null;
    }

    /**
     * Check if the user has watched a feed
     *
     * @param \Youtube\Model\Video $feed
     * @return bool
     */
    public function hasWatched($feed)
    {
        $id = $feed->getId();
        /**
         * @var $watchedFeed AccountsHistory
         */
        foreach ($this->feeds as $watchedFeed) {
            if ($watchedFeed->getFeed()->getFeedId() == $id) return true;
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
     * @param ArrayCollection $comments
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
     * @param array|\Feed\Entity\Feed $feeds
     */
    public function addFeeds($feeds)
    {
        if (is_array($feeds)) {
            foreach ($feeds as $feed)
                $this->feeds->add($feed);
        } else {
            $this->feeds->add($feeds);
        }
    }

    /**
     * @param ArrayCollection $feeds
     */
    public function setFeeds($feeds)
    {
        $this->feeds = $feeds;
    }

    /**
     * @return ArrayCollection
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
     * @return ArrayCollection
     */
    public function getRatings()
    {
        return $this->ratings;
    }

    /**
     * @param array|\League\Entity\Summoner $summoners
     */
    public function addSummoners($summoners)
    {
        if (is_array($summoners)) {
            foreach ($summoners as $summoner)
                $this->summoners->add($summoner);
        } else {
            $this->summoners->add($summoners);
        }
    }

    /**
     * @param array|\League\Entity\Summoner $summoners
     */
    public function removeSummoners($summoners)
    {
        if (is_array($summoners)) {
            foreach ($summoners as $summoner)
                $this->summoners->removeElement($summoner);
        } else {
            $this->summoners->removeElement($summoners);
        }
    }

    /**
     * @param mixed $summoners
     */
    public function setSummoners($summoners)
    {
        $this->summoners = $summoners;
    }

    /**
     * @return mixed
     */
    public function getSummoners()
    {
        return $this->summoners;
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