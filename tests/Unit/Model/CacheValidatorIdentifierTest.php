<?php

namespace SimplyTestable\PageCacheBundle\Tests\Unit\Model;

use PHPUnit\Framework\TestCase;
use SimplyTestable\PageCacheBundle\Entity\CacheValidatorHeaders;
use SimplyTestable\PageCacheBundle\Model\CacheValidatorIdentifier;
use SimplyTestable\PageCacheBundle\Tests\Unit\Implementation\ObjectImplementingJsonSerializable;
use SimplyTestable\PageCacheBundle\Tests\Unit\Implementation\ObjectImplementingToString;

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
        $cacheValidatorIdentifier = new CacheValidatorIdentifier($parameters);

        $this->assertEquals($expectedParameters, $cacheValidatorIdentifier->getParameters());
    }

    public function setParametersGetParametersDataProvider(): array
    {
        return [
            'no parameters' => [
                'parameters' => [],
                'expectedParameters' => [],
            ],
            'has parameters' => [
                'parameters' => [
                    'true' => true,
                    'false' => false,
                    'int' => 1,
                    'string' => 'foo',
                ],
                'expectedParameters' => [
                    'true' => true,
                    'false' => false,
                    'int' => 1,
                    'string' => 'foo',
                ],
            ],
        ];
    }

    /**
     * @dataProvider castToStringSuccessDataProvider
     *
     * @param array $parameters
     * @param string $expectedStringRepresentation
     */
    public function testCastToStringSuccess(array $parameters, string $expectedStringRepresentation)
    {
        $cacheValidatorIdentifier = new CacheValidatorIdentifier($parameters);

        $this->assertEquals($expectedStringRepresentation, $cacheValidatorIdentifier->__toString());
    }

    public function castToStringSuccessDataProvider(): array
    {
        return [
            'no parameters' => [
                'parameters' => [],
                'expectedStringRepresentation' => 'd41d8cd98f00b204e9800998ecf8427e',
            ],
            'has parameters' => [
                'parameters' => [
                    'null' => null,
                    'bool true' => true,
                    'bool false' => false,
                    'scalar int' => 1,
                    'scalar float' => M_PI,
                    'string' => 'foo',
                    'array' => [
                        'a' => 1,
                        'b' => true,
                        'c' => 'foo',
                    ],
                    'object implementing __toString' => new ObjectImplementingToString(123),
                    'object implementing jsonSerializable' => new ObjectImplementingJsonSerializable([1, 2, 3, ]),
                ],
                'expectedStringRepresentation' => 'bc5e57f0c2c360f0eeb430b50146ea47',
            ],
        ];
    }

    public function testCastToStringInvalidObject()
    {
        $cacheValidatorIdentifier = new CacheValidatorIdentifier([
            'value' => new CacheValidatorHeaders(),
        ]);

        $this->expectException(\UnexpectedValueException::class);
        $this->expectExceptionCode(CacheValidatorIdentifier::EXCEPTION_CODE_OBJECT_CANNOT_BE_CONVERTED_TO_STRING);
        $this->expectExceptionMessage('Object of type '. CacheValidatorHeaders::class .' cannot be converted to a string');

        $cacheValidatorIdentifier->__toString();
    }

    public function testCastToStringInvalidValue()
    {
        $thisFQCNParts = explode('\\', get_class($this));
        $className = $thisFQCNParts[count($thisFQCNParts) - 1];
        $thisPath = __DIR__ . '/' . $className . '.php';

        $fileResource = fopen($thisPath, 'r');

        $cacheValidatorIdentifier = new CacheValidatorIdentifier([
            'key_name' => $fileResource,
        ]);

        $this->expectException(\UnexpectedValueException::class);
        $this->expectExceptionCode(CacheValidatorIdentifier::EXCEPTION_CODE_VALUE_CANNOT_BE_CONVERTED_TO_STRING);
        $this->expectExceptionMessage('Value with key "key_name" cannot be converted to a string');

        $cacheValidatorIdentifier->__toString();
    }
}
