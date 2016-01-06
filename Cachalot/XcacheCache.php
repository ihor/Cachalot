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
            return $this->unserialize(xcache_get($id));
        }

        $result = call_user_func_array($callback, $params);
        xcache_set($id, $this->serialize($result), $expireIn);

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
            return $this->unserialize($value);
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
        return xcache_set($this->prefixize($id), $this->serialize($value), $expireIn);
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

    /**
     * @param mixed $value
     * @return string
     */
    private function serialize($value)
    {
        return is_object($value) ? serialize($value) : $value;
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

        if (strlen($value) < 2 || $value[1] !== ':'  || ($value[0] !== 'O' && $value[0] !== 'C')) {
            return $value;
        }

        if ($unserialized = @unserialize($value)) {
            return $unserialized;
        }

        return $value;
    }

}
