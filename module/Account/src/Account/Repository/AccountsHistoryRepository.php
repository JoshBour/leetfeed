<?php
/**
 * Created by PhpStorm.
 * User: Josh
 * Date: 13/3/2014
 * Time: 1:12 μμ
 */

namespace Account\Repository;

use Doctrine\ORM\EntityRepository;


class AccountsHistoryRepository extends EntityRepository
{
    public function countFeeds()
    {
        $query = $this->createQueryBuilder('ah')
            ->select('COUNT(ah.feed)');
        $query = $query->getQuery();
        return $query->getSingleScalarResult();


    }
} 