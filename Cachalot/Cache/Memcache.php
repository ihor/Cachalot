<?php

/**
 * @author Ihor Burlachenko
 */

namespace Cachalot\Cache;

class Memcache extends AbstractCache
{
    /**
     * @var \Memcache
     */
    private $_cache;

    /**
     * @var string
     */
    private $host;

    /**
     * @var int
     */
    private $port;

    /**
     * @param string $prefix
     * @param string $host
     * @param int $port
     * @throws \RuntimeException
     */
    public function __construct($prefix = '', $host, $port)
    {
        if (!extension_loaded('memcache')) {
            throw new \RuntimeException('Unable to use Memcache cache as memcache extension is not enabled.');
        }

        $this->host = $host;
        $this->port = $port;

        parent::__construct($prefix);
    }

    /**
     * @return \Memcache
     */
    private function getCache()
    {
        if (null === $this->_cache) {
            $this->_cache = new \Memcache();
            $this->_cache->connect($this->host, $this->port);
        }

        return $this->_cache;
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

        if (false === $result = $this->getCache()->get($id)) {
            $result = $this->call($callback, $params);
            $this->getCache()->set($id, $result, false, $expireIn);
        }

        return $result;
    }

}
