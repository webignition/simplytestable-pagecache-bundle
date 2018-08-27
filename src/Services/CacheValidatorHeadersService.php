<?php

namespace SimplyTestable\PageCacheBundle\Services;

use Doctrine\ORM\EntityManagerInterface;
use Doctrine\ORM\EntityRepository;
use SimplyTestable\PageCacheBundle\Entity\CacheValidatorHeaders;

class CacheValidatorHeadersService
{
    /**
     * @var EntityManagerInterface
     */
    private $entityManager;

    /**
     * @var EntityRepository
     */
    private $entityRepository;

    public function __construct(EntityManagerInterface $entityManager)
    {
        $this->entityManager = $entityManager;
        $this->entityRepository = $this->entityManager->getRepository(CacheValidatorHeaders::class);
    }

    public function find(string $identifier): ?CacheValidatorHeaders
    {
        /* @var CacheValidatorHeaders $cacheValidatorHeaders */
        $cacheValidatorHeaders = $this->entityRepository->findOneBy([
            'identifier' => $identifier
        ]);

        return $cacheValidatorHeaders;
    }

    public function create(string $identifier, \DateTime $lastModified): CacheValidatorHeaders
    {
        $cacheValidatorHeaders = new CacheValidatorHeaders();
        $cacheValidatorHeaders->setIdentifier($identifier);
        $cacheValidatorHeaders->setLastModifiedDate($lastModified);

        $this->entityManager->persist($cacheValidatorHeaders);
        $this->entityManager->flush();

        return $cacheValidatorHeaders;
    }

    public function clear(?int $limit = null)
    {
        $all = $this->entityRepository->findAll($limit);

        foreach ($all as $cacheValidatorHeaders) {
            $this->entityManager->remove($cacheValidatorHeaders);
        }

        $this->entityManager->flush();
    }

    public function count(): int
    {
        return $this->entityRepository->count([]);
    }
}
