<?php
namespace Feed\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * Class Comment
 * @package Feed\Entity
 * @ORM\Entity
 * @ORM\Table(name="comments")
 */
class Comment
{
    /**
     * @ORM\ManyToOne(targetEntity="Account\Entity\Account", inversedBy="comments")
     * @ORM\JoinColumn(name="account_id", referencedColumnName="account_id")
     */
    private $account;

    /**
     * @ORM\Column(type="string")
     * @ORM\Column(length=150)
     */
    private $content;

    /**
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="AUTO")
     * @ORM\Column(type="bigint")
     * @ORM\Column(length=20)
     * @ORM\Column(name="comment_id")
     */
    private $commentId;

    /**
     * @ORM\ManyToOne(targetEntity="Feed", inversedBy="comments")
     * @ORM\JoinColumn(name="feed_id", referencedColumnName="feed_id")
     */
    private $feed;

    /**
     * @ORM\Column(type="datetime")
     * @ORM\Column(name="post_time")
     */
    private $postTime;

    /**
     * Get the time difference between the comment's post time and now.
     *
     * @return string
     */
    public function getTimeAgo()
    {
        $time = strtotime($this->postTime);
        $time = time() - $time; // to get the time since that moment

        $tokens = array(
            31536000 => 'year',
            2592000 => 'month',
            604800 => 'week',
            86400 => 'day',
            3600 => 'hour',
            60 => 'minute',
            1 => 'second'
        );

        foreach ($tokens as $unit => $text) {
            if ($time < $unit) continue;
            $numberOfUnits = floor($time / $unit);
            return $numberOfUnits . ' ' . $text . (($numberOfUnits > 1) ? 's' : '');
        }
        return null;
    }

    /**
     * @param \Account\Entity\Account $account
     */
    public function setAccount($account)
    {
        $this->account = $account;
    }

    /**
     * @return \Account\Entity\Account
     */
    public function getAccount()
    {
        return $this->account;
    }

    /**
     * @param string $content
     */
    public function setContent($content)
    {
        $this->content = $content;
    }

    /**
     * @return string
     */
    public function getContent()
    {
        return $this->content;
    }

    /**
     * @param int $commentId
     */
    public function setCommentId($commentId)
    {
        $this->commentId = $commentId;
    }

    /**
     * @return int
     */
    public function getCommentId()
    {
        return $this->commentId;
    }

    /**
     * @param \Feed\Entity\Feed $feed
     */
    public function setFeed($feed)
    {
        $this->feed = $feed;
    }

    /**
     * @return \Feed\Entity\Feed
     */
    public function getFeed()
    {
        return $this->feed;
    }

    /**
     * @param string $postTime
     */
    public function setPostTime($postTime)
    {
        $this->postTime = $postTime;
    }

    /**
     * @return string
     */
    public function getPostTime()
    {
        return $this->postTime;
    }


} 