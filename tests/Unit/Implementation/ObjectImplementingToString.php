<?php

namespace SimplyTestable\PageCacheBundle\Tests\Unit\Implementation;

class ObjectImplementingToString
{
    /**
     * @var int
     */
    private $value;

    public function __construct(int $value)
    {
        $this->value = $value;
    }

    public function __toString(): string
    {
        return (string)$this->value;
    }
}
