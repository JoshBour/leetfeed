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
            $feedCount = $this->getFeedRepository()->countFeeds(false,false);
            $pageCount = $feedCount > 15000 ? $feedCount / 15000 : 1;
            if (!is_int($pageCount)) {
                $pageCount = intval($pageCount) + 1;
            }
            $sitemapXmlParser->addHeader("sitemapindex");
            $sitemapXmlParser->addSitemap("http://www.leetfeed.com/sitemap/static");
            for ($i = 0; $i < $pageCount; $i++)
                $sitemapXmlParser->addSitemap("http://www.leetfeed.com/sitemap/dynamic/" . $i * 15000 . "-" . ($i + 1) * 15000);

        } else {
            if ($type == "static") {
                $pages = $this->getServiceLocator()->get('Config')['static_pages'];
                $sitemapXmlParser->addHeader("urlset");
                foreach ($pages as $page) {
                    $sitemapXmlParser->addUrl("http://www.leetfeed.com" . $page);
                }
            } else {
                $index = $this->params()->fromRoute("index");
                $limits = explode("-", $index);
                $feeds = $this->getFeedRepository()->findBy(array("isIgnored" => 0,"isRelated" => 0), array(), 15000, $limits[0]);

                $sitemapXmlParser->addHeader("urlset",true);
                $i = 0;
                foreach ($feeds as $feed) {
                    if ($i == 20) {
                        $sitemapXmlParser->show();
                        $i = 0;
                    }
                    $sitemapXmlParser->addFeedInfo($feed);
                    $i++;
                }
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
        $feedNumber = 24;
        $hasPrivileges = ($this->account()) ? $this->account()->hasSuperPrivileges() : false;
        $feedRepository = $this->getFeedRepository();
        $feeds = $feedRepository->findBy(array("isRelated" => 0,"isIgnored" => 0), array("postDate" => "DESC","rating" => "DESC"), $feedNumber);
        $premiumFeeds = $this->getPremiumFeedRepository()->findBy(array(), array('visits' => "ASC"), $feedNumber);

        $feedCnt = $feedRepository->countFeeds("0","0");
        $feedTotalPages = intval(floor($feedCnt / $feedNumber));

        return new ViewModel(array(
            "feeds" => $feeds,
            "feedPages" => $feedTotalPages,
            "hasPrivileges" => $hasPrivileges,
            "premiumFeeds" => $premiumFeeds,
            "bodyClass" => "mainPage"
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
            $feedNumber = 24;
            $category = $this->params()->fromRoute("category");
            $page = $this->params()->fromRoute("page", 1);
            $isPremium = false;
            switch ($category) {
                case "top-feeds":
                    $feeds = $this->getFeedRepository()->findBy(array("isRelated" => 0,"isIgnored" => 0), array("rating" => "DESC","postDate" => "DESC"), $feedNumber, $feedNumber * $page);
                    break;
                case "premium-feeds":
                    $feeds = $this->getPremiumFeedRepository()->findBy(array(), array('visits' => "ASC"), $feedNumber, $feedNumber * $page);
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
            $response = $this->getServiceLocator()->get('youtube_service')->findByQuery("league of legends", null, 20, "this_week");
            $videos = $response->getVideos(true);
            foreach ($videos as $video) {
                echo $video->getScore() . '<br />';
            }
            # $feeds = $this->getServiceLocator()->get('feed_service')->getLolProFeeds(array("Ahri","Aatrox","Jayce"));
            return new ViewModel();
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
            "pageTitle" => "Leetfeed | Frequently Asked Questions",
            "noAds" => true
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
            "pageTitle" => "Leetfeed | Promote and advertise your League of Legends videos",
            "noAds" => true
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
            "pageTitle" => "Leetfeed | Contact Us",
            "noAds" => true
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
