<?php
namespace Application\Controller;

use Zend\Mvc\Controller\AbstractActionController;
use Zend\View\Model\ViewModel;
use Zend\Mail\Message;
use Zend\Mail\Transport\Sendmail as SendmailTransport;
use Zend\Paginator\Adapter\ArrayAdapter;
use Zend\Paginator\Paginator;
use Doctrine\ORM\EntityRepository;

class IndexController extends AbstractActionController
{
    const EMAIL_ERROR = "The message failed to be submitted, please try again.";
    const EMAIL_SUCCESS = "Your message has been sent successfully.";

    private $accountsHistoryRepository;

    /**
     * @var \Zend\Form\Form
     */
    private $contactForm;

    private $entityManager;

    private $feedRepository;

    private $premiumFeedRepository;

    public function indexAction()
    {
        $feedRepository = $this->getFeedRepository();
        $feeds = $feedRepository->findBy(array("isRelated" => 0), array("rating" => "DESC"));
        $premiumFeeds = $this->getPremiumFeedRepository()->findBy(array(), array('visits' => "ASC"));
        $latestFeeds = $this->getAccountsHistoryRepository()->findBy(array(), array("watchTime" => "DESC"));
        # $risingFeeds = $this->getGenerator()->getRisingFeeds(1);

        $feeds = new Paginator(new ArrayAdapter($feeds));
        $feeds->setCurrentPageNumber(1);
        $feeds->setItemCountPerPage(50);

        $premiumFeeds = new Paginator(new ArrayAdapter($premiumFeeds));
        $premiumFeeds->setCurrentPageNumber(1);
        $premiumFeeds->setItemCountPerPage(50);

        $latestFeeds = new Paginator(new ArrayAdapter($latestFeeds));
        $latestFeeds->setCurrentPageNumber(1);
        $latestFeeds->setItemCountPerPage(50);

        #  $risingFeeds = new Paginator(new ArrayAdapter($risingFeeds));
        #  $risingFeeds->setCurrentPageNumber(1);
        #  $risingFeeds->setItemCountPerPage(50);

        return new ViewModel(array(
            "feeds" => $feeds,
            "premiumFeeds" => $premiumFeeds,
            "latestFeeds" => $latestFeeds,
            #   "risingFeeds" => $risingFeeds,
        ));
    }

    public function getMoreFeedsAction()
    {
        if ($this->getRequest()->isXmlHttpRequest()) {
            $feedRepository = $this->getFeedRepository();
            $category = $this->params()->fromRoute("category");
            $page = $this->params()->fromRoute("page", 1);
            $isPremium = false;
            switch ($category) {
                case "top-feeds":
                    $feeds = $feedRepository->findBy(array("isRelated" => 0), array("rating" => "DESC"));
                    break;
                case "premium-feeds":
                    $feeds = $this->getPremiumFeedRepository()->findBy(array(), array('visits' => "ASC"));
                    $isPremium = true;
                    break;
                case "latest-feeds":
                    $feeds = $feedRepository->findBy(array("isRelated" => 0), array("feedId" => "DESC"));
                    break;
                case "rising-feeds":
                    $feeds = $this->getGenerator()->getRisingFeeds(($page*50)+1);
                    break;
            }
            $feeds = new Paginator(new ArrayAdapter($feeds));
            $feeds->setCurrentPageNumber($page);
            $view = new ViewModel();
            $view->setTerminal(true);
            $view->setVariables(array(
                "feeds" => $feeds,
                "isPremium" => $isPremium
            ));
            return $view;
        } else {
            $this->getResponse()->setStatusCode(404);
            return;
        }
    }
	
    public function faqAction()
    {
        return new ViewModel();
    }

    public function promoteAction()
    {
        return new ViewModel();
    }

    public function aboutAction()
    {
        return new ViewModel();
    }

    public function contactAction()
    {
        $request = $this->getRequest();
        $form = $this->getContactForm();
        if ($request->isPost()) {
            $data = $request->getPost();
            $form->setData($data);
            if ($form->isValid()) {
                $message = new Message();
                $message->addTo('support@leetfeed.com')
                    ->addFrom($data['contact']['sender'])
                    ->setSubject($data['contact']['subject'])
                    ->setBody($data['contact']['body'])
                    ->setEncoding("UTF-8");

                $transport = new SendmailTransport();
                $transport->send($message);

                $this->flashMessenger()->addMessage(self::EMAIL_SUCCESS);
                return $this->redirect()->toRoute('contact');
            } else {
                $this->flashMessenger()->addMessage(self::EMAIL_ERROR);
            }

        }
        return new ViewModel(array(
            "form" => $form
        ));
    }

    public function getAccountsHistoryRepository(){
        if($this->accountsHistoryRepository === null)
            $this->accountsHistoryRepository = $this->getEntityManager()->getRepository('\Account\Entity\AccountsHistory');
        return $this->accountsHistoryRepository;
    }

    /**
     * Retrieve the feed repository
     *
     * @return EntityRepository
     */
    public function getFeedRepository()
    {
        if (null === $this->feedRepository)
            $this->feedRepository = $this->getEntityManager()->getRepository('\Feed\Entity\Feed');
        return $this->feedRepository;
    }

    /**
     * Retrieve the premium feed repository
     *
     * @return EntityRepository
     */
    public function getPremiumFeedRepository()
    {
        if (null === $this->premiumFeedRepository)
            $this->premiumFeedRepository = $this->getEntityManager()->getRepository('\Feed\Entity\PremiumFeed');
        return $this->premiumFeedRepository;
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
     * Retrieve the contact form.
     *
     * @return \Zend\Form\Form
     */
    public function getContactForm()
    {
        if (null === $this->contactForm)
            $this->contactForm = $this->getServiceLocator()->get("contact_form");
        return $this->contactForm;
    }
}
