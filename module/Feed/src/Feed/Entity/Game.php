<?php
/**
 * Created by PhpStorm.
 * User: Josh
 * Date: 27/2/2014
 * Time: 8:24 μμ
 */

namespace Feed\Entity;

use Doctrine\ORM\Mapping as ORM;
use Doctrine\Common\Collections\ArrayCollection;

/**
 * Class Game
 * @package Feed\Entity
 * @ORM\Entity
 * @ORM\Table(name="games")
 */
class Game {

    /**
     * @ORM\OneToMany(targetEntity="Feed", mappedBy="game")
     */
    private $feeds;

    /**
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="AUTO")
     * @ORM\Column(type="integer")
     * @ORM\Column(length=11)
     * @ORM\Column(name="game_id")
     */
    private $gameId;

    /**
     * @ORM\Column(type="string")
     */
    private $name;

    /**
     * @ORM\Column(type="string")
     */
    private $url;

    /**
     * @ORM\OneToMany(targetEntity="Youtuber", mappedBy="game")
     */
    private $youtubers;

    public function __construct(){
        $this->youtubers = new ArrayCollection();
        $this->feeds = new ArrayCollection();
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
     * @param mixed $gameId
     */
    public function setGameId($gameId)
    {
        $this->gameId = $gameId;
    }

    /**
     * @return mixed
     */
    public function getGameId()
    {
        return $this->gameId;
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
     * @param mixed $url
     */
    public function setUrl($url)
    {
        $this->url = $url;
    }

    /**
     * @return mixed
     */
    public function getUrl()
    {
        return $this->url;
    }

    /**
     * @param mixed $youtubers
     */
    public function setYoutubers($youtubers)
    {
        $this->youtubers = $youtubers;
    }

    /**
     * @return mixed
     */
    public function getYoutubers()
    {
        return $this->youtubers;
    }


} 