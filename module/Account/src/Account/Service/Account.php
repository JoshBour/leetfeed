<?php
namespace Account\Service;


use Zend\ServiceManager\ServiceManagerAwareInterface;
use Zend\ServiceManager\ServiceManager;
use Doctrine\ORM\EntityManager;
use Doctrine\ORM\EntityRepository;

class Account implements ServiceManagerAwareInterface{

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
        $account = $this->getActiveAccount();

        $form->bind($account);
        $form->setData($data);
        if(!$form->isValid()){
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
     * @return bool|\Account\Entity\Account|null|object
     */
    public function getActiveAccount(){
        $session = new \Zend\Session\Container('account');
        $ip = $_SERVER["REMOTE_ADDR"];
        if(isset($session->accountId)){
            $account = $this->getAccountRepository()->find($session->accountId);
        }else{
            $account = $this->getAccountRepository()->findOneBy(array('ip' => $ip));
        }
        if($account){
            if($account->getIp() != $ip){
                $account->setIp($ip);
            }
        }else{
            $account = $this->create($ip);
        }
        $session->accountId = $account->getAccountId();
        return $account;
    }

    public function updateLastSeen(&$account){
        $account->setLastSeen(date("Y-m-d H:i:s", time()));
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