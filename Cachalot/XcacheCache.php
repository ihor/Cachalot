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
    public function getCached($callback, array $params = array(), $expireIn = 0, $cacheIdSuffix = null)
    {
        if (!is_callable($callback)) {
            throw new \InvalidArgumentException('First argument of getCached method has to be a valid callback');
        }

        $id = $this->getCallbackCacheId($callback, $params, $cacheIdSuffix);

        if (xcache_isset($id)) {
            return $this->unserializeCompound(xcache_get($id));
        }

        $result = call_user_func_array($callback, $params);
        xcache_set($id, $this->serializeCompound($result), $expireIn);

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
        if ($value = xcache_get($this->prefixize($id))) {
            return $this->unserializeCompound($value);
        }

        return false;
    }

    /**
     * @param string $id
     * @param mixed $value
     * @param int $expireIn
     * @return bool
     */
    public function set($id, $value, $expireIn = 0)
    {
        return xcache_set($this->prefixize($id), $this->serializeCompound($value), $expireIn);
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
