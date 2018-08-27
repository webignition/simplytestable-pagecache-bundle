<?php

namespace SimplyTestable\PageCacheBundle\Tests\Functional;

use Doctrine\ORM\EntityManagerInterface;
use Doctrine\ORM\Query\ResultSetMapping;
use Mockery\MockInterface;
use SimplyTestable\PageCacheBundle\Repository\CacheValidatorHeadersRepository;

class EntityManagerProxy implements EntityManagerInterface
{
    /**
     * @var MockInterface|EntityManagerInterface
     */
    private $entityManagerMock;

    /**
     * @var CacheValidatorHeadersRepository
     */
    private $cacheValidatorHeadersRepository;

    public function __construct()
    {
        $this->entityManagerMock = \Mockery::mock(EntityManagerInterface::class);
        $this->cacheValidatorHeadersRepository = \Mockery::mock(CacheValidatorHeadersRepository::class);
    }

    public function getMock()
    {
        return $this->entityManagerMock;
    }

    public function getClassMetadata($className)
    {
    }

    public function getCache()
    {
    }

    public function getConnection()
    {
    }

    public function getExpressionBuilder()
    {
    }

    public function beginTransaction()
    {
    }

    public function transactional($func)
    {
    }

    public function commit()
    {
    }

    public function rollback()
    {
    }

    public function createQuery($dql = '')
    {
    }

    public function createNamedQuery($name)
    {
    }

    public function createNativeQuery($sql, ResultSetMapping $rsm)
    {
    }

    public function createNamedNativeQuery($name)
    {
    }

    public function createQueryBuilder()
    {
    }

    public function getReference($entityName, $id)
    {
    }

    public function getPartialReference($entityName, $identifier)
    {
    }

    public function close()
    {
    }

    public function copy($entity, $deep = false)
    {
    }

    public function lock($entity, $lockMode, $lockVersion = null)
    {
    }

    public function getEventManager()
    {
    }

    public function getConfiguration()
    {
    }

    public function isOpen()
    {
    }

    public function getUnitOfWork()
    {
    }

    public function getHydrator($hydrationMode)
    {
    }

    public function newHydrator($hydrationMode)
    {
    }

    public function getProxyFactory()
    {
    }

    public function getFilters()
    {
    }

    public function isFiltersStateClean()
    {
    }

    public function hasFilters()
    {
    }

    public function find($className, $id)
    {
    }

    public function persist($object)
    {
    }

    public function remove($object)
    {
    }

    public function merge($object)
    {
    }

    public function clear($objectName = null)
    {
    }

    public function detach($object)
    {
    }

    public function refresh($object)
    {
    }

    public function flush()
    {
    }

    public function getRepository($className)
    {
        return $this->cacheValidatorHeadersRepository;
    }

    public function getMetadataFactory()
    {
    }

    public function initializeObject($obj)
    {
    }

    public function contains($object)
    {
    }

    public function __call($name, $arguments)
    {
    }
}
