<?php

namespace Cachalot;

class MemcacheCache extends AbstractCache
{
    /**
     * @var int
     */
    protected static $maxKeyLength = 250;

    /**
     * @var \Memcache
     */
    private $cache;

    /**
     * @param \Memcache $memcache
     * @param string $prefix
     * @throws \RuntimeException
     */
    public function __construct(\Memcache $memcache, $prefix = '')
    {
        $this->cache = $memcache;
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
            $this->cache->set($id, $result, false, $expireIn);
        }

        return $result;
    }

    /**
     * @param string $id
     * @return bool
     */
    public function contains($id)
    {
        return (bool) $this->cache->get($this->prefixize($id));
    }

    /**
     * @param string $id
     * @return bool|mixed
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
        return $this->cache->set($this->prefixize($id), $value, false, $expireIn);
    }

    /**
     * @param string $id
     * @return bool
     */
    public function delete($id)
    {
        return $this->cache->delete($this->prefixize($id));
    }

    /**
     * @return bool
     */
    public function clear()
    {
        return $this->cache->flush();
    }

}
