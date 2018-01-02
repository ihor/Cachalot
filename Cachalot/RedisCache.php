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
        return $this->cache->exists($this->prepareKey($key));
    }

    /**
     * Returns cached value by key
     *
     * @param string $key
     * @return bool|mixed
     */
    public function get($key)
    {
        if (false === $value = $this->cache->get($this->prepareKey($key))) {
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
        return $this->cache->set($this->prepareKey($key), $this->serializeCompound($value), $expireIn);
    }

    /**
     * Deletes cache entry by key
     *
     * @param string $key
     * @return bool
     */
    public function delete($key)
    {
        return (bool) $this->cache->del($this->prepareKey($key));
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
