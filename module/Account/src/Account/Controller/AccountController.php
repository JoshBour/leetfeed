<?php
namespace Account\Controller;

use Account\Entity\Account;
use Zend\Authentication\AuthenticationService;
use Zend\Form\Form;
use Zend\Mvc\Controller\AbstractActionController;
use Zend\View\Model\JsonModel;
use Zend\View\Model\ViewModel;

class AccountController extends AbstractActionController
{
    const CONTROLLER_NAME = 'account_controller';

    const ROUTE_LOGIN = 'login';
    const ROUTE_REGISTER = 'register';
    const ROUTE_AUTHENTICATE = 'authenticate';
    const ROUTE_HOMEPAGE = 'home';

    const TEMPLATE_SUMMONERS_AJAX = "account/account/summoners.ajax.phtml";

    const MESSAGE_ACCOUNT_CREATED = 'Your account has been created successfully!';
    const MESSAGE_INVALID_CREDENTIALS = 'The username/password combination is invalid.';
    const MESSAGE_ALREADY_LOGGED = "You are already logged in!";
    const MESSAGE_LOGOUT_TO_REGISTER = "You have to logout in order to register a new account!";
    const MESSAGE_SUMMONER_ADDED = "The summoner has been added successfully!";
    const MESSAGE_REMOVED_SUMMONER = "The summoner has been removed successfully!";
    const ERROR_SUMMONER_NOT_FOUND = "The summoner was not found, please verify the info and try again!";
    const ERROR_REMOVE_SUMMONER = "There was an error when removing the summoner, please try again!";

    /**
     * The account service.
     *
     * @var \Account\Service\Account
     */
    private $accountService = null;

    /**
     * The authentication service.
     *
     * @var AuthenticationService
     */
    private $authService = null;

    /**
     * The authentication storage.
     *
     * @var \Account\Model\AuthStorage
     */
    private $authStorage = null;

    /**
     * The entity manager.
     *
     * @var \Doctrine\ORM\EntityManager
     */
    private $entityManager;

    /**
     * The league service.
     *
     * @var \League\Service\League
     */
    private $leagueService;

    /**
     * The login form.
     *
     * @var Form
     */
    private $loginForm = null;

    /**
     * The register form.
     *
     * @var Form
     */
    private $registerForm = null;


    /**
     * The summoner form
     *
     * @var Form
     */
    private $summonerForm;

    /**
     * The zend translator.
     *
     * @var \Zend\I18n\Translator\Translator
     */
    private $translator;


    /**
     * The summoners action
     * Route: /summoners
     *
     * @return JsonModel|ViewModel
     */
    public function summonersAction()
    {
        if ($this->identity()) {
            /**
             * @var $request \Zend\Http\Request
             */
            $request = $this->getRequest();
            if ($request->isXmlHttpRequest()) {
                $viewModel = new ViewModel();
                $viewModel->setTerminal(true);
                $viewModel->setTemplate(self::TEMPLATE_SUMMONERS_AJAX);
                $form = $this->getSummonerForm();
                $data = $request->getPost();
                $form->setData($data);
                if ($form->isValid()) {
                    if ($this->getAccountService()->addSummoner($data)) {
                        $this->flashMessenger()->addMessage(self::MESSAGE_SUMMONER_ADDED);
                        return new JsonModel(array(
                            "redirect" => true
                        ));
                    } else {
                        $viewModel->setVariable("error", self::ERROR_SUMMONER_NOT_FOUND);
                    }
                }
                $viewModel->setVariable("form", $form);
                return $viewModel;
            } else {
                return new ViewModel(array(
                    "summoners" => $this->account()->getSummoners(),
                    "form" => $this->getSummonerForm(),
                    'leagueService' => $this->getLeagueService(),
                    "includeAjaxForm" => true,
                ));
            }
        }else{
            return $this->notFoundAction();
        }
    }

    /**
     * The remove summoner action.
     * Only accessed via xmlHttpRequest.
     * Route: /account/remove-summoner
     *
     * @return JsonModel
     */
    public function removeSummonerAction()
    {
        if ($this->getRequest()->isXmlHttpRequest() && $this->identity()) {
            $summonerId = $this->params()->fromPost("summonerId", null);
            $success = 0;
            if ($this->getAccountService()->removeSummoner($summonerId)) {
                $success = 1;
                $message = self::MESSAGE_REMOVED_SUMMONER;
            } else {
                $message = self::ERROR_REMOVE_SUMMONER;
            }
            return new JsonModel(array(
                "success" => $success,
                "message" => $message
            ));
        }else{
            return $this->notFoundAction();
        }
    }

    /**
     * The login action
     * Route: /login
     *
     * @return mixed|ViewModel
     */
    public function loginAction()
    {
        if (!$this->identity()) {
            $entity = new Account();
            $loginForm = $this->getLoginForm();
            /**
             * @var $request \Zend\Http\Request
             */
            $request = $this->getRequest();
            $loginForm->bind($entity);
            if ($request->isPost()) {
                $data = $request->getPost();
                $loginForm->setData($data);
                if ($loginForm->isValid()) {
                    $redirectUrl = $this->params()->fromRoute("redirectUrl", null);
                    return $this->forward()->dispatch(static::CONTROLLER_NAME, array('action' => 'authenticate',
                        'username' => $entity->getUsername(),
                        'password' => $entity->getPassword(),
                        'remember' => $data['account']['remember'],
                        'redirectUrl' => $redirectUrl));
                }
            }
            return new ViewModel(array(
                'form' => $loginForm,
            ));
        } else {
            $this->flashMessenger()->addMessage($this->getTranslator()->translate(self::MESSAGE_ALREADY_LOGGED));
            return $this->redirect()->toRoute(self::ROUTE_HOMEPAGE);
        }
    }

    /**
     * The logout action
     * Route: /logout
     *
     * @return \Zend\Http\Response
     */
    public function logoutAction()
    {
        if ($this->identity()) {
            $this->getAuthStorage()->forgetMe();
            $this->getAuthenticationService()->clearIdentity();
        }
        return $this->redirect()->toRoute(static::ROUTE_LOGIN);
    }

    /**
     * The register action.
     * Route: /register
     *
     * @return mixed|ViewModel
     */
    public function registerAction()
    {
        if (!$this->identity()) {
            $service = $this->getAccountService();
            /**
             * @var $request \Zend\Http\Request
             */
            $request = $this->getRequest();
            $form = $this->getRegisterForm();
            $data = $request->getPost();
            if ($request->isPost()) {
                $account = $service->register($data);
                if ($account) {
                    $this->flashMessenger()->addMessage($this->getTranslator()->translate(static::MESSAGE_ACCOUNT_CREATED));
                    return $this->forward()->dispatch(static::CONTROLLER_NAME, array('action' => 'authenticate',
                            'username' => $account->getUsername(),
                            'password' => $data['account']['password'])
                    );
                } else {
                    if (\Account\Service\Account::$error) {
                        $this->flashMessenger()->addMessage(\Account\Service\Account::$error);
                        return $this->redirect()->toRoute(self::ROUTE_REGISTER);
                    }
                }
            }
            return new ViewModel(array(
                'form' => $form,
            ));
        } else {
            $this->flashMessenger()->addMessage($this->getTranslator()->translate(self::MESSAGE_LOGOUT_TO_REGISTER));
            return $this->redirect()->toRoute(self::ROUTE_HOMEPAGE);
        }
    }

    /**
     * The authentication action.
     * Only accessed from the login and register actions.
     *
     * @return \Zend\Http\Response
     */
    public function authenticateAction()
    {
        $authService = $this->getAuthenticationService();
        /**
         * @var $adapter \Zend\Authentication\Adapter\AbstractAdapter
         */
        $adapter = $authService->getAdapter();

        $remember = $this->params()->fromRoute('remember', 1);
        $username = $this->params()->fromRoute('username');
        $password = $this->params()->fromRoute('password');
        $redirectUrl = $this->params()->fromRoute('redirectUrl');
        $adapter->setIdentityValue($username);
        $adapter->setCredentialValue($password);
        $authResult = $authService->authenticate();
        if ($authResult->isValid()) {
            if ($remember == 1) {
                $this->getAuthStorage()->setRememberMe(1);
                $authService->setStorage($this->getAuthStorage());
            }
            $identity = $authResult->getIdentity();
            $this->getAccountService()->updateLastSeen($identity, true);
            $authService->getStorage()->write($identity);
        } else {
            $this->flashMessenger()->addMessage($this->getTranslator()->translate(self::MESSAGE_INVALID_CREDENTIALS));
            return $this->redirect()->toRoute(self::ROUTE_LOGIN);
        }
        if ($redirectUrl) {
            $redirectUrl = str_replace('__', '/', $redirectUrl);
            return $this->redirect()->toUrl('/' . $redirectUrl);
        } else {
            return $this->redirect()->toRoute(self::ROUTE_HOMEPAGE);
        }
    }

    /**
     * Retrieve the account service
     *
     * @return \Account\Service\Account
     */
    public function getAccountService()
    {
        if (null === $this->accountService) {
            $this->accountService = $this->getServiceLocator()->get('account_service');
        }
        return $this->accountService;
    }

    /**
     * Retrieve the authentication service
     *
     * @return AuthenticationService
     */
    public function getAuthenticationService()
    {
        if (null === $this->authService) {
            $this->authService = $this->getServiceLocator()->get('auth_service');
        }
        return $this->authService;
    }

    /**
     * Retrieve the auth storage
     *
     * @return \Account\Model\AuthStorage
     */
    public function getAuthStorage()
    {
        if (null === $this->authStorage) {
            $this->authStorage = $this->getServiceLocator()->get('authStorage');
        }
        return $this->authStorage;
    }

    /**
     * Retrieve the doctrine entity manager
     *
     * @return \Doctrine\ORM\EntityManager
     */
    public function getEntityManager()
    {
        if (null === $this->entityManager) {
            $this->entityManager = $this->getServiceLocator()->get('Doctrine\ORM\EntityManager');
        }
        return $this->entityManager;
    }

    /**
     * Retrieve the league service
     *
     * @return \League\Service\League
     */
    public function getLeagueService()
    {
        if (null === $this->leagueService) {
            $this->leagueService = $this->getServiceLocator()->get('league_service');
        }
        return $this->leagueService;
    }

    /**
     * Retrieve the account login form
     *
     * @return Form
     */
    public function getLoginForm()
    {
        if (null === $this->loginForm) {
            $this->loginForm = $this->getServiceLocator()->get('account_login_form');
        }
        return $this->loginForm;
    }

    /**
     * Retrieve the account register form
     *
     * @return Form
     */
    public function getRegisterForm()
    {
        if (null === $this->registerForm) {
            $this->registerForm = $this->getServiceLocator()->get('account_register_form');
        }
        return $this->registerForm;
    }

    /**
     * Retrieve the summoner form
     *
     * @return Form
     */
    public function getSummonerForm()
    {
        if (null === $this->summonerForm) {
            $this->summonerForm = $this->getServiceLocator()->get('summoner_form');
        }
        return $this->summonerForm;
    }

    /**
     * Retrieve the translator
     *
     * @return \Zend\I18n\Translator\Translator
     */
    public function getTranslator()
    {
        if (null === $this->translator) {
            $this->translator = $this->getServiceLocator()->get('translator');
        }
        return $this->translator;
    }

}
