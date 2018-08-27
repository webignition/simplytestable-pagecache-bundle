<?php

namespace SimplyTestable\PageCacheBundle\Model;

class CacheValidatorIdentifier
{
    /**
     * @var array
     */
    private $parameters = [];

    public function __construct(array $parameters = [])
    {
        $this->parameters = $parameters;
    }

    /**
     * @return array
     */
    public function getParameters()
    {
        return $this->parameters;
    }

    /**
     * @return string
     */
    public function __toString()
    {
        $keyValuePairs = array();
        foreach ($this->parameters as $key => $value) {
            if (is_bool($value)) {
                $value = $value ? 'true' : 'false';
            }

            $keyValuePairs[] = $key . ':' . $value;
        }

        return md5(implode('+', $keyValuePairs));
    }
}
