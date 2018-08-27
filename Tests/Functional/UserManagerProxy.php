<?php

namespace SimplyTestable\PageCacheBundle\Tests\Functional;

use Mockery\MockInterface;
use webignition\SimplyTestableUserInterface\UserInterface;
use webignition\SimplyTestableUserManagerInterface\UserManagerInterface;

class UserManagerProxy implements UserManagerInterface
{
    /**
     * @var MockInterface|UserManagerInterface
     */
    private $userManagerMock;

    public function __construct()
    {
        $this->userManagerMock = \Mockery::mock(UserManagerInterface::class);
    }

    /**
     * @return MockInterface|UserManagerInterface
     */
    public function getMock()
    {
        return $this->userManagerMock;
    }

    public function getUser(): UserInterface
    {
        return $this->userManagerMock->getUser();
    }

    public function isLoggedIn(): bool
    {
        return $this->userManagerMock->isLoggedIn();
    }
}