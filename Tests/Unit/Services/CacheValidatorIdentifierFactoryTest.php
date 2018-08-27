<?php

namespace SimplyTestable\PageCacheBundle\Tests\Unit\Services;

use Mockery\MockInterface;
use PHPUnit\Framework\TestCase;
use SimplyTestable\PageCacheBundle\Model\CacheValidatorIdentifier;
use SimplyTestable\PageCacheBundle\Services\CacheValidatorIdentifierFactory;
use Symfony\Component\HttpFoundation\Request;
use webignition\SimplyTestableUserInterface\UserInterface;
use webignition\SimplyTestableUserManagerInterface\UserManagerInterface;

class CacheValidatorIdentifierFactoryTest extends TestCase
{
    const USER_USERNAME = 'user@example.com';

    /**
     * @dataProvider createDataProvider
     *
     * @param UserManagerInterface $userManager
     * @param Request $request
     * @param array $parameters
     * @param array $expectedCacheValidatorIdentifierParameters
     */
    public function testCreate(
        UserManagerInterface $userManager,
        Request $request,
        array $parameters,
        array $expectedCacheValidatorIdentifierParameters
    ) {
        $cacheValidatorIdentifierFactory = new CacheValidatorIdentifierFactory($userManager);

        $cacheValidatorIdentifier = $cacheValidatorIdentifierFactory->create($request, $parameters);

        $this->assertInstanceOf(CacheValidatorIdentifier::class, $cacheValidatorIdentifier);
        $this->assertEquals($expectedCacheValidatorIdentifierParameters, $cacheValidatorIdentifier->getParameters());
    }

    public function createDataProvider(): array
    {
        $user = $this->createUser(self::USER_USERNAME);

        return [
            'not logged in' => [
                'userManager' => $this->createUserManager($user, false),
                'request' => $this->createRequest([
                    '_route' => 'route_name',
                ]),
                'parameters' => [],
                'expectedCacheValidatorIdentifierParameters' => [
                    'route' => 'route_name',
                    'user' => self::USER_USERNAME,
                    'is_logged_in' => false,
                ],
            ],
            'not logged in, has parameters' => [
                'userManager' => $this->createUserManager($user, false),
                'request' => $this->createRequest([
                    '_route' => 'route_name',
                ]),
                'parameters' => [
                    'foo' => 'bar',
                ],
                'expectedCacheValidatorIdentifierParameters' => [
                    'route' => 'route_name',
                    'user' => self::USER_USERNAME,
                    'is_logged_in' => false,
                    'foo' => 'bar',
                ],
            ],
            'is logged in' => [
                'userManager' => $this->createUserManager($user, true),
                'request' => $this->createRequest([
                    '_route' => 'route_name',
                ]),
                'parameters' => [],
                'expectedCacheValidatorIdentifierParameters' => [
                    'route' => 'route_name',
                    'user' => self::USER_USERNAME,
                    'is_logged_in' => true,
                ],
            ],
            'has accept header' => [
                'userManager' => $this->createUserManager($user, true),
                'request' => $this->createRequest(
                    [
                        '_route' => 'route_name',
                    ],
                    [
                        'accept' => 'foo/bar',
                    ]
                ),
                'parameters' => [],
                'expectedCacheValidatorIdentifierParameters' => [
                    'route' => 'route_name',
                    'user' => self::USER_USERNAME,
                    'is_logged_in' => true,
                    'http-header-accept' => 'foo/bar',
                ],
            ],
        ];
    }

    private function createRequest(array $attributes, array $headers = []): Request
    {
        $request = new Request([], [], $attributes);

        foreach ($headers as $key => $value) {
            $request->headers->set($key, $value);
        }

        return $request;
    }

    private function createUserManager(UserInterface $user, bool $isLoggedIn): UserManagerInterface
    {
        /* @var MockInterface|UserManagerInterface $userManager */
        $userManager = \Mockery::mock(UserManagerInterface::class);

        $userManager
            ->shouldReceive('getUser')
            ->withNoArgs()
            ->andReturn($user);

        $userManager
            ->shouldReceive('isLoggedIn')
            ->withNoArgs()
            ->andReturn($isLoggedIn);

        return $userManager;
    }

    private function createUser($username): UserInterface
    {
        /* @var MockInterface|UserInterface $user */
        $user = \Mockery::mock(UserInterface::class);
        $user
            ->shouldReceive('getUsername')
            ->withNoArgs()
            ->andReturn($username);

        return $user;
    }

    protected function tearDown()
    {
        parent::tearDown();

        \Mockery::close();
    }
}
