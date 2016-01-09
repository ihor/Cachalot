<?php

namespace CachalotTest;

class ArrayCacheTest extends AbstractCacheBackendTest
{
    public static function setUpBeforeClass()
    {
        static::$cache = new \Cachalot\ArrayCache('cachalot-test:');
    }

}
