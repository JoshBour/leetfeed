<?php
/**
 * Created by PhpStorm.
 * User: Josh
 * Date: 15/3/2014
 * Time: 7:22 μμ
 */

namespace League\Entity;

use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\ORM\Mapping as ORM;

/**
 * Class Summoner
 * @package League\Entity
 * @ORM\Entity
 * @ORM\Table(name="summoners")
 *
 */
class Summoner
{
    /**
     * @ORM\ManyToOne(targetEntity="\Account\Entity\Account", inversedBy="summoners")
     * @ORM\JoinColumn(name="account_id", referencedColumnName="account_id")
     */
    private $account;

    /**
     * @ORM\Column(type="bigint")
     * @ORM\Column(length=20)
     * @ORM\Column(name="lol_id")
     */
    private $lolId;

    /**
     * @ORM\Column(type="string")
     * @ORM\Column(length=100)
     */
    private $name;

    /**
     * @ORM\Column(type="string")
     */
    private $region;

    /**
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="AUTO")
     * @ORM\Column(type="integer")
     * @ORM\Column(length=11)
     * @ORM\Column(name="summoner_id")
     */
    private $summonerId;

    /**
     * @ORM\Column(type="datetime")
     * @ORM\Column(name="update_time")
     */
    private $updateTime;

    public function __construct($account,$lolId,$name,$region){
        $this->account = $account;
        $this->lolId = $lolId;
        $this->name = $name;
        $this->region = $region;
        $this->updateTime = date("Y-m-d H:i:s", time());
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
     * @param mixed $lolId
     */
    public function setLolId($lolId)
    {
        $this->lolId = $lolId;
    }

    /**
     * @return mixed
     */
    public function getLolId()
    {
        return $this->lolId;
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
     * @param mixed $region
     */
    public function setRegion($region)
    {
        $this->region = $region;
    }

    /**
     * @return mixed
     */
    public function getRegion()
    {
        return $this->region;
    }

    /**
     * @param mixed $summonerId
     */
    public function setSummonerId($summonerId)
    {
        $this->summonerId = $summonerId;
    }

    /**
     * @return mixed
     */
    public function getSummonerId()
    {
        return $this->summonerId;
    }

    /**
     * @param mixed $updateTime
     */
    public function setUpdateTime($updateTime)
    {
        $this->updateTime = $updateTime;
    }

    /**
     * @return mixed
     */
    public function getUpdateTime()
    {
        return $this->updateTime;
    }


}