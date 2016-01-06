<?php

namespace Cachalot;

class ApcCache extends AbstractCache
{
    /**
     * @param string $prefix
     * @throws \RuntimeException
     */
    public function __construct($prefix = '')
    {
        if (!extension_loaded('apc') && !extension_loaded('apcu')) {
            throw new \RuntimeException('Unable to use APC(u) cache as APC(u) extension is not enabled.');
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

        if (false === $result = apc_fetch($id)) {
            $result = call_user_func_array($callback, $params);
            apc_store($id, $result, $expireIn);
        }

        return $result;
    }

    /**
     * @param string $id
     * @return bool
     */
    public function contains($id)
    {
        return apc_exists($this->prefixize($id));
    }

    /**
     * @param string $id
     * @return bool|mixed
     */
    public function get($id)
    {
        return apc_fetch($this->prefixize($id));
    }

    /**
     * @param string $id
     * @param mixed $value
     * @param int $expireIn
     * @return array|bool
     */
    public function set($id, $value, $expireIn = 0)
    {
        return apc_store($this->prefixize($id), $value, $expireIn);
    }

    /**
     * @param string $id
     * @return bool|string[]
     */
    public function delete($id)
    {
        return apc_delete($this->prefixize($id));
    }

    /**
     * @return bool
     */
    public function clear()
    {
        return apc_clear_cache();
    }

}
