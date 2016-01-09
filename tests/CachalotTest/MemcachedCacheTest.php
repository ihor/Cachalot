<?php

namespace CachalotTest;

class MemcachedCacheTest extends AbstractCacheBackendTest
{
    public static function setUpBeforeClass()
    {
        if (!extension_loaded('memcached')) {
            static::$cache = null;
            return;
        }

        $memcached = new \Memcached();
        $memcached->addServer('/usr/local/var/run/memcached.sock', 0);
        $memcached->flush();

        static::$cache = new \Cachalot\MemcachedCache($memcached, 'cachalot-test:');
    }

}
