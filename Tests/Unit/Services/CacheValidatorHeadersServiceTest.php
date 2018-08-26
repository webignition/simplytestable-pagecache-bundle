<?php

namespace SimplyTestable\PageCacheBundle\Tests\Functional\Services;

use Doctrine\ORM\EntityManagerInterface;
use Mockery\MockInterface;
use PHPUnit\Framework\TestCase;
use SimplyTestable\PageCacheBundle\Entity\CacheValidatorHeaders;
use SimplyTestable\PageCacheBundle\Model\CacheValidatorIdentifier;
use SimplyTestable\PageCacheBundle\Repository\CacheValidatorHeadersRepository;
use SimplyTestable\PageCacheBundle\Services\CacheValidatorHeadersService;
use SimplyTestable\PageCacheBundle\Services\CacheValidatorIdentifierFactory;
use Symfony\Component\HttpFoundation\Request;
use webignition\SimplyTestableUserInterface\UserInterface;
use webignition\SimplyTestableUserManagerInterface\UserManagerInterface;

class CacheValidatorHeadersServiceTest extends TestCase
{
//    public function testGetDoesNotExist()
//    {
//        $identifier = 'foo';
//
//        /* @var MockInterface|CacheValidatorHeadersRepository $cacheValidatorHeadersRepository */
//        $entityRepository = \Mockery::mock(CacheValidatorHeadersRepository::class);
//
//        $entityRepository
//            ->shouldReceive('findOneBy')
//            ->with([
//                'identifier' => $identifier,
//            ])
//            ->andReturn(null);
//
//        /* @var MockInterface|EntityManagerInterface $entityManager */
//        $entityManager = \Mockery::mock(EntityManagerInterface::class);
//
//        $entityManager
//            ->shouldReceive('getRepository')
//            ->with(CacheValidatorHeaders::class)
//            ->andReturn($entityRepository);
//
//        $entityManager
//            ->shouldReceive('persist')
//            ->withArgs(function (CacheValidatorHeaders $cacheValidatorHeaders) use ($identifier) {
//                $this->assertEquals($identifier, $cacheValidatorHeaders->getIdentifier());
//
//                var_dump($cacheValidatorHeaders);
//            });
//
//        $entityManager
//            ->shouldReceive('flush');
//
//        $cacheValidatorHeadersService = new CacheValidatorHeadersService($entityManager);
//
//        $cacheValidatorHeadersService->get($identifier);
//    }

    /**
     * @dataProvider findDataProvider
     *
     * @param string $identifier
     * @param EntityManagerInterface $entityManager
     * @param CacheValidatorHeadersRepository $cacheValidatorHeadersRepository
     * @param CacheValidatorHeaders|null $expectedReturnValue
     */
    public function testFind(
        string $identifier,
        EntityManagerInterface $entityManager,
        CacheValidatorHeadersRepository $cacheValidatorHeadersRepository,
        $expectedReturnValue
    ) {
        /* @var MockInterface|EntityManagerInterface $entityManager */
        $entityManager
            ->shouldReceive('getRepository')
            ->with(CacheValidatorHeaders::class)
            ->andReturn($cacheValidatorHeadersRepository);

        $cacheValidatorHeadersService = new CacheValidatorHeadersService($entityManager);

        $this->assertEquals($expectedReturnValue, $cacheValidatorHeadersService->find($identifier));
    }

    public function findDataProvider(): array
    {
        $cacheValidatorHeaders = new CacheValidatorHeaders();

        return [
            'does not exist' => [
                'identifier' => 'foo',
                'entityManager' => $this->createEntityManager(),
                'cacheValidatorHeadersRepository' => $this->createFindEntityRepository(
                    [
                        'identifier' => 'foo',
                    ],
                    null
                ),
                'expectedReturnValue' => null,
            ],
            'exists' => [
                'identifier' => 'foo',
                'entityManager' => $this->createEntityManager(),
                'cacheValidatorHeadersRepository' => $this->createFindEntityRepository(
                    [
                        'identifier' => 'foo',
                    ],
                    $cacheValidatorHeaders
                ),
                'expectedReturnValue' => $cacheValidatorHeaders,
            ],
        ];
    }

    public function testCreate()
    {
        $identifier = 'foo';
        $lastModified = new \DateTime('2018-08-26');

        $cacheValidatorHeadersRepository = \Mockery::mock(CacheValidatorHeadersRepository::class);

        /* @var MockInterface|EntityManagerInterface $entityManager */
        $entityManager = \Mockery::mock(EntityManagerInterface::class);
        $entityManager
            ->shouldReceive('getRepository')
            ->with(CacheValidatorHeaders::class)
            ->andReturn($cacheValidatorHeadersRepository);

        $entityManager
            ->shouldReceive('persist')
            ->withArgs(function (CacheValidatorHeaders $cacheValidatorHeaders) use ($identifier, $lastModified) {
                $this->assertEquals($identifier, $cacheValidatorHeaders->getIdentifier());
                $this->assertEquals($lastModified, $cacheValidatorHeaders->getLastModifiedDate());

                return true;
            });

        $entityManager
            ->shouldReceive('flush');

        $cacheValidatorHeadersService = new CacheValidatorHeadersService($entityManager);
        $cacheValidatorHeaders = $cacheValidatorHeadersService->create($identifier, $lastModified);

        $this->assertInstanceOf(CacheValidatorHeaders::class, $cacheValidatorHeaders);
        $this->assertEquals($identifier, $cacheValidatorHeaders->getIdentifier());
        $this->assertEquals($lastModified, $cacheValidatorHeaders->getLastModifiedDate());
    }

    /**
     * @dataProvider clearDataProvider
     *
     * @param int|null $limit
     */
    public function testClear($limit)
    {
        $cacheValidatorHeaders = new CacheValidatorHeaders();

        $cacheValidatorHeadersRepository = \Mockery::mock(CacheValidatorHeadersRepository::class);
        $cacheValidatorHeadersRepository
            ->shouldReceive('findAll')
            ->with($limit)
            ->andReturn([
                $cacheValidatorHeaders,
            ]);

        /* @var MockInterface|EntityManagerInterface $entityManager */
        $entityManager = \Mockery::mock(EntityManagerInterface::class);
        $entityManager
            ->shouldReceive('getRepository')
            ->with(CacheValidatorHeaders::class)
            ->andReturn($cacheValidatorHeadersRepository);

        $entityManager
            ->shouldReceive('remove')
            ->with($cacheValidatorHeaders);

        $entityManager
            ->shouldReceive('flush')
            ->withNoArgs();

        $cacheValidatorHeadersService = new CacheValidatorHeadersService($entityManager);
        $cacheValidatorHeadersService->clear($limit);

        $this->addToAssertionCount(\Mockery::getContainer()->mockery_getExpectationCount());
    }

    public function clearDataProvider(): array
    {
        return [
            'no limit' => [
                'limit' => null,
            ],
            'has limit' => [
                'limit' => 10,
            ],
        ];
    }

    public function testCount()
    {
        $count = 3;

        $cacheValidatorHeadersRepository = \Mockery::mock(CacheValidatorHeadersRepository::class);
        $cacheValidatorHeadersRepository
            ->shouldReceive('count')
            ->andReturn($count);

        /* @var MockInterface|EntityManagerInterface $entityManager */
        $entityManager = \Mockery::mock(EntityManagerInterface::class);
        $entityManager
            ->shouldReceive('getRepository')
            ->with(CacheValidatorHeaders::class)
            ->andReturn($cacheValidatorHeadersRepository);

        $cacheValidatorHeadersService = new CacheValidatorHeadersService($entityManager);

        $this->assertEquals($count, $cacheValidatorHeadersService->count());
    }

    private function createFindEntityRepository(
        array $findOneByArg,
        ?CacheValidatorHeaders $findOneByResult
    ): CacheValidatorHeadersRepository {
        /* @var MockInterface|CacheValidatorHeadersRepository $cacheValidatorHeadersRepository */
        $cacheValidatorHeadersRepository = \Mockery::mock(CacheValidatorHeadersRepository::class);

        $cacheValidatorHeadersRepository
            ->shouldReceive('findOneBy')
            ->with($findOneByArg)
            ->andReturn($findOneByResult);

        return $cacheValidatorHeadersRepository;
    }

    private function createEntityManager(): EntityManagerInterface
    {
        /* @var MockInterface|EntityManagerInterface $entityManager */
        $entityManager = \Mockery::mock(EntityManagerInterface::class);

        return $entityManager;
    }

    protected function tearDown()
    {
        parent::tearDown();

        \Mockery::close();
    }
}
