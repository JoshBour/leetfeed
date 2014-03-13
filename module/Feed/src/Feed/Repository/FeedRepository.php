<?php
/**
 * Created by PhpStorm.
 * User: Josh
 * Date: 13/3/2014
 * Time: 1:12 μμ
 */

namespace Account\Repository;

use Doctrine\ORM\EntityRepository;
use Doctrine\ORM\Query\ResultSetMapping;
use Zend\Paginator\Paginator;
use Zend\Paginator\Adapter\ArrayAdapter;
use Doctrine\ORM\Query\ResultSetMappingBuilder;


class AccountRepository extends EntityRepository{
    public function findFeedsByDate($date = 'now', $firstResults = 0)
    {
        switch($date){
            case "now":
                $time = date("Y-m-d H:i:s");
                break;
        }
        $time = date("",$time);
        $qb = $this->createQueryBuilder('f');
        $qb->select()
            ->where($qb->expr()->gte('f.watchTime', '?1'))
            ->setParameter("1",$time)
            ->setFirstResult($firstResults);

        $query = $qb->getQuery();
        return new Paginator(new ArrayAdapter($query->getResult()), true);
    }
} 