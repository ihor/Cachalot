<?php

namespace Cachalot;

class CouchbaseCache extends AbstractCache
{
    /**
     * @var int
     */
    protected static $maxKeyLength = 250;

    /**
     * @var \Couchbase
     */
    private $cache;

    /**
     * @param \Couchbase $couchbase
     * @param string $prefix
     * @throws \RuntimeException
     */
    public function __construct(\Couchbase $couchbase, $prefix = '')
    {
        $this->cache = $couchbase;
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

        if (!($result = $this->cache->get($key))) {
            $result = call_user_func_array($callback, $args);
            $this->cache->set($key, $result, $expireIn);
        }

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
        try {
            return (bool) $this->cache->get($this->prepareKey($key));
        }
        catch (\CouchbaseException $e) {
            return false;
        }
    }

    /**
     * Returns cached value by key or false if there is no cache entry for the given key
     *
     * @param string $key
     * @return bool|mixed
     */
    public function get($key)
    {
        try {
            return $this->cache->get($this->prepareKey($key));
        }
        catch (\CouchbaseException $e) {
            return false;
        }
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
        try {
            return (bool) $this->cache->set($this->prepareKey($key), $value, $expireIn);
        }
        catch (\CouchbaseException $e) {
            return false;
        }
    }

    /**
     * Deletes cache entry by key
     *
     * @param string $key
     * @return bool
     */
    public function delete($key)
    {
        try {
            return (bool) $this->cache->delete($this->prepareKey($key));
        }
        catch (\CouchbaseException $e) {
            return false;
        }
    }

    /**
     * Deletes all cache entries
     *
     * @return bool
     */
    public function clear()
    {
        try {
            return $this->cache->flush();
        }
        catch (\CouchbaseException $e) {
            return false;
        }
    }

}
