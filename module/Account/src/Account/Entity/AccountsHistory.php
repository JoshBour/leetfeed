<?php
/**
 * Created by PhpStorm.
 * User: Josh
 * Date: 13/3/2014
 * Time: 12:57 μμ
 */

namespace Account\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * Class AccountsHistory
 * @package Account\Entity
 * @ORM\Entity
 * @ORM\Table(name="accounts_feeds")
 */
class AccountsHistory {
    /**
     * @ORM\Id
     * @ORM\ManyToOne(targetEntity="Account", inversedBy="feeds")
     * @ORM\JoinColumn(name="account_id", referencedColumnName="account_id")
     **/
    private $account;

    /**
     * @ORM\Id
     * @ORM\ManyToOne(targetEntity="Feed\Entity\Feed", inversedBy="watchedHistory")
     * @ORM\JoinColumn(name="feed_id", referencedColumnName="feed_id")
     **/
    private $feed;

    /**
     * @ORM\Column(type="datetime")
     * @ORM\Column(name="watch_time")
     */
    private $watchTime;

    /**
     * @param Account $account
     * @param \Feed\Entity\Feed $feed
     * @return AccountsHistory
     */
    public static function create($account, $feed)
    {
        $joinEntry = new AccountsHistory();
        $joinEntry->setAccount($account);
        $joinEntry->setFeed($feed);
        $joinEntry->setWatchTime(date("Y-m-d H:i:s"));
        return $joinEntry;
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

    /**
     * @param mixed $watchTime
     */
    public function setWatchTime($watchTime)
    {
        $this->watchTime = $watchTime;
    }

    /**
     * @return mixed
     */
    public function getWatchTime()
    {
        return $this->watchTime;
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


} 