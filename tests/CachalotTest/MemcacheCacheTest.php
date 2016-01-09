<?php

namespace CachalotTest;

class MemcacheCacheTest extends AbstractCacheBackendTest
{
    public static function setUpBeforeClass()
    {
        if (!extension_loaded('memcache')) {
            static::$cache = null;
            return;
        }

        $memcache = new \Memcache();
        $memcache->connect('unix:///usr/local/var/run/memcached.sock', 0);
        $memcache->flush();

        static::$cache = new \Cachalot\MemcacheCache($memcache, 'cachalot-test:');
    }

}
