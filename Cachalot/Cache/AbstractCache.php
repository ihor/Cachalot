<?php

namespace Cachalot\Cache;

abstract class AbstractCache implements \Cachalot\Cache
{
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
     * @return mixed
     * @throws \InvalidArgumentException
     */
    protected function call($callback, $params = array())
    {
        if (!is_callable($callback)) {
            throw new \InvalidArgumentException('First argument of getCached method has to be a valid callback');
        }

        $result = call_user_func_array($callback, $params);
        if ($result instanceof \Iterator) {
            $result = iterator_to_array($result);
        }

        return $result;
    }

    /**
     * @param \callable $callback
     * @param array $params
     * @param mixed $cacheIdSuffix
     * @return string
     */
    protected function getCallbackCacheId($callback, $params = array(), $cacheIdSuffix = null)
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

        return $this->prefix . $callbackStr . $paramsStr . ($cacheIdSuffix ? $cacheIdSuffix : '');
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

        return strval($param);
    }

}
