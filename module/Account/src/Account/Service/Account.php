<?php
namespace Account\Service;


use Zend\ServiceManager\ServiceManagerAwareInterface;
use Zend\ServiceManager\ServiceManager;
use Doctrine\ORM\EntityManager;
use Doctrine\ORM\EntityRepository;

class Account implements ServiceManagerAwareInterface{

    public static $error = null;

    private $account;

    /**
     * var EntityManager
     */
    private $entityManager;

    /**
     * @var ServiceManager
     */
    private $serviceManager;

    /**
     * @var EntityRepository
     */
    private $accountRepository;

    /**
     * @var \Zend\Form\Form
     */
    private $registerForm;

    /**
     * @var \League\Service\League
     */
    private $leagueService;

    private $groupRepository;

    private $summonerRepository;

    public function removeSummoner($summonerId){
        $summoner = $this->getSummonerRepository()->find($summonerId);
        if($summoner){
            $em = $this->getEntityManager();
            $account = $this->getAccount();
            $account->removeSummoners($summoner);
            try{
                $em->remove($summoner);
                $em->persist($account);
                $em->flush();
                return true;
            }catch (\Exception $e){
                return false;
            }
        }
        return false;
    }

    public function addSummoner($data){
        $name = $data['summoner']['name'];
        $region = $data['summoner']['region'];
        $leagueService = $this->getLeagueService();
        $summoner = $leagueService->getSummoner($name,$region);
        if($summoner){
            $account = $this->getAccount();
            $em = $this->getEntityManager();
            $entity = new \League\Entity\Summoner($account,$summoner->getId(),$name,$region);
            $account->addSummoners($entity);
            try{
                $em->persist($entity);
                $em->persist($account);
                $em->flush();
                return true;
            }catch (\Exception $e){
                return false;
            }
        }
        return false;
    }

    public function register($data){
        $form = $this->getRegisterForm();
        $account = new \Account\Entity\Account();
        $disabledUsernames = $this->getServiceManager()->get('Config')['disabled_usernames'];
        $group = $this->getGroupRepository()->find(2);

        $form->bind($account);
        $form->setData($data);
        if(!$form->isValid()){
            return false;
        }
        $account->addGroups($group);
        $account->setIp($_SERVER["REMOTE_ADDR"]);
        $account->setFirstSeen(date("Y-m-d H:i:s", time()));
        if(in_array(strtolower($account->getUsername()),$disabledUsernames)){
            self::$error = "The username is not allowed, please select a new one.";
            return false;
        }
        $account->setPassword(\Account\Entity\Account::getHashedPassword($account->getPassword()));
        $em = $this->getEntityManager();
        try{
            $em->persist($account);
            $em->flush();

            return $account;
        }catch(\Exception $e){
            return false;
        }
    }

    /**
     * @param \Account\Entity\Account $account
     * @param bool $flush
     */
    public function updateLastSeen(&$account,$flush = false){
        $account->setLastSeen(date("Y-m-d H:i:s", time()));
        if($flush){
            $em = $this->getEntityManager();
            $em->persist($account);
            $em->flush();
        }
    }

    public function create($ip){
        $em = $this->getEntityManager();
        $account = new \Account\Entity\Account();
        $account->setIp($ip);
        $account->setFirstSeen(date("Y-m-d H:i:s", time()));
        try{
            $em->persist($account);
            $em->flush();
            return $account;
        }catch (\Exception $e){
            return false;
        }
    }

    /**
     * Retrieve the active account
     *
     * @return \Account\Entity\Account
     */
    public function getAccount(){
        if(null === $this->account)
            $this->account = $this->getServiceManager()->get('ControllerPluginManager')->get('account')->getAccount();
        return $this->account;
    }

    public function getRegisterForm(){
        if(null === $this->registerForm)
            $this->registerForm = $this->getServiceManager()->get('account_register_form');
        return $this->registerForm;
    }

    /**
     * Retrieve the doctrine entity manager
     *
     * @return \Doctrine\ORM\EntityManager
     */
    public function getEntityManager(){
        if(null === $this->entityManager){
            $this->entityManager = $this->getServiceManager()->get('Doctrine\ORM\EntityManager');
        }
        return $this->entityManager;
    }

    /**
     * Set the doctrine entity manager
     *
     * @param EntityManager $entityManager
     * @return Account
     */
    public function setEntityManager(EntityManager $entityManager){
        $this->entityManager = $entityManager;
        return $this;
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
     * @return Account
     */
    public function setServiceManager(ServiceManager $serviceManager)
    {
        $this->serviceManager = $serviceManager;
        return $this;
    }

    /**
     * Retrieve the group repository
     *
     * @return EntityRepository
     */
    public function getGroupRepository(){
        if(null === $this->groupRepository)
            $this->groupRepository = $this->getEntityManager()->getRepository('\Account\Entity\Group');
        return $this->groupRepository;
    }


    /**
     * Retrieve the summoner repository
     *
     * @return EntityRepository
     */
    public function getSummonerRepository(){
        if(null === $this->summonerRepository)
            $this->summonerRepository = $this->getEntityManager()->getRepository('\League\Entity\Summoner');
        return $this->summonerRepository;
    }

    /**
     * Retrieve the account repository
     *
     * @return EntityRepository
     */
    public function getAccountRepository(){
        if(null === $this->accountRepository)
            $this->accountRepository = $this->getEntityManager()->getRepository('\Account\Entity\Account');
        return $this->accountRepository;
    }

    /**
     * Retrieve the league service
     *
     * @return \League\Service\League
     */
    public function getLeagueService(){
        if(null === $this->leagueService)
            $this->leagueService = $this->getServiceManager()->get('league_service');
        return $this->leagueService;
    }

} 