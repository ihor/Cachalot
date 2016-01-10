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
            return (bool) $this->cache->get($this->prefixize($key));
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
            return $this->cache->get($this->prefixize($key));
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
            return (bool) $this->cache->set($this->prefixize($key), $value, $expireIn);
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
            return (bool) $this->cache->delete($this->prefixize($key));
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
