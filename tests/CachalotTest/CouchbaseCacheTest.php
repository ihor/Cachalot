<?php

namespace CachalotTest;

class CouchbaseCacheTest extends AbstractCacheBackendTest
{
    public static function setUpBeforeClass()
    {
        if (!extension_loaded('couchbase') || !class_exists('Couchbase')) {
            static::$cache = null;
            return;
        }

        $couchbase = new \Couchbase('127.0.0.1', '', '', 'cachalot-test');
        $couchbase->flush();

        static::$cache = new \Cachalot\CouchbaseCache($couchbase);
    }

}
