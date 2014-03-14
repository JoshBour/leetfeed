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

    public function register($data){
        $form = $this->getRegisterForm();
        $account = new \Account\Entity\Account();
        $disabledUsernames = $this->getServiceManager()->get('Config')['disabled_usernames'];


        $form->bind($account);
        $form->setData($data);
        if(!$form->isValid()){
            return false;
        }
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
     * Retrieve the account plugin
     *
     * @return \Account\Plugin\ActiveAccount
     */
    public function getAccount(){
        if(null === $this->account)
            $this->account = $this->getServiceManager()->get('ControllerPluginManager')->get('account')->getActiveAccount();
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
     * Retrieve the account repository
     *
     * @return EntityRepository
     */
    public function getAccountRepository(){
        if(null === $this->accountRepository)
            $this->accountRepository = $this->getEntityManager()->getRepository('\Account\Entity\Account');
        return $this->accountRepository;
    }

} 