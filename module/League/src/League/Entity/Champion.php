<?php
/**
 * Created by PhpStorm.
 * User: Josh
 * Date: 19/3/2014
 * Time: 6:59 μμ
 */

namespace League\Entity;

use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\ORM\Mapping as ORM;

/**
 * Class Champion
 * @package League\Entity
 * @ORM\Entity
 * @ORM\Table(name="champions")
 */
class Champion {

    /**
     * @ORM\ManyToMany(targetEntity="\Feed\Entity\Feed")
     * @ORM\JoinTable(name="champions_feeds",
     *          joinColumns={@ORM\JoinColumn(name="champion_name", referencedColumnName="name")},
     *          inverseJoinColumns={@ORM\JoinColumn(name="feed_id", referencedColumnName="feed_id")}
     *          )
     */
    private $feeds;

    /**
     * @ORM\Id
     * @ORM\Column(type="string")
     * @ORM\Column(length=50)
     */
    private $name;

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
     * @return ArrayCollection
     */
    public function getFeeds()
    {
        return $this->feeds;
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


} 