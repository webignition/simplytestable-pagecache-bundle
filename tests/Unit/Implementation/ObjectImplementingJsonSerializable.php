<?php

namespace SimplyTestable\PageCacheBundle\Tests\Unit\Implementation;

class ObjectImplementingJsonSerializable implements \JsonSerializable
{
    /**
     * @var array
     */
    private $values;

    public function __construct(array $values)
    {
        $this->values = $values;
    }

    public function jsonSerialize(): array
    {
        return $this->values;
    }
}
