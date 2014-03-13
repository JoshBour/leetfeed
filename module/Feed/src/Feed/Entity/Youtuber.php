<?php
/**
 * Created by PhpStorm.
 * User: Josh
 * Date: 27/2/2014
 * Time: 8:17 μμ
 */

namespace Feed\Entity;

use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\ORM\Mapping as ORM;

/**
 * Class Youtuber
 * @package Feed\Entity
 * @ORM\Entity
 * @ORM\Table(name="youtubers")
 */
class Youtuber {

    /**
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="AUTO")
     * @ORM\Column(type="integer")
     * @ORM\Column(length=11)
     * @ORM\Column(name="youtuber_id")
     */
    private $youtuberId;

    /**
     * @ORM\Column(type="string")
     */
    private $name;

    /**
     * @ORM\Column(type="string")
     * @ORM\Column(name="ig_name")
     */
    private $igName;

    /**
     * @ORM\Column(type="string")
     */
    private $thumbnail;

    /**
     * @ORM\ManyToOne(targetEntity="Game", inversedBy="youtubers")
     * @ORM\JoinColumn(name="game_id", referencedColumnName="game_id")
     */
    private $game;

    /**
     * @ORM\ManyToMany(targetEntity="Feed")
     * @ORM\JoinTable(name="youtubers_feeds",
     *      joinColumns={@ORM\JoinColumn(name="youtuber_id", referencedColumnName="youtuber_id")},
     *      inverseJoinColumns={@ORM\JoinColumn(name="feed_id", referencedColumnName="feed_id")}
     *      )
     */
    private $feeds;

    public function __construct(){
        $this->feeds = new ArrayCollection();
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
     * @param mixed $feeds
     */
    public function setFeeds($feeds)
    {
        $this->feeds = $feeds;
    }

    /**
     * @return mixed
     */
    public function getFeeds()
    {
        return $this->feeds;
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
     * @param mixed $igName
     */
    public function setIgName($igName)
    {
        $this->igName = $igName;
    }

    /**
     * @return mixed
     */
    public function getIgName()
    {
        return $this->igName;
    }

    /**
     * @param mixed $name
     */
    public function setName($name)
    {
        $this->name = $name;
    }

    /**
     * @return mixed
     */
    public function getName()
    {
        return $this->name;
    }

    /**
     * @param mixed $thumbnail
     */
    public function setThumbnail($thumbnail)
    {
        $this->thumbnail = $thumbnail;
    }

    /**
     * @return mixed
     */
    public function getThumbnail()
    {
        return $this->thumbnail;
    }

    /**
     * @param mixed $youtuberId
     */
    public function setYoutuberId($youtuberId)
    {
        $this->youtuberId = $youtuberId;
    }

    /**
     * @return mixed
     */
    public function getYoutuberId()
    {
        return $this->youtuberId;
    }


} 