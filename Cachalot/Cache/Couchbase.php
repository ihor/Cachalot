<?php

namespace Cachalot\Cache;

class Couchbase extends AbstractCache
{
    /**
     * @var \Couchbase
     */
    private $cache;

    /**
     * @param \Couchbase $couchbase
     * @param string $prefix
     * @throws \RuntimeException
     */
    public function __construct($couchbase, $prefix = '')
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
    public function getCached($callback, $params = array(), $expireIn = 0, $cacheIdSuffix = null)
    {
        $id = $this->getCallbackCacheId($callback, $params, $cacheIdSuffix);

        if (false === $result = $this->cache->get($id)) {
            $result = $this->call($callback, $params);
            $this->cache->set($id, $result, $expireIn);
        }

        return $result;
    }

}
