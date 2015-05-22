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
     * @throws \InvalidArgumentException
     * @param \callable $callback
     * @param array $params
     * @param int $expireIn Seconds
     * @param mixed $cacheIdSuffix
     * @return mixed
     */
    public function getCached($callback, $params = array(), $expireIn = 0, $cacheIdSuffix = null);

    /**
     * @param string $id
     * @return bool
     */
    public function contains($id);

    /**
     * @param string $id
     * @return bool|mixed
     */
    public function get($id);

    /**
     * @param string $id
     * @param mixed $value
     * @param int $expireIn
     * @return bool
     */
    public function set($id, $value, $expireIn = 0);

    /**
     * @param string $id
     * @return bool
     */
    public function delete($id);

    /**
     * @return bool
     */
    public function clear();

}
