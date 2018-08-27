<?php

namespace SimplyTestable\PageCacheBundle\Tests\Functional\Services;

use Doctrine\ORM\EntityManagerInterface;
use Mockery\MockInterface;
use SimplyTestable\PageCacheBundle\Entity\CacheValidatorHeaders;
use SimplyTestable\PageCacheBundle\Services\CacheableResponseFactory;
use SimplyTestable\PageCacheBundle\Tests\Functional\AbstractFunctionalTestCase;
use SimplyTestable\PageCacheBundle\Tests\Functional\EntityManagerProxy;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use webignition\SimplyTestableUserInterface\UserInterface;

class CacheableResponseFactoryTest extends AbstractFunctionalTestCase
{
    const USER_USERNAME = 'user@example.com';

    /**
     * @var UserInterface
     */
    private $user;

    protected function setUp()
    {
        parent::setUp();

        $this->user = \Mockery::mock(UserInterface::class);
        $this->user
            ->shouldReceive('getUserName')
            ->andReturn(self::USER_USERNAME);
    }

    public function testCreateResponse()
    {
        /* @var EntityManagerProxy $entityManagerProxy */
        $entityManagerProxy = $this->container->get(EntityManagerProxy::class);
        $entityManagerMock = $entityManagerProxy->getMock();

        /* @var EntityManagerInterface|MockInterface $entityRepositoryMock */
        $entityRepositoryMock = $entityManagerProxy->getRepository(CacheValidatorHeaders::class);

        $entityRepositoryMock
            ->shouldReceive('findOneBy')
            ->with(['identifier' => 'ce2628dfb460f03c1a28e087f03828e5'])
            ->andReturn(null);

        /* @var CacheableResponseFactory $cacheableResponseFactory */
        $cacheableResponseFactory = $this->container->get(CacheableResponseFactory::class);

        $request = new Request([], [], [
            '_route' => 'route_name',
        ]);

        $parameters = [];

        $cacheableResponse = $cacheableResponseFactory->createResponse($request, $parameters);

        $this->assertInstanceOf(Response::class, $cacheableResponse);
    }
}
