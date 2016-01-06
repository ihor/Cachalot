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

        if (false === $result = $this->cache->get($id)) {
            $result = call_user_func_array($callback, $params);
            $this->cache->set($id, $this->serializeCompound($result), $expireIn);
        }

        return $this->unserializeCompound($result);
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
        if (false === $value = $this->cache->get($this->prefixize($id))) {
            return false;
        }

        return $this->unserializeCompound($value);
    }

    /**
     * @param string $id
     * @param mixed $value
     * @param int $expireIn
     * @return bool
     */
    public function set($id, $value, $expireIn = 0)
    {
        return $this->cache->set($this->prefixize($id), $this->serializeCompound($value), $expireIn);
    }

    /**
     * @param string $id
     * @return bool
     */
    public function delete($id)
    {
        return (bool) $this->cache->del($this->prefixize($id));
    }

    /**
     * @return bool
     */
    public function clear()
    {
        return $this->cache->flushAll();
    }

}
