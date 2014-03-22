<?php
/**
 * Created by PhpStorm.
 * User: Josh
 * Date: 15/3/2014
 * Time: 8:21 μμ
 */

namespace League\Model;


class PlayerDto {
    private static $staticFields = array('championId','summonerId','teamId');

    private $championId;

    private $summonerId;

    private $teamId;

    public function __construct($player){
        foreach(self::$staticFields as $field){
            $this->{$field} = $player[$field];
        }
    }

    /**
     * @param mixed $championId
     */
    public function setChampionId($championId)
    {
        $this->championId = $championId;
    }

    /**
     * @return mixed
     */
    public function getChampionId()
    {
        return $this->championId;
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
     * @param mixed $teamId
     */
    public function setTeamId($teamId)
    {
        $this->teamId = $teamId;
    }

    /**
     * @return mixed
     */
    public function getTeamId()
    {
        return $this->teamId;
    }


} 