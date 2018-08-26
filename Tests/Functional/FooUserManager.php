<?php

namespace SimplyTestable\PageCacheBundle\Tests\Functional;

use webignition\SimplyTestableUserInterface\UserInterface;
use webignition\SimplyTestableUserManagerInterface\UserManagerInterface;

class FooUserManager implements UserManagerInterface
{

    public function getUser(): UserInterface
    {
        // TODO: Implement getUser() method.
    }

    public function isLoggedIn(): bool
    {
        // TODO: Implement isLoggedIn() method.
    }
}