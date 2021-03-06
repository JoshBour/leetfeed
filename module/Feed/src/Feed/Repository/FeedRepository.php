<?php
/**
 * Created by PhpStorm.
 * User: Josh
 * Date: 13/3/2014
 * Time: 1:12 μμ
 */

namespace Feed\Repository;

use Doctrine\ORM\EntityRepository;
use Zend\Paginator\Adapter\ArrayAdapter;
use Zend\Paginator\Paginator;


class FeedRepository extends EntityRepository
{
    public function countFeeds($related = false, $ignored = false)
    {
        $isRelated = $related ? 1 : 0;
        $isIgnored = $ignored ? 1 : 0;
        $query = $this->createQueryBuilder('f')
            ->select('COUNT(f.feedId)')
            ->where('f.isRelated = :related')
            ->andWhere('f.isIgnored = :ignored')
            ->setParameter("related", $isRelated)
            ->setParameter("ignored",$isIgnored);
        $query = $query->getQuery();
        return $query->getSingleScalarResult();


    }

    public function findFeedsByDate($account, $date = 'now', $firstResults = 0)
    {
        switch ($date) {
            case "now":
                $datetime = new \DateTime("today");
                break;
            case "yesterday":
                $datetime = new \DateTime("yesterday");
                break;
            case "week":
                $datetime = new \DateTime("-1 week");
                break;
            case "month":
                $datetime = new \DateTime("-1 week");
                break;
            case "all":
                $datetime = new \DateTime("-20 year");
                break;
            default:
                $datetime = new \DateTime("-20 year");
        }

        $time = $datetime->format("Y-m-d H:i:s");
        $qb = $this->createQueryBuilder('f');
        $qb->select('f')
            ->add('from', 'Feed\Entity\Feed f LEFT JOIN f.watchedHistory wh')
            ->where($qb->expr()->gte('wh.watchTime', '?1'))
            ->andWhere($qb->expr()->eq('wh.account', '?2'))
            ->setParameters(array("1" => $time, "2" => $account))
            ->setFirstResult($firstResults);

        $query = $qb->getQuery();
        return new Paginator(new ArrayAdapter($query->getResult()), true);
    }
} 