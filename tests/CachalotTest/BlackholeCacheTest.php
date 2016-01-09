<?php

namespace CachalotTest;

class BlackholeCacheTest extends AbstractCacheBackendTest
{
    public static function setUpBeforeClass()
    {
        static::$cache = new \Cachalot\BlackholeCache();
    }

    public function testSetGetDelete()
    {
        foreach (array(
            'int-value' => 42,
            'string-value' => 'hello world',
            'array-value' => array('hello', 'world'),
            'object-value' => (object) array('hello' => 'world', 'answer' => 42),
        ) as $key => $value) {
            $this->assertFalse(static::$cache->contains($key));
            $this->assertTrue(static::$cache->set($key, $value));
            $this->assertFalse(static::$cache->contains($key));
            $this->assertFalse(static::$cache->get($key));
            $this->assertTrue(static::$cache->delete($key));
            $this->assertFalse(static::$cache->contains($key));
        }
    }

    public function testExpiration() {}

    private $hits = array();

    protected function countHits($function, $test)
    {
        if (!isset($this->hits[$function])) {
            $this->hits[$function] = 0;
        }

        return ++$this->hits[$function];
    }

}
