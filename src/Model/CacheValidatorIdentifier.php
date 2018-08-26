<?php

namespace SimplyTestable\PageCacheBundle\Model;

class CacheValidatorIdentifier
{
    /**
     * @var array
     */
    private $parameters = [];

    /**
     * @param string $key
     *
     * @param mixed $value
     */
    public function setParameter($key, $value)
    {
        $this->parameters[$key] = $value;
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
