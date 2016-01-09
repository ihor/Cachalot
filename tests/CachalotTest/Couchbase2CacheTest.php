<?php

namespace CachalotTest;

class Couchbase2CacheTest extends AbstractCacheBackendTest
{
    public static function setUpBeforeClass()
    {
        if (!extension_loaded('couchbase') || !class_exists('CouchbaseCluster')) {
            static::$cache = null;
            return;
        }

        $cluster = new \CouchbaseCluster('couchbase://localhost');
        $bucket = $cluster->openBucket('cachalot-test');

        static::$cache = new \Cachalot\Couchbase2Cache($bucket);
    }

}
