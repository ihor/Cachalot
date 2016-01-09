<?php

namespace CachalotTest;

class ApcCacheTest extends AbstractCacheBackendTest
{
    public static function setUpBeforeClass()
    {
        if (!extension_loaded('apc') && !extension_loaded('apcu')) {
            static::$cache = null;
            return;
        }

        apc_clear_cache();
        static::$cache = new \Cachalot\ApcCache('cachalot-test:');
    }

    public function testExpiration() {}

}
