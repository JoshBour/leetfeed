<?php
/**
 * Created by PhpStorm.
 * User: Josh
 * Date: 12/3/2014
 * Time: 6:51 μμ
 */

namespace League\Service;

use League\Model\SummonerDto;
use Zend\ServiceManager\ServiceManager;
use Zend\ServiceManager\ServiceManagerAwareInterface;

class League implements ServiceManagerAwareInterface
{
    const API_URL_1_1 = 'https://prod.api.pvp.net/api/lol/{region}/v1.1/';
    const API_URL_1_2 = 'https://prod.api.pvp.net/api/lol/{region}/v1.2/';
    const API_URL_1_3 = 'https://prod.api.pvp.net/api/lol/{region}/v1.3/';
    const API_URL_2_1 = 'https://prod.api.pvp.net/api/lol/{region}/v2.2/';
    const RATE_LIMIT_MINUTES = 500;
    const RATE_LIMIT_SECONDS = 10;
    const CACHE_LIFETIME_MINUTES = 60;
    const CACHE_ENABLED = true;
    const ERROR_NOT_SUPPORTED_REGION = "The region you selected is not supported.";

    public static $supportedRegions = array('br', 'eune', 'euw', 'lan', 'las', 'na', 'oce');

    private $apiKey;

    /**
     * @var ServiceManager
     */
    private $serviceManager;

    private $cacheService;

    /**
     * @param \League\Entity\Summoner $summoner
     * @return array
     */
    public function getLatestChampions($summoner)
    {
        // we get the latest champions that the player needs to improve
        $games = $this->getGames($summoner->getLolId(), $summoner->getRegion());
        $champions = array();
        if (!empty($games)) {
            foreach ($games as $game) {
                $champions[] = $game->getChampion();
            }
        }
        return array_unique($champions);
    }

    public function getChampions($region)
    {
        if (!in_array($region, self::$supportedRegions)) throw new \Exception(self::ERROR_NOT_SUPPORTED_REGION);
        $call = 'champion';

        //add API URL to the call
        $call = self::API_URL_1_1 . $call;
        $response = json_decode($this->performRequest($call, $region), true);
        return $response;
    }

    public function getGames($id, $region)
    {
        if (!in_array($region, self::$supportedRegions)) throw new \Exception(self::ERROR_NOT_SUPPORTED_REGION);
        $call = 'game/by-summoner/' . $id . '/recent';

        //add API URL to the call
        $call = self::API_URL_1_3 . $call;

        $response = json_decode($this->performRequest($call, $region), true);
        $gameDtoList = array();
        if (!empty($response['games'])) {
            foreach ($response['games'] as $game) {
                $gameDtoList[] = new \League\Model\GameDto($game);
            }
        }
        return $gameDtoList;
    }

    public function getSummoner($name, $region)
    {
        if (!in_array($region, self::$supportedRegions)) throw new \Exception(self::ERROR_NOT_SUPPORTED_REGION);
        //sanitize name a bit - this will break weird characters
      #  $name = preg_replace("/[^a-zA-Z0-9 ]+/", "", $name);
        $name = str_replace(' ','',$name);
        $call = 'summoner/by-name/' . $name;

        //add API URL to the call
        $call = self::API_URL_1_3 . $call;
        $response = json_decode($this->performRequest($call, $region), true);
        return new SummonerDto($response[strtolower($name)]);
    }

    private function performRequest($call, $region)
    {

        //probably should put rate limiting stuff here


        //format the full URL
        $url = $this->format_url($call, $region);

        //caching
        if (self::CACHE_ENABLED) {
            $cacheFile = dirname(__FILE__) . '/../../../data/cache/' . md5($url);

            if (file_exists($cacheFile)) {
                $fh = fopen($cacheFile, 'r');
                $cacheTime = trim(fgets($fh));

                // if data was cached recently, return cached data
                if ($cacheTime > strtotime('-' . self::CACHE_LIFETIME_MINUTES . ' minutes')) {
                    return fread($fh, filesize($cacheFile));
                }

                // else delete cache file
                fclose($fh);
                unlink($cacheFile);
            }
        }

        //call the API and return the result
        $curl = curl_init($url);
        curl_setopt($curl, CURLOPT_RETURNTRANSFER, 1);
        curl_setopt($curl, CURLOPT_SSL_VERIFYPEER, true);
        curl_setopt($curl, CURLOPT_CAINFO, dirname(__FILE__) . '/cacert.pem');

        $response = curl_exec($curl);
        if ($response === false) {
            throw new \Exception(curl_error($curl));
        }
        curl_close($curl);

        if (self::CACHE_ENABLED) {
            //create cache file
            $fh = fopen($cacheFile, 'w');
            fwrite($fh, time() . "\n");
            fwrite($fh, $response);
            fclose($fh);
        }

        return $response;

    }

    //creates a full URL you can query on the API
    private function format_url($call, $region)
    {
        return str_replace('{region}', $region, $call) . '?api_key=' . $this->getApiKey();
    }

    public function getCacheService()
    {
        if (null === $this->cacheService)
            $this->cacheService = $this->getServiceManager()->get('cache_service');
        return $this->cacheService;
    }

    public function getApiKey()
    {
        if (null === $this->apiKey)
            $this->apiKey = $this->getServiceManager()->get('config')['api']['key'];
        return $this->apiKey;
    }

    /**
     * Retrieve service manager instance
     *
     * @return ServiceManager
     */
    public function getServiceManager()
    {
        return $this->serviceManager;
    }

    /**
     * Set service manager instance
     *
     * @param ServiceManager $serviceManager
     * @return Youtube
     */
    public function setServiceManager(ServiceManager $serviceManager)
    {
        $this->serviceManager = $serviceManager;
        return $this;
    }
} 