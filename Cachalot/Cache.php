<?php

namespace Cachalot;

interface Cache
{
    const FIFTEEN_MINUTES = 900;
    const HALF_HOUR = 1800;
    const ONE_HOUR = 3600;
    const ONE_DAY = 86400;

    /**
     * @throws \InvalidArgumentException
     * @param \callable $callback
     * @param array $params
     * @param int $expireIn Seconds
     * @param mixed $cacheIdSuffix
     * @return mixed
     */
    public function getCached($callback, $params = array(), $expireIn = 0, $cacheIdSuffix = null);

}
