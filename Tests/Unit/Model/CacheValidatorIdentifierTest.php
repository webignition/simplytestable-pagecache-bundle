<?php

namespace SimplyTestable\PageCacheBundle\Tests\Functional\Model;

use PHPUnit\Framework\TestCase;
use SimplyTestable\PageCacheBundle\Entity\CacheValidatorHeaders;
use SimplyTestable\PageCacheBundle\Model\CacheValidatorIdentifier;

class CacheValidatorIdentifierTest extends TestCase
{
    /**
     * @dataProvider setParametersGetParametersDataProvider
     *
     * @param array $parameters
     * @param array $expectedParameters
     */
    public function testSetParametersGetParameters(array $parameters, array $expectedParameters)
    {
        $cacheValidatorIdentifier = new CacheValidatorIdentifier();

        foreach ($parameters as $key => $value) {
            $cacheValidatorIdentifier->setParameter($key, $value);
        }

        $this->assertEquals($expectedParameters, $cacheValidatorIdentifier->getParameters());
    }

    public function setParametersGetParametersDataProvider(): array
    {
        return [
            'none' => [
                'parameters' => [],
                'expectedParameters' => [],
            ],
            'boolean parameter conversion' => [
                'parameters' => [
                    'foo' => true,
                    'bar' => false,
                ],
                'expectedParameters' => [
                    'foo' => 'true',
                    'bar' => 'false',
                ],
            ],
            'scalar parameters' => [
                'parameters' => [
                    'int' => 1,
                    'string' => 'foo',
                ],
                'expectedParameters' => [
                    'int' => 1,
                    'string' => 'foo',
                ],
            ],
        ];
    }

    /**
     * @dataProvider castToStringDataProvider
     *
     * @param array $parameters
     * @param string $expectedStringRepresentation
     */
    public function testCastToString(array $parameters, string $expectedStringRepresentation)
    {
        $cacheValidatorIdentifier = new CacheValidatorIdentifier();

        foreach ($parameters as $key => $value) {
            $cacheValidatorIdentifier->setParameter($key, $value);
        }

        $this->assertEquals($expectedStringRepresentation, $cacheValidatorIdentifier->__toString());
    }

    public function castToStringDataProvider(): array
    {
        return [
            'no parameters' => [
                'parameters' => [],
                'expectedStringRepresentation' => 'd41d8cd98f00b204e9800998ecf8427e',
            ],
            'has parameters' => [
                'parameters' => [
                    'foo' => true,
                    'bar' => false,
                    'int' => 1,
                    'string' => 'foo',
                ],
                'expectedStringRepresentation' => '126d389663284baf69956edd701c9d3e',
            ],
        ];
    }
}
