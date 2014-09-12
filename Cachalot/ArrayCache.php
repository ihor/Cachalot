<?php

namespace Cachalot;

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

    /**
     * @param string $id
     * @return bool
     */
    public function contains($id)
    {
        return array_key_exists($id, $this->cache) && $this->expire[$id] >= time();
    }


    /**
     * @param string $id
     * @return bool|mixed
     */
    public function get($id)
    {
        return $this->contains($id)
            ? $this->cache[$id]
            : false;
    }

    /**
     * @param string $id
     * @param mixed $value
     * @param int $expireIn
     * @return bool
     */
    public function set($id, $value, $expireIn = 0)
    {
        $this->cache[$id] = $value;
        $this->expire[$id] = time();

        return true;
    }

    /**
     * @param string $id
     * @return bool
     */
    public function delete($id)
    {
        unset($this->cache[$id], $this->expire[$id]);

        return true;
    }

}
