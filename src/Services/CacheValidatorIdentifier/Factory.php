<?php

namespace SimplyTestable\PageCacheBundle\Services\CacheValidatorIdentifier;

use SimplyTestable\PageCacheBundle\Model\CacheValidatorIdentifier;
use webignition\SimplyTestableUserManagerInterface\UserManagerInterface;

class Factory
{
    /**
     * @var UserManagerInterface
     */
    private $userManager;

    public function __construct(UserManagerInterface $userManager)
    {
        $this->userManager = $userManager;
    }

    /**
     * @param array $parameters
     *
     * @return CacheValidatorIdentifier
     */
    public function create(array $parameters = [])
    {
        $user = $this->userManager->getUser();

        $parameters['user'] = $user->getUsername();
        $parameters['is_logged_in'] = $this->userManager->isLoggedIn();

        return new CacheValidatorIdentifier($parameters);
    }
}
