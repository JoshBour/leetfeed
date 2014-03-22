<?php
/**
 * Created by PhpStorm.
 * User: Josh
 * Date: 16/3/2014
 * Time: 10:22 μμ
 */

namespace League\Model;


class SummonerDto {

    private static $staticFields = array('id','name','profileIconId','revisionDate','summonerLevel');

    private $id;

    private $name;

    private $profileIconId;

    private $revisionDate;

    private $summonerLevel;

    public function __construct($summoner){
        foreach(self::$staticFields as $field){
            $this->{$field} = $summoner[$field];
        }
    }

    /**
     * @param mixed $field
     */
    public function setField($field)
    {
        $this->field = $field;
    }

    /**
     * @return mixed
     */
    public function getField()
    {
        return $this->field;
    }

    /**
     * @param mixed $id
     */
    public function setId($id)
    {
        $this->id = $id;
    }

    /**
     * @return mixed
     */
    public function getId()
    {
        return $this->id;
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
     * @param mixed $profileIconId
     */
    public function setProfileIconId($profileIconId)
    {
        $this->profileIconId = $profileIconId;
    }

    /**
     * @return mixed
     */
    public function getProfileIconId()
    {
        return $this->profileIconId;
    }

    /**
     * @param mixed $revisionDate
     */
    public function setRevisionDate($revisionDate)
    {
        $this->revisionDate = $revisionDate;
    }

    /**
     * @return mixed
     */
    public function getRevisionDate()
    {
        return $this->revisionDate;
    }

    /**
     * @param array $staticFields
     */
    public static function setStaticFields($staticFields)
    {
        self::$staticFields = $staticFields;
    }

    /**
     * @return array
     */
    public static function getStaticFields()
    {
        return self::$staticFields;
    }

    /**
     * @param mixed $summonerLevel
     */
    public function setSummonerLevel($summonerLevel)
    {
        $this->summonerLevel = $summonerLevel;
    }

    /**
     * @return mixed
     */
    public function getSummonerLevel()
    {
        return $this->summonerLevel;
    }


} 