<?php

namespace Cachalot;

class BlackholeCache extends AbstractCache
{
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

        return call_user_func_array($callback, $params);
    }

    /**
     * @param string $id
     * @return bool
     */
    public function contains($id)
    {
        return false;
    }

    /**
     * @param string $id
     * @return bool|mixed
     */
    public function get($id)
    {
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
        return true;
    }

    /**
     * @param string $id
     * @return bool
     */
    public function delete($id)
    {
        return true;
    }

    /**
     * @return bool
     */
    public function clear()
    {
        return true;
    }

}
