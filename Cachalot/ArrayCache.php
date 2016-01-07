<?php

namespace Cachalot;

class ArrayCache extends AbstractCache
{
    /**
     * @var array
     */
    private $cache = array();

    /**
     * @var array
     */
    private $expire = array();

    /**
     * Returns cached $callback result
     *
     * @param callable $callback
     * @param array $args Callback arguments
     * @param int $expireIn Seconds
     * @param string|null $cacheKeySuffix Is needed to avoid collisions when caches results of anonymous functions
     * @return mixed
     * @throws \InvalidArgumentException
     */
    public function getCached($callback, array $args = array(), $expireIn = 0, $cacheKeySuffix = null)
    {
        if (!is_callable($callback)) {
            throw new \InvalidArgumentException('First argument of getCached method has to be a valid callback');
        }

        $key = $this->getCallbackCacheKey($callback, $args, $cacheKeySuffix);
        $now = time();

        if (!isset($this->cache[$key]) || !array_key_exists($key, $this->cache) || ($this->expire[$key] < $now)) {
            $result = call_user_func_array($callback, $args);
            $this->cache[$key] = $result;
            $this->expire[$key] = $now + $expireIn;
        }

        return $this->cache[$key];
    }

    /**
     * Returns true if cache contains entry with given key
     *
     * @param string $key
     * @return bool
     */
    public function contains($key)
    {
        return (isset($this->cache[$key]) || array_key_exists($key, $this->cache)) && $this->expire[$key] >= time();
    }

    /**
     * Returns cached value by key or false if there is no cache entry for the given key
     *
     * @param string $key
     * @return bool|mixed
     */
    public function get($key)
    {
        return $this->contains($key)
            ? $this->cache[$key]
            : false;
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
        $this->cache[$key] = $value;
        $this->expire[$key] = time();
        return true;
    }

    /**
     * Deletes cache entry by key
     *
     * @param string $key
     * @return bool
     */
    public function delete($key)
    {
        unset($this->cache[$key], $this->expire[$key]);
        return true;
    }

    /**
     * Deletes all cache entries
     *
     * @return bool
     */
    public function clear()
    {
        $this->cache = array();
        return true;
    }

}
