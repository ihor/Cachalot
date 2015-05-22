<?php

namespace Cachalot;

class XcacheCache extends AbstractCache
{
    /**
     * @param string $prefix
     * @throws \RuntimeException
     */
    public function __construct($prefix = '')
    {
        if (!extension_loaded('xcache')) {
            throw new \RuntimeException('Unable to use XCache cache as XCache extension is not enabled.');
        }

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

        if (xcache_isset($id)) {
            return unserialize(xcache_get($id));
        }

        $result = $this->call($callback, $params);
        xcache_set($id, serialize($result), $expireIn);

        return $result;
    }

    /**
     * @param string $id
     * @return bool
     */
    public function contains($id)
    {
        return xcache_isset($this->prefixize($id));
    }

    /**
     * @param string $id
     * @return bool|mixed
     */
    public function get($id)
    {
        return xcache_get($this->prefixize($id));
    }

    /**
     * @param string $id
     * @param mixed $value
     * @param int $expireIn
     * @return bool
     */
    public function set($id, $value, $expireIn = 0)
    {
        return xcache_set($this->prefixize($id), $value, $expireIn);
    }

    /**
     * @param string $id
     * @return bool
     */
    public function delete($id)
    {
        return xcache_unset($this->prefixize($id));
    }

    /**
     * @return bool
     */
    public function clear()
    {
        xcache_clear_cache(XC_TYPE_VAR);
        return true;
    }

}
