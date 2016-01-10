<?php

namespace Cachalot;

class RedisCache extends AbstractCache
{
    /**
     * @var \Redis
     */
    private $cache;

    /**
     * @param \Redis $redis
     * @param string $prefix
     * @throws \RuntimeException
     */
    public function __construct(\Redis $redis, $prefix = '')
    {
        $this->cache = $redis;
        parent::__construct($prefix);
    }

    /**
     * Returns cached $callback result
     *
     * @param callable $callback
     * @param array $args Callback arguments
     * @param int $expireIn Seconds
     * @param string|null $cacheKeySuffix Is needed to avoid collisions when callback is an anonymous function
     * @return mixed
     * @throws \InvalidArgumentException
     */
    public function getCached($callback, array $args = array(), $expireIn = 0, $cacheKeySuffix = null)
    {
        if (!is_callable($callback)) {
            throw new \InvalidArgumentException('First argument of getCached method has to be a valid callback');
        }

        $key = $this->getCallbackCacheKey($callback, $args, $cacheKeySuffix);

        if (false === $result = $this->cache->get($key)) {
            $result = call_user_func_array($callback, $args);
            $this->cache->set($key, $this->serializeCompound($result), $expireIn);
        }

        return $this->unserializeCompound($result);
    }

    /**
     * Returns true if cache contains entry with given key
     *
     * @param string $key
     * @return bool
     */
    public function contains($key)
    {
        return $this->cache->exists($this->prefixize($key));
    }

    /**
     * Returns cached value by key
     *
     * @param string $key
     * @return bool|mixed
     */
    public function get($key)
    {
        if (false === $value = $this->cache->get($this->prefixize($key))) {
            return false;
        }

        return $this->unserializeCompound($value);
    }

    /**
     * Returns cached value by key or false if there is no cache entry for the given key
     *
     * @param string $key
     * @param mixed $value
     * @param int $expireIn Seconds
     * @return bool
     */
    public function set($key, $value, $expireIn = 0)
    {
        return $this->cache->set($this->prefixize($key), $this->serializeCompound($value), $expireIn);
    }

    /**
     * Deletes cache entry by key
     *
     * @param string $key
     * @return bool
     */
    public function delete($key)
    {
        return (bool) $this->cache->del($this->prefixize($key));
    }

    /**
     * Deletes all cache entries
     *
     * @return bool
     */
    public function clear()
    {
        return $this->cache->flushAll();
    }

}
