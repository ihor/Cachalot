<?php

namespace Cachalot;

class XcacheCache extends AbstractCache
{
    /**
     * @param string $prefix
     * @throws \RuntimeException
     */
    public function __construct($prefix = '')
    {
        if (!extension_loaded('xcache')) {
            throw new \RuntimeException('Unable to use XCache cache as XCache extension is not enabled.');
        }

        parent::__construct($prefix);
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
    public function getCached($callback, array $args = array(), $expireIn = 0, $suffix = null, $useSuffixAsKey = false)
    {
        if (!is_callable($callback)) {
            throw new \InvalidArgumentException('First argument of getCached method has to be a valid callback');
        }

        $key = $this->getCallbackCacheKey($callback, $args, $suffix);

        if (xcache_isset($key)) {
            return $this->unserializeCompound(xcache_get($key));
        }

        $result = call_user_func_array($callback, $args);
        xcache_set($key, $this->serializeCompound($result), $expireIn);

        return $result;
    }

    /**
     * Returns true if cache contains entry with given key
     *
     * @param string $key
     * @return bool
     */
    public function contains($key)
    {
        return xcache_isset($this->prepareKey($key));
    }

    /**
     * Returns cached value by key or false if there is no cache entry for the given key
     *
     * @param string $key
     * @return bool|mixed
     */
    public function get($key)
    {
        if ($value = xcache_get($this->prepareKey($key))) {
            return $this->unserializeCompound($value);
        }

        return false;
    }

    /**
     * Caches value by key
     *
     * @param string $key
     * @param mixed $value
     * @param int $expireIn Seconds
     * @return bool
     */
    public function set($key, $value, $expireIn = 0)
    {
        return xcache_set($this->prepareKey($key), $this->serializeCompound($value), $expireIn);
    }

    /**
     * Deletes cache entry by key
     *
     * @param string $key
     * @return bool
     */
    public function delete($key)
    {
        return xcache_unset($this->prepareKey($key));
    }

    /**
     * Deletes all cache entries
     *
     * @return bool
     */
    public function clear()
    {
        xcache_clear_cache(XC_TYPE_VAR);
        return true;
    }

}
