<?php

namespace SimplyTestable\PageCacheBundle\Repository;

use Doctrine\ORM\EntityRepository;

class CacheValidatorHeadersRepository extends EntityRepository
{
    /**
     * @param int|null $limit
     *
     * @return int[]
     */
    public function findAll($limit = null)
    {
        $queryBuilder = $this->createQueryBuilder('CacheValidatorHeaders');
        $queryBuilder->select('CacheValidatorHeaders');
        $queryBuilder->orderBy('CacheValidatorHeaders.id', 'DESC');

        if (!is_null($limit)) {
            $queryBuilder->setMaxResults($limit);
        }

        return $queryBuilder->getQuery()->getResult();
    }
}
