<?php

namespace Cachalot\Cache;

class Apc extends AbstractCache
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
    public function getCached($callback, $params = array(), $expireIn = 0, $cacheIdSuffix = null)
    {
        $id = $this->getCallbackCacheId($callback, $params, $cacheIdSuffix);

        if (false === $result = apc_fetch($id)) {
            $result = $this->call($callback, $params);
            apc_store($id, $result, $expireIn);
        }

        return $result;
    }

}
