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
     * @throws \InvalidArgumentException
     * @param \callable $callback
     * @param array $params
     * @param int $expireIn Seconds
     * @param mixed $cacheIdSuffix
     * @return mixed
     */
    public function getCached($callback, array $params = array(), $expireIn = 0, $cacheIdSuffix = null)
    {
        if (!is_callable($callback)) {
            throw new \InvalidArgumentException('First argument of getCached method has to be a valid callback');
        }

        $id = $this->getCallbackCacheId($callback, $params, $cacheIdSuffix);

        if (!($result = $this->cache->get($id))) {
            $result = call_user_func_array($callback, $params);
            $this->cache->set($id, $result, $expireIn);
        }

        return $result;
    }

    /**
     * @param string $id
     * @return bool
     */
    public function contains($id)
    {
        try {
            return (bool) $this->cache->get($this->prefixize($id));
        }
        catch (\CouchbaseException $e) {
            return false;
        }
    }

    /**
     * @param string $id
     * @return bool|mixed
     */
    public function get($id)
    {
        try {
            return $this->cache->get($this->prefixize($id));
        }
        catch (\CouchbaseException $e) {
            return false;
        }
    }

    /**
     * @param string $id
     * @param mixed $value
     * @param int $expireIn
     * @return bool
     */
    public function set($id, $value, $expireIn = 0)
    {
        try {
            return (bool) $this->cache->set($this->prefixize($id), $value, $expireIn);
        }
        catch (\CouchbaseException $e) {
            return false;
        }
    }

    /**
     * @param string $id
     * @return bool
     */
    public function delete($id)
    {
        try {
            return (bool) $this->cache->delete($this->prefixize($id));
        }
        catch (\CouchbaseException $e) {
            return false;
        }
    }

    /**
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
