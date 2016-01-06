<?php

namespace Cachalot;

abstract class AbstractCache implements \Cachalot\Cache
{
    /**
     * @var int
     */
    protected static $maxKeyLength = 250;

    /**
     * @var string
     */
    private $prefix;

    /**
     * @param string $prefix
     */
    public function __construct($prefix = '')
    {
        $this->prefix = $prefix;
    }

    /**
     * @param \callable $callback
     * @param array $params
     * @param mixed $cacheIdSuffix
     * @return string
     */
    protected function getCallbackCacheId($callback, array $params = array(), $cacheIdSuffix = null)
    {
        if (is_array($callback)) {
            $callbackStr = (is_string($callback[0]) ? $callback[0] : get_class($callback[0])) . '::' . $callback[1];
        }
        else if (is_object($callback)) {
            $callbackStr = get_class($callback);
        }
        else {
            $callbackStr = $callback;
        }

        $paramsStr = '(' . implode(',', array_map(array($this, 'stringilizeCallbackParam'), $params)) . ')';

        $id = $this->prefix . $callbackStr . $paramsStr . ($cacheIdSuffix ? $cacheIdSuffix : '');
        return static::$maxKeyLength && strlen($id) > static::$maxKeyLength ? md5($id) : $id;
    }

    /**
     * @param mixed $param
     * @return string
     */
    protected function stringilizeCallbackParam($param)
    {
        if (is_array($param)) {
            return '[' . implode(',', array_map(array($this, 'stringilizeCallbackParam'), $param)) . ']';
        }

        if (is_object($param)) {
            return serialize($param);
        }

        return strval($param);
    }

    /**
     * @param string $id
     * @return string
     */
    protected function prefixize($id)
    {
        return $this->prefix . $id;
    }

}
