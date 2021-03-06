<?php

namespace Cachalot;

interface Cache
{
    const ONE_MINUTE = 60;
    const FIVE_MINUTES = 300;
    const TEN_MINUTES = 600;
    const FIFTEEN_MINUTES = 900;
    const HALF_HOUR = 1800;
    const ONE_HOUR = 3600;
    const THREE_HOURS = 10800;
    const SIX_HOURS = 21600;
    const HALF_DAY = 43200;
    const ONE_DAY = 86400;
    const ONE_WEEK = 604800;
    const ONE_MONTH = 2592000;

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
    public function getCached($callback, array $args = array(), $expireIn = 0, $suffix = null, $useSuffixAsKey = false);

    /**
     * @param \callable $callback
     * @param array $args
     * @param string $suffix
     * @param bool $useSuffixAsKey When is true then instead automatic cache key generation the value provided in $suffix will be used as cache key
     * @return string
     */
    public function getCallbackCacheKey($callback, array $args = array(), $suffix = null, $useSuffixAsKey = false);

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
    public function __invoke($callback, array $args = array(), $expireIn = 0, $suffix = null, $useSuffixAsKey = false);

    /**
     * Returns true if cache contains entry with given key
     *
     * @param string $key
     * @return bool
     */
    public function contains($key);

    /**
     * Returns cached value by key or false if there is no cache entry for the given key
     *
     * @param string $key
     * @return bool|mixed
     */
    public function get($key);

    /**
     * Caches value by key
     *
     * @param string $key
     * @param mixed $value
     * @param int $expireIn Seconds
     * @return bool
     */
    public function set($key, $value, $expireIn = 0);

    /**
     * Deletes cache entry by key
     *
     * @param string $key
     * @return bool
     */
    public function delete($key);

    /**
     * Deletes all cache entries
     *
     * @return bool
     */
    public function clear();

}
