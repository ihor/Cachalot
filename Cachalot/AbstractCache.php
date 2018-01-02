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
     * Returns cached $callback result
     *
     * @param callable $callback
     * @param array $args Callback arguments
     * @param int $expireIn Seconds
     * @param string|null $suffix Is needed to avoid cache collisions when callback is an anonymous function
     * @param bool $useSuffixAsKey When is true then instead automatic cache key generation the value provided in $suffix will be used as cache key
     * @return mixed
     * @throws \InvalidArgumentException
     */
    public function __invoke($callback, array $args = array(), $expireIn = 0, $suffix = null, $useSuffixAsKey = false)
    {
        return $this->getCached($callback, $args, $expireIn, $suffix, $useSuffixAsKey);
    }

    /**
     * @param \callable $callback
     * @param array $args
     * @param string $suffix
     * @param bool $useSuffixAsKey When is true then instead automatic cache key generation the value provided in $suffix will be used as cache key
     * @return string
     */
    protected function getCallbackCacheKey($callback, array $args = array(), $suffix = null, $useSuffixAsKey = false)
    {
        if ($useSuffixAsKey) {
            return $this->prepareKey($suffix);
        }

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
        return $this->prepareKey($callbackStr . $argStr . ($suffix ? $suffix : ''));
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
