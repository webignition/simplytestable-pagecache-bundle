<?php

namespace SimplyTestable\PageCacheBundle\Tests\Unit\Services\CacheValidatorIdentifier;

use PHPUnit\Framework\TestCase;
use SimplyTestable\PageCacheBundle\Services\CacheValidatorIdentifier\ParametersFactory;
use Symfony\Component\HttpFoundation\Request;

class ParametersFactoryTest extends TestCase
{
    /**
     * @dataProvider createFromRequestDataProvider
     *
     * @param Request $request
     * @param array $expectedParameters
     */
    public function testCreateFromRequest(Request $request, array $expectedParameters)
    {
        $parametersFactory = new ParametersFactory();

        $this->assertEquals($expectedParameters, $parametersFactory->createFromRequest($request));
    }

    public function createFromRequestDataProvider(): array
    {
        return [
            'route only' => [
                'request' => $this->createRequest([
                    '_route' => 'route_only_route_name',
                ]),
                'expectedParameters' => [
                    'route' => 'route_only_route_name',
                ],
            ],
            'route and accept header' => [
                'request' => $this->createRequest(
                    [
                        '_route' => 'route_and_accept_header_route_name',
                    ],
                    [
                        'accept' => 'foo/bar',
                    ]
                ),
                'expectedParameters' => [
                    'route' => 'route_and_accept_header_route_name',
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

    protected function tearDown()
    {
        parent::tearDown();

        \Mockery::close();
    }
}
