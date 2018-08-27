<?php

namespace SimplyTestable\PageCacheBundle\Tests\Unit\Services\CacheValidatorIdentifier;

use Mockery\MockInterface;
use PHPUnit\Framework\TestCase;
use SimplyTestable\PageCacheBundle\Model\CacheValidatorIdentifier;
use SimplyTestable\PageCacheBundle\Services\CacheValidatorIdentifier\Factory as CacheValidatorIdentifierFactory;
use webignition\SimplyTestableUserInterface\UserInterface;
use webignition\SimplyTestableUserManagerInterface\UserManagerInterface;

class FactoryTest extends TestCase
{
    const USER_USERNAME = 'user@example.com';

    /**
     * @dataProvider createDataProvider
     *
     * @param UserManagerInterface $userManager
     * @param array $parameters
     * @param array $expectedCacheValidatorIdentifierParameters
     */
    public function testCreate(
        UserManagerInterface $userManager,
        array $parameters,
        array $expectedCacheValidatorIdentifierParameters
    ) {
        $cacheValidatorIdentifierFactory = new CacheValidatorIdentifierFactory($userManager);

        $cacheValidatorIdentifier = $cacheValidatorIdentifierFactory->create($parameters);

        $this->assertInstanceOf(CacheValidatorIdentifier::class, $cacheValidatorIdentifier);
        $this->assertEquals($expectedCacheValidatorIdentifierParameters, $cacheValidatorIdentifier->getParameters());
    }

    public function createDataProvider(): array
    {
        $user = $this->createUser(self::USER_USERNAME);

        return [
            'not logged in' => [
                'userManager' => $this->createUserManager($user, false),
                'parameters' => [],
                'expectedCacheValidatorIdentifierParameters' => [
                    'user' => self::USER_USERNAME,
                    'is_logged_in' => false,
                ],
            ],
            'is logged in' => [
                'userManager' => $this->createUserManager($user, true),
                'parameters' => [],
                'expectedCacheValidatorIdentifierParameters' => [
                    'user' => self::USER_USERNAME,
                    'is_logged_in' => true,
                ],
            ],
        ];
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
