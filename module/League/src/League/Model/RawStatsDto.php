<?php
/**
 * Created by PhpStorm.
 * User: Josh
 * Date: 15/3/2014
 * Time: 8:14 μμ
 */

namespace League\Model;


class RawStatsDto
{
    private static $staticFields = array('assists', 'barracksKilled', 'championsKilled',
        'combatPlayerScore', 'consumablesPurchased', 'damageDealtPlayer',
        'doubleKills', 'firstBlood', 'gold',
        'goldEarned', 'goldSpent', 'item0',
        'item1', 'item2', 'item3',
        'item4', 'item5', 'item6',
        'itemsPurchased', 'killingSprees', 'largestCriticalStrike',
        'largestKillingSpree', 'largestMultiKill', 'legendaryItemsCreated',
        'level', 'magicDamageDealtPlayer', 'magicDamageDealtToChampions',
        'magicDamageTaken', 'minionsDenied', 'minionsKilled',
        'neutralMinionsKilled', 'neutralMinionsKilledEnemyJungle', 'neutralMinionsKilledYourJungle',
        'nexusKilled', 'nodeCapture', 'nodeCaptureAssist',
        'nodeNeutralize', 'nodeNeutralizeAssist', 'numDeaths',
        'numItemsBought', 'objectivePlayerScore', 'pentaKills',
        'physicalDamageDealtPlayer', 'physicalDamageDealtToChampions', 'physicalDamageTaken',
        'quadraKills', 'sightWardsBought', 'spell1Cast',
        'spell2Cast', 'spell3Cast', 'spell4Cast',
        'summonSpell1Cast', 'summonSpell2Cast', 'superMonsterKilled',
        'team', 'teamObjective', 'timePlayed',
        'totalDamageDealt', 'totalDamageDealtToChampions', 'totalDamageTaken',
        'totalHeal', 'totalPlayerScore', 'totalScoreRank',
        'totalTimeCrowdControlDealt', 'totalUnitsHealed', 'tripleKills',
        'trueDamageDealtPlayer', 'trueDamageDealtToChampions', 'trueDamageTaken',
        'turretsKilled', 'unrealKills', 'victoryPointTotal',
        'visionWardsBought', 'wardKilled', 'wardPlaced', 'win');

    private $assists;

    private $barracksKilled;

    private $championsKilled;

    private $combatPlayerScore;

    private $consumablesPurchased;

    private $damageDealtPlayer;

    private $doubleKills;

    private $firstBlood;

    private $gold;

    private $private;

    private $goldSpent;

    private $item0;

    private $item1;

    private $item2;

    private $item3;

    private $item4;

    private $item5;

    private $item6;

    private $itemsPurchased;

    private $killingSprees;

    private $largestCriticalStrike;

    private $largestKillingSpree;

    private $largestMultiKill;

    private $legendaryItemsCreated;

    private $level;

    private $magicDamageDealtPlayer;

    private $magicDamageDealtToChampions;

    private $magicDamageTaken;

    private $minionsDenied;

    private $minionsKilled;

    private $neutralMinionsKilled;

    private $neutralMinionsKilledEnemyJungle;

    private $neutralMinionsKilledYourJungle;

    private $nexusKilled;

    private $nodeCapture;

    private $nodeCaptureAssist;

    private $nodeNeutralize;

    private $nodeNeutralizeAssist;

    private $numDeaths;

    private $numItemsBought;

    private $objectivePlayerScore;

    private $pentaKills;

    private $physicalDamageDealtPlayer;

    private $physicalDamageDealtToChampions;

    private $physicalDamageTaken;

    private $quadraKills;

    private $sightWardsBought;

    private $spell1Cast;

    private $spell2Cast;

    private $spell3Cast;

    private $spell4Cast;

    private $summonSpell1Cast;

    private $summonSpell2Cast;

    private $superMonsterKilled;

    private $team;

    private $teamObjective;

    private $timePlayed;

    private $totalDamageDealt;

    private $totalDamageDealtToChampions;

    private $totalDamageTaken;

    private $totalHeal;

    private $totalPlayerScore;

    private $totalScoreRank;

    private $totalTimeCrowdControlDealt;

    private $totalUnitsHealed;

    private $tripleKills;

    private $trueDamageDealtPlayer;

    private $trueDamageDealtToChampions;

    private $trueDamageTaken;

    private $turretsKilled;

    private $unrealKills;

    private $victoryPoTotal;

    private $visionWardsBought;

    private $wardKilled;

    private $wardPlaced;

    private $win;

    public function __construct($stats)
    {
        foreach (self::$staticFields as $field) {
            if (isset($stats[$field]))
                $this->$field = $stats[$field];
        }
    }

    public function getLeetScore(){
        if(!$this->win){
            return 1/($this->getKda()+0.9);
        }else{
            return $this->getKda();
        }
    }

    public function getKda(){
        if($this->assists && $this->numDeaths && $this->championsKilled){
            return ($this->assists + $this->championsKilled)/$this->numDeaths;
        }
        return 0;
    }

    /**
     * @param mixed $assists
     */
    public function setAssists($assists)
    {
        $this->assists = $assists;
    }

    /**
     * @return mixed
     */
    public function getAssists()
    {
        return $this->assists;
    }

    /**
     * @param mixed $barracksKilled
     */
    public function setBarracksKilled($barracksKilled)
    {
        $this->barracksKilled = $barracksKilled;
    }

    /**
     * @return mixed
     */
    public function getBarracksKilled()
    {
        return $this->barracksKilled;
    }

    /**
     * @param mixed $championsKilled
     */
    public function setChampionsKilled($championsKilled)
    {
        $this->championsKilled = $championsKilled;
    }

    /**
     * @return mixed
     */
    public function getChampionsKilled()
    {
        return $this->championsKilled;
    }

    /**
     * @param mixed $combatPlayerScore
     */
    public function setCombatPlayerScore($combatPlayerScore)
    {
        $this->combatPlayerScore = $combatPlayerScore;
    }

    /**
     * @return mixed
     */
    public function getCombatPlayerScore()
    {
        return $this->combatPlayerScore;
    }

    /**
     * @param mixed $consumablesPurchased
     */
    public function setConsumablesPurchased($consumablesPurchased)
    {
        $this->consumablesPurchased = $consumablesPurchased;
    }

    /**
     * @return mixed
     */
    public function getConsumablesPurchased()
    {
        return $this->consumablesPurchased;
    }

    /**
     * @param mixed $damageDealtPlayer
     */
    public function setDamageDealtPlayer($damageDealtPlayer)
    {
        $this->damageDealtPlayer = $damageDealtPlayer;
    }

    /**
     * @return mixed
     */
    public function getDamageDealtPlayer()
    {
        return $this->damageDealtPlayer;
    }

    /**
     * @param mixed $doubleKills
     */
    public function setDoubleKills($doubleKills)
    {
        $this->doubleKills = $doubleKills;
    }

    /**
     * @return mixed
     */
    public function getDoubleKills()
    {
        return $this->doubleKills;
    }

    /**
     * @param mixed $firstBlood
     */
    public function setFirstBlood($firstBlood)
    {
        $this->firstBlood = $firstBlood;
    }

    /**
     * @return mixed
     */
    public function getFirstBlood()
    {
        return $this->firstBlood;
    }

    /**
     * @param mixed $gold
     */
    public function setGold($gold)
    {
        $this->gold = $gold;
    }

    /**
     * @return mixed
     */
    public function getGold()
    {
        return $this->gold;
    }

    /**
     * @param mixed $goldSpent
     */
    public function setGoldSpent($goldSpent)
    {
        $this->goldSpent = $goldSpent;
    }

    /**
     * @return mixed
     */
    public function getGoldSpent()
    {
        return $this->goldSpent;
    }

    /**
     * @param mixed $item0
     */
    public function setItem0($item0)
    {
        $this->item0 = $item0;
    }

    /**
     * @return mixed
     */
    public function getItem0()
    {
        return $this->item0;
    }

    /**
     * @param mixed $item1
     */
    public function setItem1($item1)
    {
        $this->item1 = $item1;
    }

    /**
     * @return mixed
     */
    public function getItem1()
    {
        return $this->item1;
    }

    /**
     * @param mixed $item2
     */
    public function setItem2($item2)
    {
        $this->item2 = $item2;
    }

    /**
     * @return mixed
     */
    public function getItem2()
    {
        return $this->item2;
    }

    /**
     * @param mixed $item3
     */
    public function setItem3($item3)
    {
        $this->item3 = $item3;
    }

    /**
     * @return mixed
     */
    public function getItem3()
    {
        return $this->item3;
    }

    /**
     * @param mixed $item4
     */
    public function setItem4($item4)
    {
        $this->item4 = $item4;
    }

    /**
     * @return mixed
     */
    public function getItem4()
    {
        return $this->item4;
    }

    /**
     * @param mixed $item5
     */
    public function setItem5($item5)
    {
        $this->item5 = $item5;
    }

    /**
     * @return mixed
     */
    public function getItem5()
    {
        return $this->item5;
    }

    /**
     * @param mixed $item6
     */
    public function setItem6($item6)
    {
        $this->item6 = $item6;
    }

    /**
     * @return mixed
     */
    public function getItem6()
    {
        return $this->item6;
    }

    /**
     * @param mixed $itemsPurchased
     */
    public function setItemsPurchased($itemsPurchased)
    {
        $this->itemsPurchased = $itemsPurchased;
    }

    /**
     * @return mixed
     */
    public function getItemsPurchased()
    {
        return $this->itemsPurchased;
    }

    /**
     * @param mixed $killingSprees
     */
    public function setKillingSprees($killingSprees)
    {
        $this->killingSprees = $killingSprees;
    }

    /**
     * @return mixed
     */
    public function getKillingSprees()
    {
        return $this->killingSprees;
    }

    /**
     * @param mixed $largestCriticalStrike
     */
    public function setLargestCriticalStrike($largestCriticalStrike)
    {
        $this->largestCriticalStrike = $largestCriticalStrike;
    }

    /**
     * @return mixed
     */
    public function getLargestCriticalStrike()
    {
        return $this->largestCriticalStrike;
    }

    /**
     * @param mixed $largestKillingSpree
     */
    public function setLargestKillingSpree($largestKillingSpree)
    {
        $this->largestKillingSpree = $largestKillingSpree;
    }

    /**
     * @return mixed
     */
    public function getLargestKillingSpree()
    {
        return $this->largestKillingSpree;
    }

    /**
     * @param mixed $largestMultiKill
     */
    public function setLargestMultiKill($largestMultiKill)
    {
        $this->largestMultiKill = $largestMultiKill;
    }

    /**
     * @return mixed
     */
    public function getLargestMultiKill()
    {
        return $this->largestMultiKill;
    }

    /**
     * @param mixed $legendaryItemsCreated
     */
    public function setLegendaryItemsCreated($legendaryItemsCreated)
    {
        $this->legendaryItemsCreated = $legendaryItemsCreated;
    }

    /**
     * @return mixed
     */
    public function getLegendaryItemsCreated()
    {
        return $this->legendaryItemsCreated;
    }

    /**
     * @param mixed $level
     */
    public function setLevel($level)
    {
        $this->level = $level;
    }

    /**
     * @return mixed
     */
    public function getLevel()
    {
        return $this->level;
    }

    /**
     * @param mixed $magicDamageDealtPlayer
     */
    public function setMagicDamageDealtPlayer($magicDamageDealtPlayer)
    {
        $this->magicDamageDealtPlayer = $magicDamageDealtPlayer;
    }

    /**
     * @return mixed
     */
    public function getMagicDamageDealtPlayer()
    {
        return $this->magicDamageDealtPlayer;
    }

    /**
     * @param mixed $magicDamageDealtToChampions
     */
    public function setMagicDamageDealtToChampions($magicDamageDealtToChampions)
    {
        $this->magicDamageDealtToChampions = $magicDamageDealtToChampions;
    }

    /**
     * @return mixed
     */
    public function getMagicDamageDealtToChampions()
    {
        return $this->magicDamageDealtToChampions;
    }

    /**
     * @param mixed $magicDamageTaken
     */
    public function setMagicDamageTaken($magicDamageTaken)
    {
        $this->magicDamageTaken = $magicDamageTaken;
    }

    /**
     * @return mixed
     */
    public function getMagicDamageTaken()
    {
        return $this->magicDamageTaken;
    }

    /**
     * @param mixed $minionsDenied
     */
    public function setMinionsDenied($minionsDenied)
    {
        $this->minionsDenied = $minionsDenied;
    }

    /**
     * @return mixed
     */
    public function getMinionsDenied()
    {
        return $this->minionsDenied;
    }

    /**
     * @param mixed $minionsKilled
     */
    public function setMinionsKilled($minionsKilled)
    {
        $this->minionsKilled = $minionsKilled;
    }

    /**
     * @return mixed
     */
    public function getMinionsKilled()
    {
        return $this->minionsKilled;
    }

    /**
     * @param mixed $neutralMinionsKilled
     */
    public function setNeutralMinionsKilled($neutralMinionsKilled)
    {
        $this->neutralMinionsKilled = $neutralMinionsKilled;
    }

    /**
     * @return mixed
     */
    public function getNeutralMinionsKilled()
    {
        return $this->neutralMinionsKilled;
    }

    /**
     * @param mixed $neutralMinionsKilledEnemyJungle
     */
    public function setNeutralMinionsKilledEnemyJungle($neutralMinionsKilledEnemyJungle)
    {
        $this->neutralMinionsKilledEnemyJungle = $neutralMinionsKilledEnemyJungle;
    }

    /**
     * @return mixed
     */
    public function getNeutralMinionsKilledEnemyJungle()
    {
        return $this->neutralMinionsKilledEnemyJungle;
    }

    /**
     * @param mixed $neutralMinionsKilledYourJungle
     */
    public function setNeutralMinionsKilledYourJungle($neutralMinionsKilledYourJungle)
    {
        $this->neutralMinionsKilledYourJungle = $neutralMinionsKilledYourJungle;
    }

    /**
     * @return mixed
     */
    public function getNeutralMinionsKilledYourJungle()
    {
        return $this->neutralMinionsKilledYourJungle;
    }

    /**
     * @param mixed $nexusKilled
     */
    public function setNexusKilled($nexusKilled)
    {
        $this->nexusKilled = $nexusKilled;
    }

    /**
     * @return mixed
     */
    public function getNexusKilled()
    {
        return $this->nexusKilled;
    }

    /**
     * @param mixed $nodeCapture
     */
    public function setNodeCapture($nodeCapture)
    {
        $this->nodeCapture = $nodeCapture;
    }

    /**
     * @return mixed
     */
    public function getNodeCapture()
    {
        return $this->nodeCapture;
    }

    /**
     * @param mixed $nodeCaptureAssist
     */
    public function setNodeCaptureAssist($nodeCaptureAssist)
    {
        $this->nodeCaptureAssist = $nodeCaptureAssist;
    }

    /**
     * @return mixed
     */
    public function getNodeCaptureAssist()
    {
        return $this->nodeCaptureAssist;
    }

    /**
     * @param mixed $nodeNeutralize
     */
    public function setNodeNeutralize($nodeNeutralize)
    {
        $this->nodeNeutralize = $nodeNeutralize;
    }

    /**
     * @return mixed
     */
    public function getNodeNeutralize()
    {
        return $this->nodeNeutralize;
    }

    /**
     * @param mixed $nodeNeutralizeAssist
     */
    public function setNodeNeutralizeAssist($nodeNeutralizeAssist)
    {
        $this->nodeNeutralizeAssist = $nodeNeutralizeAssist;
    }

    /**
     * @return mixed
     */
    public function getNodeNeutralizeAssist()
    {
        return $this->nodeNeutralizeAssist;
    }

    /**
     * @param mixed $numDeaths
     */
    public function setNumDeaths($numDeaths)
    {
        $this->numDeaths = $numDeaths;
    }

    /**
     * @return mixed
     */
    public function getNumDeaths()
    {
        return $this->numDeaths;
    }

    /**
     * @param mixed $numItemsBought
     */
    public function setNumItemsBought($numItemsBought)
    {
        $this->numItemsBought = $numItemsBought;
    }

    /**
     * @return mixed
     */
    public function getNumItemsBought()
    {
        return $this->numItemsBought;
    }

    /**
     * @param mixed $objectivePlayerScore
     */
    public function setObjectivePlayerScore($objectivePlayerScore)
    {
        $this->objectivePlayerScore = $objectivePlayerScore;
    }

    /**
     * @return mixed
     */
    public function getObjectivePlayerScore()
    {
        return $this->objectivePlayerScore;
    }

    /**
     * @param mixed $pentaKills
     */
    public function setPentaKills($pentaKills)
    {
        $this->pentaKills = $pentaKills;
    }

    /**
     * @return mixed
     */
    public function getPentaKills()
    {
        return $this->pentaKills;
    }

    /**
     * @param mixed $physicalDamageDealtPlayer
     */
    public function setPhysicalDamageDealtPlayer($physicalDamageDealtPlayer)
    {
        $this->physicalDamageDealtPlayer = $physicalDamageDealtPlayer;
    }

    /**
     * @return mixed
     */
    public function getPhysicalDamageDealtPlayer()
    {
        return $this->physicalDamageDealtPlayer;
    }

    /**
     * @param mixed $physicalDamageDealtToChampions
     */
    public function setPhysicalDamageDealtToChampions($physicalDamageDealtToChampions)
    {
        $this->physicalDamageDealtToChampions = $physicalDamageDealtToChampions;
    }

    /**
     * @return mixed
     */
    public function getPhysicalDamageDealtToChampions()
    {
        return $this->physicalDamageDealtToChampions;
    }

    /**
     * @param mixed $physicalDamageTaken
     */
    public function setPhysicalDamageTaken($physicalDamageTaken)
    {
        $this->physicalDamageTaken = $physicalDamageTaken;
    }

    /**
     * @return mixed
     */
    public function getPhysicalDamageTaken()
    {
        return $this->physicalDamageTaken;
    }

    /**
     * @param mixed $private
     */
    public function setPrivate($private)
    {
        $this->private = $private;
    }

    /**
     * @return mixed
     */
    public function getPrivate()
    {
        return $this->private;
    }

    /**
     * @param mixed $quadraKills
     */
    public function setQuadraKills($quadraKills)
    {
        $this->quadraKills = $quadraKills;
    }

    /**
     * @return mixed
     */
    public function getQuadraKills()
    {
        return $this->quadraKills;
    }

    /**
     * @param mixed $sightWardsBought
     */
    public function setSightWardsBought($sightWardsBought)
    {
        $this->sightWardsBought = $sightWardsBought;
    }

    /**
     * @return mixed
     */
    public function getSightWardsBought()
    {
        return $this->sightWardsBought;
    }

    /**
     * @param mixed $spell1Cast
     */
    public function setSpell1Cast($spell1Cast)
    {
        $this->spell1Cast = $spell1Cast;
    }

    /**
     * @return mixed
     */
    public function getSpell1Cast()
    {
        return $this->spell1Cast;
    }

    /**
     * @param mixed $spell2Cast
     */
    public function setSpell2Cast($spell2Cast)
    {
        $this->spell2Cast = $spell2Cast;
    }

    /**
     * @return mixed
     */
    public function getSpell2Cast()
    {
        return $this->spell2Cast;
    }

    /**
     * @param mixed $spell3Cast
     */
    public function setSpell3Cast($spell3Cast)
    {
        $this->spell3Cast = $spell3Cast;
    }

    /**
     * @return mixed
     */
    public function getSpell3Cast()
    {
        return $this->spell3Cast;
    }

    /**
     * @param mixed $spell4Cast
     */
    public function setSpell4Cast($spell4Cast)
    {
        $this->spell4Cast = $spell4Cast;
    }

    /**
     * @return mixed
     */
    public function getSpell4Cast()
    {
        return $this->spell4Cast;
    }

    /**
     * @param mixed $summonSpell1Cast
     */
    public function setSummonSpell1Cast($summonSpell1Cast)
    {
        $this->summonSpell1Cast = $summonSpell1Cast;
    }

    /**
     * @return mixed
     */
    public function getSummonSpell1Cast()
    {
        return $this->summonSpell1Cast;
    }

    /**
     * @param mixed $summonSpell2Cast
     */
    public function setSummonSpell2Cast($summonSpell2Cast)
    {
        $this->summonSpell2Cast = $summonSpell2Cast;
    }

    /**
     * @return mixed
     */
    public function getSummonSpell2Cast()
    {
        return $this->summonSpell2Cast;
    }

    /**
     * @param mixed $superMonsterKilled
     */
    public function setSuperMonsterKilled($superMonsterKilled)
    {
        $this->superMonsterKilled = $superMonsterKilled;
    }

    /**
     * @return mixed
     */
    public function getSuperMonsterKilled()
    {
        return $this->superMonsterKilled;
    }

    /**
     * @param mixed $team
     */
    public function setTeam($team)
    {
        $this->team = $team;
    }

    /**
     * @return mixed
     */
    public function getTeam()
    {
        return $this->team;
    }

    /**
     * @param mixed $teamObjective
     */
    public function setTeamObjective($teamObjective)
    {
        $this->teamObjective = $teamObjective;
    }

    /**
     * @return mixed
     */
    public function getTeamObjective()
    {
        return $this->teamObjective;
    }

    /**
     * @param mixed $timePlayed
     */
    public function setTimePlayed($timePlayed)
    {
        $this->timePlayed = $timePlayed;
    }

    /**
     * @return mixed
     */
    public function getTimePlayed()
    {
        return $this->timePlayed;
    }

    /**
     * @param mixed $totalDamageDealt
     */
    public function setTotalDamageDealt($totalDamageDealt)
    {
        $this->totalDamageDealt = $totalDamageDealt;
    }

    /**
     * @return mixed
     */
    public function getTotalDamageDealt()
    {
        return $this->totalDamageDealt;
    }

    /**
     * @param mixed $totalDamageDealtToChampions
     */
    public function setTotalDamageDealtToChampions($totalDamageDealtToChampions)
    {
        $this->totalDamageDealtToChampions = $totalDamageDealtToChampions;
    }

    /**
     * @return mixed
     */
    public function getTotalDamageDealtToChampions()
    {
        return $this->totalDamageDealtToChampions;
    }

    /**
     * @param mixed $totalDamageTaken
     */
    public function setTotalDamageTaken($totalDamageTaken)
    {
        $this->totalDamageTaken = $totalDamageTaken;
    }

    /**
     * @return mixed
     */
    public function getTotalDamageTaken()
    {
        return $this->totalDamageTaken;
    }

    /**
     * @param mixed $totalHeal
     */
    public function setTotalHeal($totalHeal)
    {
        $this->totalHeal = $totalHeal;
    }

    /**
     * @return mixed
     */
    public function getTotalHeal()
    {
        return $this->totalHeal;
    }

    /**
     * @param mixed $totalPlayerScore
     */
    public function setTotalPlayerScore($totalPlayerScore)
    {
        $this->totalPlayerScore = $totalPlayerScore;
    }

    /**
     * @return mixed
     */
    public function getTotalPlayerScore()
    {
        return $this->totalPlayerScore;
    }

    /**
     * @param mixed $totalScoreRank
     */
    public function setTotalScoreRank($totalScoreRank)
    {
        $this->totalScoreRank = $totalScoreRank;
    }

    /**
     * @return mixed
     */
    public function getTotalScoreRank()
    {
        return $this->totalScoreRank;
    }

    /**
     * @param mixed $totalTimeCrowdControlDealt
     */
    public function setTotalTimeCrowdControlDealt($totalTimeCrowdControlDealt)
    {
        $this->totalTimeCrowdControlDealt = $totalTimeCrowdControlDealt;
    }

    /**
     * @return mixed
     */
    public function getTotalTimeCrowdControlDealt()
    {
        return $this->totalTimeCrowdControlDealt;
    }

    /**
     * @param mixed $totalUnitsHealed
     */
    public function setTotalUnitsHealed($totalUnitsHealed)
    {
        $this->totalUnitsHealed = $totalUnitsHealed;
    }

    /**
     * @return mixed
     */
    public function getTotalUnitsHealed()
    {
        return $this->totalUnitsHealed;
    }

    /**
     * @param mixed $tripleKills
     */
    public function setTripleKills($tripleKills)
    {
        $this->tripleKills = $tripleKills;
    }

    /**
     * @return mixed
     */
    public function getTripleKills()
    {
        return $this->tripleKills;
    }

    /**
     * @param mixed $trueDamageDealtPlayer
     */
    public function setTrueDamageDealtPlayer($trueDamageDealtPlayer)
    {
        $this->trueDamageDealtPlayer = $trueDamageDealtPlayer;
    }

    /**
     * @return mixed
     */
    public function getTrueDamageDealtPlayer()
    {
        return $this->trueDamageDealtPlayer;
    }

    /**
     * @param mixed $trueDamageDealtToChampions
     */
    public function setTrueDamageDealtToChampions($trueDamageDealtToChampions)
    {
        $this->trueDamageDealtToChampions = $trueDamageDealtToChampions;
    }

    /**
     * @return mixed
     */
    public function getTrueDamageDealtToChampions()
    {
        return $this->trueDamageDealtToChampions;
    }

    /**
     * @param mixed $trueDamageTaken
     */
    public function setTrueDamageTaken($trueDamageTaken)
    {
        $this->trueDamageTaken = $trueDamageTaken;
    }

    /**
     * @return mixed
     */
    public function getTrueDamageTaken()
    {
        return $this->trueDamageTaken;
    }

    /**
     * @param mixed $turretsKilled
     */
    public function setTurretsKilled($turretsKilled)
    {
        $this->turretsKilled = $turretsKilled;
    }

    /**
     * @return mixed
     */
    public function getTurretsKilled()
    {
        return $this->turretsKilled;
    }

    /**
     * @param mixed $unrealKills
     */
    public function setUnrealKills($unrealKills)
    {
        $this->unrealKills = $unrealKills;
    }

    /**
     * @return mixed
     */
    public function getUnrealKills()
    {
        return $this->unrealKills;
    }

    /**
     * @param mixed $victoryPoTotal
     */
    public function setVictoryPoTotal($victoryPoTotal)
    {
        $this->victoryPoTotal = $victoryPoTotal;
    }

    /**
     * @return mixed
     */
    public function getVictoryPoTotal()
    {
        return $this->victoryPoTotal;
    }

    /**
     * @param mixed $visionWardsBought
     */
    public function setVisionWardsBought($visionWardsBought)
    {
        $this->visionWardsBought = $visionWardsBought;
    }

    /**
     * @return mixed
     */
    public function getVisionWardsBought()
    {
        return $this->visionWardsBought;
    }

    /**
     * @param mixed $wardKilled
     */
    public function setWardKilled($wardKilled)
    {
        $this->wardKilled = $wardKilled;
    }

    /**
     * @return mixed
     */
    public function getWardKilled()
    {
        return $this->wardKilled;
    }

    /**
     * @param mixed $wardPlaced
     */
    public function setWardPlaced($wardPlaced)
    {
        $this->wardPlaced = $wardPlaced;
    }

    /**
     * @return mixed
     */
    public function getWardPlaced()
    {
        return $this->wardPlaced;
    }

    /**
     * @param mixed $win
     */
    public function setWin($win)
    {
        $this->win = $win;
    }

    /**
     * @return mixed
     */
    public function getWin()
    {
        return $this->win;
    }


} 