<?php

namespace Cachalot;

class Couchbase2Cache extends AbstractCache
{
    /**
     * @var int
     */
    protected static $maxKeyLength = 250;

    /**
     * @var \CouchbaseBucket
     */
    private $cache;

    /**
     * @param \CouchbaseBucket $bucket
     * @param string $prefix
     * @throws \RuntimeException
     */
    public function __construct(\CouchbaseBucket $bucket, $prefix = '')
    {
        $this->cache = $bucket;
        parent::__construct($prefix);
    }

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

        try {
            $result = $this->cache->get($key)->value;
        }
        catch (\CouchbaseException $e) {
            $result = call_user_func_array($callback, $args);
            $this->cache->insert($key, $this->serializeCompound($result), array(
                'expiry' => $expireIn
            ));
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
            return $this->unserializeCompound($this->cache->get($this->prefixize($key))->value);
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
            return (bool) $this->cache->upsert($this->prefixize($key), $this->serializeCompound($value), array(
                'expiry' => $expireIn
            ));
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
            return (bool) $this->cache->remove($this->prefixize($key));
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
            $this->cache->manager()->flush();
            return true;
        }
        catch (\CouchbaseException $e) {
            return false;
        }
    }

}
