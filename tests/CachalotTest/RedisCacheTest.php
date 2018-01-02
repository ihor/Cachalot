<?php

namespace CachalotTest;

class RedisCacheTest extends AbstractCacheBackendTest
{
    public static function setUpBeforeClass()
    {
        if (!extension_loaded('redis')) {
            static::$cache = null;
            return;
        }

        try {
            $redis = new \Redis();
            $redis->connect('127.0.0.1');
            $redis->select(1);
            $redis->flushAll();
        }
        catch (\RedisException $e) {
            return;
        }

        static::$cache = new \Cachalot\RedisCache($redis, 'cachalot-test:');
    }

    // @todo Add serialize/unserialize tests

}
