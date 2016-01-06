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
            $this->cache->set($id, $this->serialize($result), $expireIn);
        }

        return $this->unserialize($result);
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

        return $this->unserialize($value);
    }

    /**
     * @param string $id
     * @param mixed $value
     * @param int $expireIn
     * @return bool
     */
    public function set($id, $value, $expireIn = 0)
    {
        return $this->cache->set($this->prefixize($id), $this->serialize($value), $expireIn);
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

    /**
     * @param mixed $value
     * @return string
     */
    private function serialize($value)
    {
        return is_array($value) || is_object($value) ? serialize($value) : $value;
    }

    /**
     * @param string $value
     * @return mixed
     */
    private function unserialize($value)
    {
        if (!is_string($value)) {
            return $value;
        }

        if (strlen($value) < 2 || $value[1] !== ':'  || ($value[0] !== 'a' && $value[0] !== 'O' && $value[0] !== 'C')) {
            return $value;
        }

        if ($unserialized = @unserialize($value)) {
            return $unserialized;
        }

        return $value;
    }

}
