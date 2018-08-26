<?php

namespace SimplyTestable\PageCacheBundle\Tests\Functional\Model;

use Doctrine\ORM\AbstractQuery;
use Doctrine\ORM\EntityManagerInterface;
use Doctrine\ORM\Mapping\ClassMetadata;
use Doctrine\ORM\QueryBuilder;
use Mockery\MockInterface;
use PHPUnit\Framework\TestCase;
use SimplyTestable\PageCacheBundle\Repository\CacheValidatorHeadersRepository;

class CacheValidatorHeadersRepositoryTest extends TestCase
{
    /**
     * @dataProvider findAllDataProvider
     *
     * @param int|null $limit
     * @param callable $queryBuilderModifier
     */
    public function testFindAll(?int $limit, callable $queryBuilderModifier)
    {
        $query = $this->createQuery();
        $queryBuilder = $this->createQueryBuilder($query);

        /* @var EntityManagerInterface|MockInterface $entityManager */
        $entityManager = $this->createEntityManager($queryBuilder);

        /* @var ClassMetadata $classMetaData */
        $classMetaData = \Mockery::mock(ClassMetadata::class);
        $classMetaData->name = 'CacheValidatorHeaders';

        $queryBuilderModifier($queryBuilder);

        $cacheValidatorHeadersRepository = new CacheValidatorHeadersRepository($entityManager, $classMetaData);
        $cacheValidatorHeadersRepository->findAll($limit);

        $this->addToAssertionCount(\Mockery::getContainer()->mockery_getExpectationCount());
    }

    public function findAllDataProvider(): array
    {
        return [
            'no limit' => [
                'limit' => null,
                'queryBuilderModifier' => function (MockInterface $queryBuilder) {
                    return $queryBuilder;
                },
            ],
            'has limit' => [
                'limit' => 10,
                'queryBuilderModifier' => function (MockInterface $queryBuilder) {
                    $queryBuilder
                        ->shouldReceive('setMaxResults')
                        ->with(10)
                        ->andReturn($queryBuilder);

                    return $queryBuilder;
                },
            ],
        ];
    }

    private function createQuery(): MockInterface
    {
        $query = \Mockery::mock(AbstractQuery::class);
        $query
            ->shouldReceive('getResult')
            ->once()
            ->andReturn([]);

        return $query;
    }

    private function createQueryBuilder(MockInterface $query): MockInterface
    {
        $queryBuilder = \Mockery::mock(QueryBuilder::class);

        $queryBuilder
            ->shouldReceive('select')
            ->twice()
            ->with('CacheValidatorHeaders')
            ->andReturn($queryBuilder);

        $queryBuilder
            ->shouldReceive('from')
            ->once()
            ->with('CacheValidatorHeaders', 'CacheValidatorHeaders', null)
            ->andReturn($queryBuilder);

        $queryBuilder
            ->shouldReceive('orderBy')
            ->once()
            ->with('CacheValidatorHeaders.id', 'DESC')
            ->andReturn($queryBuilder);

        $queryBuilder
            ->shouldReceive('getQuery')
            ->once()
            ->andReturn($query);

        return $queryBuilder;
    }

    private function createEntityManager(MockInterface $queryBuilder): MockInterface
    {
        $entityManager = \Mockery::mock(EntityManagerInterface::class);
        $entityManager
            ->shouldReceive('createQueryBuilder')
            ->once()
            ->andReturn($queryBuilder);

        return $entityManager;
    }

    protected function tearDown()
    {
        parent::tearDown();

        \Mockery::close();
    }
}
