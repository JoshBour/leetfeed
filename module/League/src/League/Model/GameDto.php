<?php
/**
 * Created by PhpStorm.
 * User: Josh
 * Date: 15/3/2014
 * Time: 8:13 μμ
 */

namespace League\Model;

class GameDto
{

    private static $staticFields = array('championId', 'createDate', 'gameId',
        'gameMode', 'gameType', 'invalid',
        'level', 'mapId', 'spell1',
        'spell2', 'subType', 'teamId');

    private static $dynamicFields = array(
        'fellowPlayers' => '\League\Model\PlayerDto',
        'stats' => '\League\Model\RawStatsDto'
    );

    private static $championList = array(
        '266' => 'Aatrox', '103' => 'Ahri', '84' => 'Akali', '12' => 'Alistar',
        '32' => 'Amumu', '34' => 'Anivia', '1' => 'Annie', '22' => 'Ashe',
        '53' => 'Blitzcrank', '63' => 'Brand', '51' => 'Caitlyn',
        '69' => 'Cassiopeia', '31' => 'Chogath', '42' => 'Corki',
        '122' => 'Darius', '131' => 'Diana', '119' => 'Draven',
        '36' => 'DrMundo', '60' => 'Elise', '28' => 'Evelynn',
        '81' => 'Ezreal', '9' => 'FiddleSticks', '114' => 'Fiora',
        '105' => 'Fizz', '3' => 'Galio', '41' => 'Gangplank',
        '86' => 'Garen', '79' => 'Gragas', '104' => 'Graves',
        '120' => 'Hecarim', '74' => 'Heimerdinger', '39' => 'Irelia',
        '40' => 'Janna', '59' => 'JarvanIV', '24' => 'Jax', '126' => 'Jayce',
        '222' => 'Jinx', '43' => 'Karma', '30' => 'Karthus', '38' => 'Kassadin',
        '55' => 'Katarina', '10' => 'Kayle', '85' => 'Kennen', '121' => 'Khazix',
        '96' => 'KogMaw', '7' => 'Leblanc', '64' => 'LeeSin', '89' => 'Leona',
        '127' => 'Lissandra', '236' => 'Lucian', '117' => 'Lulu', '99' => 'Lux',
        '54' => 'Malphite', '90' => 'Malzahar', '57' => 'Maokai', '11' => 'MasterYi',
        '21' => 'MissFortune', '62' => 'MonkeyKing', '82' => 'Mordekaiser', '25' => 'Morgana',
        '267' => 'Nami', '75' => 'Nasus', '111' => 'Nautilus', '76' => 'Nidalee', '56' => 'Nocturne',
        '20' => 'Nunu', '2' => 'Olaf', '61' => 'Orianna', '80' => 'Pantheon', '78' => 'Poppy',
        '133' => 'Quinn', '33' => 'Rammus', '58' => 'Renekton', '107' => 'Rengar', '92' => 'Riven',
        '68' => 'Rumble', '13' => 'Ryze', '113' => 'Sejuani', '35' => 'Shaco', '98' => 'Shen',
        '102' => 'Shyvana', '27' => 'Singed', '14' => 'Sion', '15' => 'Sivir', '72' => 'Skarner',
        '37' => 'Sona', '16' => 'Soraka', '50' => 'Swain', '134' => 'Syndra', '91' => 'Talon',
        '44' => 'Taric', '17' => 'Teemo', '412' => 'Thresh', '18' => 'Tristana', '48' => 'Trundle',
        '23' => 'Tryndamere', '4' => 'TwistedFate', '29' => 'Twitch', '77' => 'Udyr', '6' => 'Urgot',
        '110' => 'Varus', '67' => 'Vayne', '45' => 'Veigar', '161' => 'Velkoz', '254' => 'Vi', '112' => 'Viktor',
        '8' => 'Vladimir', '106' => 'Volibear', '19' => 'Warwick', '101' => 'Xerath', '5' => 'XinZhao', '157' => 'Yasuo',
        '83' => 'Yorick', '154' => 'Zac', '238' => 'Zed', '115' => 'Ziggs', '26' => 'Zilean', '143' => 'Zyra'
    );

    /**
     * @var int
     */
    private $championId;

    /**
     * @var double
     */
    private $createDate;

    /**
     * @var array
     */
    private $fellowPlayers;

    /**
     * @var double
     */
    private $gameId;

    /**
     * @var string
     */
    private $gameMode;

    /**
     * @var string
     */
    private $gameType;

    /**
     * @var boolean
     */
    private $invalid;

    /**
     * @var int
     */
    private $level;

    /**
     * @var int
     */
    private $mapId;

    /**
     * @var int
     */
    private $spell1;

    /**
     * @var int
     */
    private $spell2;

    /**
     * @var RawStatsDto
     */
    private $stats;

    /**
     * @var string
     */
    private $subType;

    /**
     * @var int
     */
    private $teamId;

    /**
     * @param array $game
     */
    public function __construct($game)
    {
        // assign the "static" variables that are not equal to a class
        foreach (self::$staticFields as $field) {
            if (isset($game[$field]))
                $this->{$field} = $game[$field];
        }

        if (isset($game['fellowPlayers']))
            foreach ($game['fellowPlayers'] as $player)
                $this->fellowPlayers[] = new PlayerDto(($player));

        if (isset($game['stats']))
            $this->stats = new RawStatsDto($game['stats']);
    }

    public function getChampion()
    {
        return self::$championList[$this->championId];
    }

    /**
     * @param int $championId
     */
    public function setChampionId($championId)
    {
        $this->championId = $championId;
    }

    /**
     * @return int
     */
    public function getChampionId()
    {
        return $this->championId;
    }

    /**
     * @param float $createDate
     */
    public function setCreateDate($createDate)
    {
        $this->createDate = $createDate;
    }

    /**
     * @return float
     */
    public function getCreateDate()
    {
        return $this->createDate;
    }

    /**
     * @param array $dynamicFields
     */
    public static function setDynamicFields($dynamicFields)
    {
        self::$dynamicFields = $dynamicFields;
    }

    /**
     * @return array
     */
    public static function getDynamicFields()
    {
        return self::$dynamicFields;
    }

    /**
     * @param array $fellowPlayers
     */
    public function setFellowPlayers($fellowPlayers)
    {
        $this->fellowPlayers = $fellowPlayers;
    }

    /**
     * @return array
     */
    public function getFellowPlayers()
    {
        return $this->fellowPlayers;
    }

    /**
     * @param float $gameId
     */
    public function setGameId($gameId)
    {
        $this->gameId = $gameId;
    }

    /**
     * @return float
     */
    public function getGameId()
    {
        return $this->gameId;
    }

    /**
     * @param string $gameMode
     */
    public function setGameMode($gameMode)
    {
        $this->gameMode = $gameMode;
    }

    /**
     * @return string
     */
    public function getGameMode()
    {
        return $this->gameMode;
    }

    /**
     * @param string $gameType
     */
    public function setGameType($gameType)
    {
        $this->gameType = $gameType;
    }

    /**
     * @return string
     */
    public function getGameType()
    {
        return $this->gameType;
    }

    /**
     * @param boolean $invalid
     */
    public function setInvalid($invalid)
    {
        $this->invalid = $invalid;
    }

    /**
     * @return boolean
     */
    public function getInvalid()
    {
        return $this->invalid;
    }

    /**
     * @param int $level
     */
    public function setLevel($level)
    {
        $this->level = $level;
    }

    /**
     * @return int
     */
    public function getLevel()
    {
        return $this->level;
    }

    /**
     * @param int $mapId
     */
    public function setMapId($mapId)
    {
        $this->mapId = $mapId;
    }

    /**
     * @return int
     */
    public function getMapId()
    {
        return $this->mapId;
    }

    /**
     * @param int $spell1
     */
    public function setSpell1($spell1)
    {
        $this->spell1 = $spell1;
    }

    /**
     * @return int
     */
    public function getSpell1()
    {
        return $this->spell1;
    }

    /**
     * @param int $spell2
     */
    public function setSpell2($spell2)
    {
        $this->spell2 = $spell2;
    }

    /**
     * @return int
     */
    public function getSpell2()
    {
        return $this->spell2;
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
     * @param \League\Model\RawStatsDto $stats
     */
    public function setStats($stats)
    {
        $this->stats = $stats;
    }

    /**
     * @return \League\Model\RawStatsDto
     */
    public function getStats()
    {
        return $this->stats;
    }

    /**
     * @param string $subType
     */
    public function setSubType($subType)
    {
        $this->subType = $subType;
    }

    /**
     * @return string
     */
    public function getSubType()
    {
        return $this->subType;
    }

    /**
     * @param int $teamId
     */
    public function setTeamId($teamId)
    {
        $this->teamId = $teamId;
    }

    /**
     * @return int
     */
    public function getTeamId()
    {
        return $this->teamId;
    }


}