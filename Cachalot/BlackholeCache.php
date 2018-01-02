<?php

namespace Cachalot;

class BlackholeCache extends AbstractCache
{
    /**
     * Returns cached $callback result
     *
     * @param callable $callback
     * @param array $args Callback arguments
     * @param int $expireIn Seconds
     * @param string|null $suffix Is needed to avoid cache collisions when callback is an anonymous function
     * @param bool $useSuffixAsKey When is true then instead automatic cache key generation the value provided in $suffix will be used as cache key
     * @return mixed
     * @throws \InvalidArgumentException
     */
    public function getCached($callback, array $args = array(), $expireIn = 0, $suffix = null, $useSuffixAsKey = false)
    {
        if (!is_callable($callback)) {
            throw new \InvalidArgumentException('First argument of getCached method has to be a valid callback');
        }

        return call_user_func_array($callback, $args);
    }

    /**
     * Returns true if cache contains entry with given key
     *
     * @param string $key
     * @return bool
     */
    public function contains($key)
    {
        return false;
    }

    /**
     * Returns cached value by key
     *
     * @param string $key
     * @return bool|mixed
     */
    public function get($key)
    {
        return false;
    }

    /**
     * Returns cached value by key or false if there is no cache entry for the given key
     *
     * @param string $key
     * @param mixed $value
     * @param int $expireIn Seconds
     * @return bool
     */
    public function set($key, $value, $expireIn = 0)
    {
        return true;
    }

    /**
     * Deletes cache entry by key
     *
     * @param string $key
     * @return bool
     */
    public function delete($key)
    {
        return true;
    }

    /**
     * Deletes all cache entries
     *
     * @return bool
     */
    public function clear()
    {
        return true;
    }

}
