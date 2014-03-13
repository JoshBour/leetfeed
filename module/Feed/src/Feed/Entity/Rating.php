<?php
/**
 * Created by PhpStorm.
 * User: Josh
 * Date: 28/2/2014
 * Time: 8:42 μμ
 */

namespace Feed\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * Class Rating
 * @package Feed\Entity
 * @ORM\Entity
 * @ORM\Table(name="accounts_ratings")
 */
class Rating {

    /**
     * @ORM\Id
     * @ORM\OneToOne(targetEntity="\Account\Entity\Account")
     * @ORM\JoinColumn(name="account_id", referencedColumnName="account_id")
     */
    private $account;

    /**
     * @ORM\Id
     * @ORM\OneToOne(targetEntity="Feed")
     * @ORM\JoinColumn(name="feed_id", referencedColumnName="feed_id")
     */
    private $feed;

    /**
     * @ORM\Column(type="smallint")
     */
    private $rating;

    /**
     * @ORM\Column(type="datetime")
     * @ORM\Column(name="rate_time")
     */
    private $rateTime;

    public function __construct($account, $feed, $rating){
        $this->account = $account;
        $this->feed = $feed;
        $this->rating = $rating;
        $this->rateTime = date("Y-m-d H:i:s",time());
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
     * @param mixed $rateTime
     */
    public function setRateTime($rateTime)
    {
        $this->rateTime = $rateTime;
    }

    /**
     * @return mixed
     */
    public function getRateTime()
    {
        return $this->rateTime;
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
     * @param mixed $account
     */
    public function setAccount($account)
    {
        $this->account = $account;
    }

    /**
     * @return mixed
     */
    public function getAccount()
    {
        return $this->account;
    }



} 