<?php

namespace Cachalot;

abstract class AbstractCache implements \Cachalot\Cache
{
    /**
     * @var int
     */
    protected static $maxKeyLength = 250;

    /**
     * @var string
     */
    private $prefix;

    /**
     * @param string $prefix
     */
    public function __construct($prefix = '')
    {
        $this->prefix = $prefix;
    }

    /**
     * @param \callable $callback
     * @param array $args
     * @param string $cacheKeySuffix
     * @return string
     */
    protected function getCallbackCacheKey($callback, array $args = array(), $cacheKeySuffix = null)
    {
        if (is_array($callback)) {
            $callbackStr = (is_string($callback[0]) ? $callback[0] : get_class($callback[0])) . '::' . $callback[1];
        }
        else if (is_object($callback)) {
            $callbackStr = get_class($callback);
        }
        else {
            $callbackStr = $callback;
        }

        $argStr = '(' . implode(',', array_map(array($this, 'stringilizeCallbackArg'), $args)) . ')';
        return $this->prepareKey($callbackStr . $argStr . ($cacheKeySuffix ? $cacheKeySuffix : ''));
    }

    /**
     * @param mixed $arg
     * @return string
     */
    protected function stringilizeCallbackArg($arg)
    {
        if (is_array($arg)) {
            return '[' . implode(',', array_map(array($this, 'stringilizeCallbackArg'), $arg)) . ']';
        }

        if (is_object($arg)) {
            return serialize($arg);
        }

        return strval($arg);
    }

    /**
     * @param string $key
     * @return string
     */
    protected function prepareKey($key)
    {
        return static::$maxKeyLength && strlen($key) > static::$maxKeyLength
            ? md5($key)
            : $this->prefix . $key;
    }

    /**
     * @param mixed $value
     * @return string
     */
    protected function serializeCompound($value)
    {
        return is_array($value) || is_object($value)? serialize($value) : $value;
    }

    /**
     * @param string $value
     * @return mixed
     */
    protected function unserializeCompound($value)
    {
        if (!is_string($value)) {
            return $value;
        }

        if (strlen($value) < 2 || $value[1] !== ':'  || ($value[0] !== 'a' && $value[0] !== 'O' && $value[0] !== 'C')) {
            return $value;
        }

        if ($unserialized = @unserialize($value)) {
            return $unserialized;
        }

        return $value;
    }

}
