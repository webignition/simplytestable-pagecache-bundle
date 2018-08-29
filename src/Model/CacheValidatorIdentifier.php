<?php

namespace SimplyTestable\PageCacheBundle\Model;

class CacheValidatorIdentifier
{
    const BOOL_TRUE_STRING = 'true';
    const BOOL_FALSE_STRING = 'false';
    const NULL_STRING = 'null';

    const EXCEPTION_CODE_OBJECT_CANNOT_BE_CONVERTED_TO_STRING = 1;
    const EXCEPTION_CODE_VALUE_CANNOT_BE_CONVERTED_TO_STRING = 2;

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
            $keyValuePairs[] = $key . ':' . $this->convertValueToString($value, $key);
        }

        return md5(implode('+', $keyValuePairs));
    }

    private function convertValueToString($value, $key): string
    {
        if (is_bool($value)) {
            return $value ? self::BOOL_TRUE_STRING : self::BOOL_FALSE_STRING;
        }

        if (is_null($value)) {
            return self::NULL_STRING;
        }

        if (is_scalar($value)) {
            return (string)$value;
        }

        if (is_array($value)) {
            return json_encode($value);
        }

        if (is_object($value)) {
            if (method_exists($value, '__toString')) {
                return (string)$value;
            }

            if ($value instanceof \JsonSerializable) {
                return json_encode($value);
            }

            throw new \UnexpectedValueException(
                sprintf('Object of type %s cannot be converted to a string', get_class($value)),
                self::EXCEPTION_CODE_OBJECT_CANNOT_BE_CONVERTED_TO_STRING
            );
        }

        throw new \UnexpectedValueException(
            sprintf('Value with key "%s" cannot be converted to a string', $key),
            self::EXCEPTION_CODE_VALUE_CANNOT_BE_CONVERTED_TO_STRING
        );
    }
}
