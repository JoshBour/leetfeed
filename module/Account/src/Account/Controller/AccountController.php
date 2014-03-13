<?php
namespace Account\Controller;

use Zend\Form\Form;
use Zend\Mvc\Controller\AbstractActionController;
use Zend\View\Model\ViewModel;
use Zend\Authentication\AuthenticationService;
use Account\Entity\Account;

class AccountController extends AbstractActionController
{
    const CONTROLLER_NAME = 'account_controller';

    const ROUTE_LOGIN = 'account-login';
    const ROUTE_REGISTER = 'account-register';
    const ROUTE_AUTHENTICATE = 'account-authenticate';
    const ROUTE_HOMEPAGE = 'home';

    const MESSAGE_ACCOUNT_CREATED = 'Your account has been created successfully!';
    const MESSAGE_INVALID_CREDENTIALS = 'The username/password combination is invalid.';

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
     * The zend translator.
     *
     * @var \Zend\I18n\Translator\Translator
     */
    private $translator;

    /**
     * The login action.
     *
     * @return mixed|\Zend\Http\Response|ViewModel
     */
    public function loginAction()
    {
        $entity = new Account();
        $loginForm = $this->getLoginForm();
        $request = $this->getRequest();
        $loginForm->bind($entity);
        if ($request->isPost()) {
            $data = $request->getPost();
            $loginForm->setData($data);
            if ($loginForm->isValid()) {
                return $this->forward()->dispatch(static::CONTROLLER_NAME, array('action' => 'authenticate',
                    'username' => $entity->getUsername(),
                    'password' => $entity->getPassword(),
                    'remember' => $data['account']['remember']));
            }
        }
        return new ViewModel(array(
            'form' => $loginForm,
        ));
    }

    /**
     * The logout action
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
     *
     * @return mixed|\Zend\Http\Response|ViewModel
     */
    public function registerAction()
    {
        $service = $this->getAccountService();
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
            }
        }
        return new ViewModel(array(
            'form' => $form,
        ));
    }

    /**
     * The authentication action.
     *
     * @return \Zend\Http\Response
     */
    public function authenticateAction()
    {
        $authService = $this->getAuthenticationService();
        $adapter = $authService->getAdapter();

        $remember = $this->params()->fromRoute('remember', 1);
        $username = $this->params()->fromRoute('username');
        $password = $this->params()->fromRoute('password');

        $adapter->setIdentityValue($username);
        $adapter->setCredentialValue($password);
        $authResult = $authService->authenticate();
        if ($authResult->isValid()) {
            if ($remember == 1) {
                $this->getAuthStorage()->setRememberMe(1);
                $authService->setStorage($this->getAuthStorage());
            }
            $authService->getStorage()->write($authResult->getIdentity());
        } else {
            $this->flashMessenger()->addMessage($this->getTranslator()->translate(self::MESSAGE_INVALID_CREDENTIALS));
            return $this->redirect()->toRoute(self::ROUTE_LOGIN);
        }
        return $this->redirect()->toRoute(self::ROUTE_HOMEPAGE);
    }

    /**
     * Retrieve the account service
     *
     * @return \Account\Service\Account
     */
    public function getAccountService()
    {
        if (null === $this->accountService) {
            $this->setAccountService($this->getServiceLocator()->get('account_service'));
        }
        return $this->accountService;
    }

    /**
     * Set the account service
     *
     * @param \Account\Service\Account $accountService
     * @return AccountController
     */
    public function setAccountService($accountService)
    {
        $this->accountService = $accountService;
        return $this;
    }

    /**
     * Retrieve the account login form
     *
     * @return Form
     */
    public function getLoginForm()
    {
        if (null === $this->loginForm) {
            $this->setLoginForm($this->getServiceLocator()->get('account_login_form'));
        }
        return $this->loginForm;
    }

    /**
     * Set the account login form
     *
     * @param Form $loginForm
     * @return AccountController
     */
    public function setLoginForm($loginForm)
    {
        $this->loginForm = $loginForm;
        return $this;
    }

    /**
     * Retrieve the account register form
     *
     * @return Form
     */
    public function getRegisterForm()
    {
        if (null === $this->registerForm) {
            $this->setRegisterForm($this->getServiceLocator()->get('account_register_form'));
        }
        return $this->registerForm;
    }

    /**
     * Set the account register form
     *
     * @param Form $registerForm
     * @return AccountController
     */
    public function setRegisterForm($registerForm)
    {
        $this->registerForm = $registerForm;
        return $this;
    }

    /**
     * Retrieve the doctrine entity manager
     *
     * @return \Doctrine\ORM\EntityManager
     */
    public function getEntityManager()
    {
        if (null === $this->entityManager) {
            $this->setEntityManager($this->getServiceLocator()->get('Doctrine\ORM\EntityManager'));
        }
        return $this->entityManager;
    }

    /**
     * Set the doctrine entity manager
     *
     * @param \Doctrine\ORM\EntityManager $em
     * @return $this
     */
    public function setEntityManager($em)
    {
        $this->entityManager = $em;
        return $this;
    }

    /**
     * Retrieve the translator
     *
     * @return \Zend\I18n\Translator\Translator
     */
    public function getTranslator()
    {
        if (null === $this->translator) {
            $this->setTranslator($this->getServiceLocator()->get('translator'));
        }
        return $this->translator;
    }

    /**
     * Set the translator
     *
     * @param \Zend\I18n\Translator\Translator $translator
     * @return AccountController
     */
    public function setTranslator($translator)
    {
        $this->translator = $translator;
        return $this;
    }

    /**
     * Retrieve the authentication service
     *
     * @return AuthenticationService
     */
    public function getAuthenticationService()
    {
        if (null === $this->authService) {
            $this->setAuthenticationService($this->getServiceLocator()->get('auth_service'));
        }
        return $this->authService;
    }

    /**
     * Set the authentication service
     *
     * @param AuthenticationService $authService
     * @return AccountController
     */
    public function setAuthenticationService($authService)
    {
        $this->authService = $authService;
        return $this;
    }

    /**
     * Retrieve the auth storage
     *
     * @return \Account\Model\AuthStorage
     */
    public function getAuthStorage()
    {
        if (null === $this->authStorage) {
            $this->setAuthStorage($this->getServiceLocator()->get('authStorage'));
        }
        return $this->authStorage;
    }

    /**
     * Set the auth storage
     *
     * @param \Account\Model\AuthStorage $authStorage
     * @return AccountController
     */
    public function setAuthStorage($authStorage)
    {
        $this->authStorage = $authStorage;
        return $this;
    }
}
