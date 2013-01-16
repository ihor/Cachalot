<?php

/**
 * @author Ihor Burlachenko
 */

namespace Cachalot\Cache;

class ArrayCache extends AbstractCache
{
    /**
     * @var array
     */
    private $cache = array();

    /**
     * @var array
     */
    private $expire = array();

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
        $now = time();

        if (!array_key_exists($id, $this->cache) || ($this->expire[$id] < $now)) {
            $result = $this->call($callback, $params);
            $this->cache[$id] = $result;
            $this->expire[$id] = $now + $expireIn;
        }

        return $this->cache[$id];
    }

}
