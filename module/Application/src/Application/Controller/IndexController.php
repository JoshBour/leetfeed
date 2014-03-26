<?php
namespace Application\Controller;

use Doctrine\ORM\EntityRepository;
use Zend\Mail\Message;
use Zend\Mail\Transport\Smtp as SmtpTransport;
use Zend\Mail\Transport\SmtpOptions;
use Zend\Mvc\Controller\AbstractActionController;
use Zend\Paginator\Adapter\ArrayAdapter;
use Zend\Paginator\Paginator;
use Zend\View\Model\ViewModel;

class IndexController extends AbstractActionController
{
    const EMAIL_ERROR = "The message failed to be submitted, please try again.";
    const EMAIL_SUCCESS = "Your message has been sent successfully.";

    /**
     * The account history repository.
     *
     * @var EntityRepository
     */
    private $accountsHistoryRepository;

    /**
     * The contact form.
     *
     * @var \Zend\Form\Form
     */
    private $contactForm;

    /**
     * The entity manager.
     *
     * @var \Doctrine\ORM\EntityManager
     */
    private $entityManager;

    /**
     * The feed repository
     *
     * @var \Feed\Repository\FeedRepository
     */
    private $feedRepository;

    /**
     * The premium feed repository
     *
     * @var EntityRepository
     */
    private $premiumFeedRepository;

    public function sitemapAction()
    {
        $this->getResponse()->getHeaders()->addHeaders(array('Content-type' => 'application/xml; charset=utf-8'));
        $type = $this->params()->fromRoute('type');
        $sitemapXmlParser = new \Application\Model\SitemapXmlParser();
        $sitemapXmlParser->begin();
        if (!$type) {
            $feedCount = $this->getFeedRepository()->countFeeds();
            $pageCount = $feedCount > 15000 ? $feedCount / 15000 : 1;
            if(!is_int($pageCount)){
                $pageCount = intval($pageCount)+1;
            }
            $sitemapXmlParser->addHeader("sitemapindex");
            $sitemapXmlParser->addSitemap("http://www.leetfeed.com/sitemap/static");
            for ($i = 0; $i < $pageCount; $i++)
                $sitemapXmlParser->addSitemap("http://www.leetfeed.com/sitemap/dynamic/" . $i * 15000 . "-" . ($i + 1) * 15000);

        }else{
            $pages = array();
            if($type == "static"){
                $pages = $this->getServiceLocator()->get('Config')['static_pages'];
            }else{
                $index = $this->params()->fromRoute("index");
                $limits = explode("-",$index);
                $feeds = $this->getFeedRepository()->findBy(array(),array(),15000,$limits[0]);
                foreach($feeds as $feed){
                    $pages[] = "/feed/".$feed->getFeedId();
                }
            }
            $sitemapXmlParser->addHeader("urlset");
            $i = 0;
            foreach($pages as $page){
                if($i == 20){
                    $sitemapXmlParser->show();
                    $i = 0;
                }
                $sitemapXmlParser->addUrl("http://www.leetfeed.com" . $page);
                $i++;
            }
        }
        $view = new ViewModel();
        $view->setTerminal(true);
        $view->setTemplate('application/index/sitemap.xml');
        $sitemapXmlParser->close();
        $sitemapXmlParser->show();
        return $view;
    }

    /**
     * The index action.
     * Route: \
     *
     * @return ViewModel
     */
    public function indexAction()
    {
        $feedRepository = $this->getFeedRepository();
        $accountsHistoryRepository = $this->getAccountsHistoryRepository();
        $feeds = $feedRepository->findBy(array("isRelated" => 0), array("rating" => "DESC"), 50);
        $premiumFeeds = $this->getPremiumFeedRepository()->findBy(array(), array('visits' => "ASC"), 50);
        $latestFeeds = $accountsHistoryRepository->findBy(array(), array("watchTime" => "DESC"), 50);

        $feedCnt = $feedRepository->countFeeds("0");
        $feedTotalPages = intval(floor($feedCnt / 50));

        $latestFeedCnt = $accountsHistoryRepository->countFeeds();
        $latestFeedsTotalPages = intval($latestFeedCnt / 50);

        $premiumFeeds = new Paginator(new ArrayAdapter($premiumFeeds));
        $premiumFeeds->setCurrentPageNumber(1);
        $premiumFeeds->setItemCountPerPage(50);

        return new ViewModel(array(
            "feeds" => $feeds,
            "feedPages" => $feedTotalPages,
            "premiumFeeds" => $premiumFeeds,
            "latestFeedPages" => $latestFeedsTotalPages,
            "latestFeeds" => $latestFeeds,
        ));
    }

    /**
     * The get more feeds action
     * Only accessed via xmlHttpRequest.
     * Route: \get-more-feeds
     *
     * @return ViewModel
     */
    public function getMoreFeedsAction()
    {
        if ($this->getRequest()->isXmlHttpRequest()) {
            $category = $this->params()->fromRoute("category");
            $page = $this->params()->fromRoute("page", 1);
            $isPremium = false;
            switch ($category) {
                case "top-feeds":
                    $feeds = $this->getFeedRepository()->findBy(array("isRelated" => 0), array("rating" => "DESC"), 50, 50 * $page);
                    break;
                case "premium-feeds":
                    $feeds = $this->getPremiumFeedRepository()->findBy(array(), array('visits' => "ASC"), 50, 50 * $page);
                    $isPremium = true;
                    break;
                case "latest-feeds":
                    $feeds = $this->getAccountsHistoryRepository()->findBy(array(), array("watchTime" => "DESC"), 50, 50 * $page);
                    $isPremium = true;
                    break;
                default:
                    $feeds = array();
            }
            $view = new ViewModel();
            $view->setTerminal(true);
            $view->setVariables(array(
                "feeds" => $feeds,
                "isPremium" => $isPremium
            ));
            return $view;
        } else {
            return $this->notFoundAction();
        }
    }

    /**
     * The test action.
     * Used only in development.
     * Route: /test
     *
     * @return ViewModel
     */
    public function testAction()
    {
        if ($this->identity()) {
            $summoners = $this->account()->getSummoners();
            $summoner = $summoners[0];
            $feeds = $this->getServiceLocator()->get('feed_service')->getSummonerFeeds($summoner);
            # $feeds = $this->getServiceLocator()->get('feed_service')->getLolProFeeds(array("Ahri","Aatrox","Jayce"));
            return new ViewModel(array("feeds" => $feeds));
        } else {
            return $this->notFoundAction();
        }
    }

    /**
     * The faq action.
     * Route: /faq
     *
     * @return ViewModel
     */
    public function faqAction()
    {
        return new ViewModel(array(
            "pageTitle" => "Frequently Asked Questions"
        ));
    }

    /**
     * The promote action.
     * Route: /promote
     *
     * @return ViewModel
     */
    public function promoteAction()
    {
        return new ViewModel(array(
            "pageTitle" => "Promote your feeds!"
        ));
    }

    /**
     * The contact action.
     * Route: /contact
     *
     * @return \Zend\Http\Response|ViewModel
     */
    public function contactAction()
    {
        /**
         * @var $request \Zend\Http\Request
         */
        $request = $this->getRequest();
        $form = $this->getContactForm();
        if ($request->isPost()) {
            $data = $request->getPost();
            $form->setData($data);
            if ($form->isValid()) {
                $message = new Message();
                $message->addTo('support@leetfeed.com')
                    ->addFrom("admin@leetfeed.com")
                    ->setSubject($data['contact']['subject'])
                    ->addReplyTo($data['contact']['sender'])
                    ->setBody("From:" . $data['contact']['sender'] . '\r\n' . $data['contact']['body'])
                    ->setEncoding("UTF-8");

                $transport = new SmtpTransport();
                $options = new SmtpOptions(array(
                    'name' => 'leetfeed.com',
                    'host' => 'smtpout.europe.secureserver.net',
                    'port' => '80',
                    'connection_class' => 'login',
                    'connection_config' => array(
                        'username' => 'support@leetfeed.com',
                        'password' => '7934603745912766',
                    )
                ));
                $transport->setOptions($options);
                $transport->send($message);

                $this->flashMessenger()->addMessage(self::EMAIL_SUCCESS);
                return $this->redirect()->toRoute('contact');
            } else {
                $this->flashMessenger()->addMessage(self::EMAIL_ERROR);
            }

        }
        return new ViewModel(array(
            "form" => $form,
            "pageTitle" => "Contact Us"
        ));
    }

    /**
     * Retrieve the accounts history repository.
     *
     * @return \Account\Repository\AccountsHistoryRepository
     */
    public function getAccountsHistoryRepository()
    {
        if ($this->accountsHistoryRepository === null)
            $this->accountsHistoryRepository = $this->getEntityManager()->getRepository('\Account\Entity\AccountsHistory');
        return $this->accountsHistoryRepository;
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

    /**
     * Retrieve the doctrine entity manager.
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
     * Retrieve the feed repository.
     *
     * @return \Feed\Repository\FeedRepository
     */
    public function getFeedRepository()
    {
        if (null === $this->feedRepository)
            $this->feedRepository = $this->getEntityManager()->getRepository('\Feed\Entity\Feed');
        return $this->feedRepository;
    }

    /**
     * Retrieve the premium feed repository.
     *
     * @return EntityRepository
     */
    public function getPremiumFeedRepository()
    {
        if (null === $this->premiumFeedRepository)
            $this->premiumFeedRepository = $this->getEntityManager()->getRepository('\Feed\Entity\PremiumFeed');
        return $this->premiumFeedRepository;
    }


}
