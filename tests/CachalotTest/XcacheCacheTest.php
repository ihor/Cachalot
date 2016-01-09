<?php

namespace CachalotTest;

class XcacheCacheTest extends AbstractCacheBackendTest
{
    public static function setUpBeforeClass()
    {
        if (!extension_loaded('xcache')) {
            static::$cache = null;
            return;
        }

        xcache_clear_cache(XC_TYPE_VAR);
        static::$cache = new \Cachalot\XcacheCache('cachalot-test:');
    }

    public function testExpiration() {}

}
