<?php
/**
 * User: Josh
 * Date: 12/9/2013
 * Time: 7:14 μμ
 */

namespace Account\View\Helper;

use Zend\View\Helper\AbstractHelper;

class Account extends AbstractHelper
{

    private $serviceManager;

    private $accountService;

    private $entityManager;

    private $authService;

    public function __invoke(){
        return $this->getAccount();
    }

    /**
     * @return bool|\Account\Entity\Account
     */
    public function getAccount(){
        $em = $this->getEntityManager();
        $auth = $this->getAuthService();
        $accService = $this->getAccountService();
        if($auth->hasIdentity()){
            $account = $em->getRepository('Account\Entity\Account')->find($auth->getIdentity()->getAccountId());

            $accService->updateLastSeen($account);
            $em->persist($account);
            $em->flush();
        }else{
            $account = false;
        }
        return $account;
    }

    public function getAuthService()
    {
        if (null === $this->authService)
            $this->authService = $this->getServiceManager()->get('auth_service');
        return $this->authService;
    }

    /**
     * @return \Account\Service\Account
     */
    public function getAccountService()
    {
        if (null === $this->accountService)
            $this->accountService = $this->getServiceManager()->get("account_service");
        return $this->accountService;
    }

    /**
     * @return \Doctrine\ORM\EntityManager
     */
    public function getEntityManager()
    {
        if (null === $this->entityManager)
            $this->entityManager = $this->getServiceManager()->get('Doctrine\ORM\EntityManager');
        return $this->entityManager;
    }

    public function setServiceManager($sm)
    {
        $this->serviceManager = $sm;
    }

    /**
     * @return \Zend\ServiceManager\ServiceManager
     */
    public function getServiceManager()
    {
        return $this->serviceManager;
    }
}