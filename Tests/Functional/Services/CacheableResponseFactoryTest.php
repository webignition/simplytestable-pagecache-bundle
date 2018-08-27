<?php

namespace SimplyTestable\PageCacheBundle\Tests\Functional\Services;

use Doctrine\ORM\EntityManagerInterface;
use Mockery\MockInterface;
use PHPUnit\Framework\TestCase;
use SimplyTestable\PageCacheBundle\Entity\CacheValidatorHeaders;
use SimplyTestable\PageCacheBundle\Model\CacheValidatorIdentifier;
use SimplyTestable\PageCacheBundle\Repository\CacheValidatorHeadersRepository;
use SimplyTestable\PageCacheBundle\Services\CacheableResponseFactory;
use SimplyTestable\PageCacheBundle\Services\CacheValidatorHeadersService;
use SimplyTestable\PageCacheBundle\Services\CacheValidatorIdentifierFactory;
use SimplyTestable\PageCacheBundle\Tests\Functional\AbstractFunctionalTestCase;
use SimplyTestable\PageCacheBundle\Tests\Functional\EntityManagerProxy;
use SimplyTestable\PageCacheBundle\Tests\Functional\UserManagerProxy;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use webignition\SimplyTestableUserInterface\UserInterface;
use webignition\SimplyTestableUserManagerInterface\UserManagerInterface;

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
            ->with(['identifier' => 'cf4adad68ede925ef3d3a9e6c95af8da'])
            ->andReturn(null);

        /* @var UserManagerProxy $userManagerProxy */
        $userManagerProxy = $this->container->get(UserManagerProxy::class);
        $userManagerMock = $userManagerProxy->getMock();

        $userManagerMock
            ->shouldReceive('getUser')
            ->andReturn($this->user);

        $userManagerMock
            ->shouldReceive('isLoggedIn')
            ->andReturn(true);

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
