<?php

namespace SimplyTestable\PageCacheBundle\Tests\Unit\Entity;

use PHPUnit\Framework\TestCase;
use SimplyTestable\PageCacheBundle\Entity\CacheValidatorHeaders;

class CacheValidatorHeadersTest extends TestCase
{
    /**
     * @dataProvider getEtagDataProvider
     *
     * @param string $identifier
     * @param string $expectedEtag
     */
    public function testGetEtag(string $identifier, string $expectedEtag)
    {
        $cacheValidatorHeaders = new CacheValidatorHeaders();
        $cacheValidatorHeaders->setIdentifier($identifier);

        $this->assertEquals($expectedEtag, $cacheValidatorHeaders->getETag());
        $this->assertNull($cacheValidatorHeaders->getId());
    }

    public function getEtagDataProvider(): array
    {
        return [
            'foo' => [
                'identifier' => 'foo',
                'expectedEtag' => 'acbd18db4cc2f85cedef654fccc4a4d8',
            ],
            'bar' => [
                'identifier' => 'bar',
                'expectedEtag' => '37b51d194a7513e45b56f6524f2d51f2',
            ],
        ];
    }
}
