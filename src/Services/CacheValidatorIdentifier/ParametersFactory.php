<?php

namespace SimplyTestable\PageCacheBundle\Services\CacheValidatorIdentifier;

use Symfony\Component\HttpFoundation\Request;

class ParametersFactory
{
    public function createFromRequest(Request $request): array
    {
        $parameters = [
            'route' => $request->attributes->get('_route'),
        ];

        if ($request->headers->has('accept')) {
            $parameters['http-header-accept'] = $request->headers->get('accept');
        }

        return $parameters;
    }
}
