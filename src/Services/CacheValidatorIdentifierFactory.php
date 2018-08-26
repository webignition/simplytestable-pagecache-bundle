<?php

namespace SimplyTestable\PageCacheBundle\Services;

use SimplyTestable\PageCacheBundle\Model\CacheValidatorIdentifier;
use Symfony\Component\HttpFoundation\Request;
use webignition\SimplyTestableUserManagerInterface\UserManagerInterface;

class CacheValidatorIdentifierFactory
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
     * @param Request $request
     * @param array $parameters
     *
     * @return CacheValidatorIdentifier
     */
    public function create(Request $request, array $parameters = [])
    {
        $user = $this->userManager->getUser();

        $identifier = new CacheValidatorIdentifier();
        $identifier->setParameter('route', $request->attributes->get('_route'));
        $identifier->setParameter('user', $user->getUsername());
        $identifier->setParameter('is_logged_in', $this->userManager->isLoggedIn());

        if ($request->headers->has('accept')) {
            $identifier->setParameter('http-header-accept', $request->headers->get('accept'));
        }

        foreach ($parameters as $key => $value) {
            $identifier->setParameter($key, $value);
        }

        return $identifier;
    }
}
