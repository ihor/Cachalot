<?php

namespace Cachalot;

class Redis extends AbstractCache
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
    public function __construct($redis, $prefix = '')
    {
        $this->cache = $redis;
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
    public function getCached($callback, $params = array(), $expireIn = 0, $cacheIdSuffix = null)
    {
        $id = $this->getCallbackCacheId($callback, $params, $cacheIdSuffix);

        if (false === $result = $this->cache->get($id)) {
            $result = serialize($this->call($callback, $params));
            $this->cache->set($id, $result, $expireIn);
        }

        return unserialize($result);
    }

    /**
     * @param string $id
     * @return bool
     */
    public function contains($id)
    {
        return $this->cache->exists($this->prefixize($id));
    }

    /**
     * @param string $id
     * @return bool
     */
    public function get($id)
    {
        return $this->cache->get($this->prefixize($id));
    }

    /**
     * @param string $id
     * @param mixed $value
     * @param int $expireIn
     * @return bool
     */
    public function set($id, $value, $expireIn = 0)
    {
        return $this->cache->set($this->prefixize($id), $value, $expireIn);
    }

    /**
     * @param string $id
     * @return bool
     */
    public function delete($id)
    {
        return !$this->cache->delete($this->prefixize($id));
    }

}
