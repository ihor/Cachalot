<?php

namespace Cachalot\Cache;

class Memcache extends AbstractCache
{
    /**
     * @var \Memcache
     */
    private $cache;

    /**
     * @param string $prefix
     * @param \Memcache $memcache
     * @throws \RuntimeException
     */
    public function __construct($prefix = '', $memcache)
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
    public function getCached($callback, $params = array(), $expireIn = 0, $cacheIdSuffix = null)
    {
        $id = $this->getCallbackCacheId($callback, $params, $cacheIdSuffix);

        if (false === $result = $this->cache->get($id)) {
            $result = $this->call($callback, $params);
            $this->cache->set($id, $result, false, $expireIn);
        }

        return $result;
    }

}
