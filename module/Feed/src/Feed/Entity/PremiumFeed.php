<?php
/**
 * Created by PhpStorm.
 * User: Josh
 * Date: 5/3/2014
 * Time: 5:36 μμ
 */

namespace Feed\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * Class PremiumFeed
 * @package Feed\Entity
 * @ORM\Entity
 * @ORM\Table(name="premium_feeds")
 */
class PremiumFeed {
    /**
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="AUTO")
     * @ORM\Column(type="integer")
     * @ORM\Column(length=11)
     * @ORM\Column(name="premium_feed_id")
     */
    private $premiumFeedId;

    /**
     * @ORM\OneToOne(targetEntity="Feed")
     * @ORM\JoinColumn(name="feed_id", referencedColumnName="feed_id")
     */
    private $feed;

    /**
     * @ORM\Column(type="string")
     * @ORM\Column(name="author_email")
     */
    private $authorEmail;

    /**
     * @ORM\Column(type="datetime")
     * @ORM\Column(name="start_time")
     */
    private $startTime;

    /**
     * @ORM\Column(type="datetime")
     * @ORM\Column(name="end_time")
     */
    private $endTime;

    /**
     * @ORM\Column(type="float")
     */
    private $price;

    /**
     * @ORM\Column(type="integer")
     * @ORM\Column(length=11)
     */
    private $visits;

    /**
     * @param mixed $authorEmail
     */
    public function setAuthorEmail($authorEmail)
    {
        $this->authorEmail = $authorEmail;
    }

    /**
     * @return mixed
     */
    public function getAuthorEmail()
    {
        return $this->authorEmail;
    }

    /**
     * @param mixed $endTime
     */
    public function setEndTime($endTime)
    {
        $this->endTime = $endTime;
    }

    /**
     * @return mixed
     */
    public function getEndTime()
    {
        return $this->endTime;
    }

    /**
     * @param mixed $feed
     */
    public function setFeed($feed)
    {
        $this->feed = $feed;
    }

    /**
     * @return mixed
     */
    public function getFeed()
    {
        return $this->feed;
    }

    /**
     * @param mixed $premiumFeedId
     */
    public function setPremiumFeedId($premiumFeedId)
    {
        $this->premiumFeedId = $premiumFeedId;
    }

    /**
     * @return mixed
     */
    public function getPremiumFeedId()
    {
        return $this->premiumFeedId;
    }

    /**
     * @param mixed $price
     */
    public function setPrice($price)
    {
        $this->price = $price;
    }

    /**
     * @return mixed
     */
    public function getPrice()
    {
        return $this->price;
    }

    /**
     * @param mixed $startTime
     */
    public function setStartTime($startTime)
    {
        $this->startTime = $startTime;
    }

    /**
     * @return mixed
     */
    public function getStartTime()
    {
        return $this->startTime;
    }

    /**
     * @param mixed $visits
     */
    public function setVisits($visits)
    {
        $this->visits = $visits;
    }

    /**
     * @return mixed
     */
    public function getVisits()
    {
        return $this->visits;
    }


} 