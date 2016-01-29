<?php

namespace CachalotTest;


abstract class AbstractCacheBackendTest extends \PHPUnit_Framework_TestCase
{
    const ttl = 60;

    /**
     * @var \Cachalot\Cache
     */
    protected static $cache;

    private function getCachedTestCases()
    {
        return array(
            // Can't test boolean values because many back-end returns this value in case of error
            'string and int arguments' => array('answer', 42),
            'int and float arguments' => array(42, 3.14),
            'float and array arguments' => array(3.14, array('hello', 'world')),
            'array and StdClass arguments' => array(
                array('hello', 'world'),
                (object) array('hello' => 'world', 'answer' => 42)
            ),
            'StdClass and \ArrayIterator arguments' => array(
                (object) array('hello' => 'world', 'answer' => 42),
                new \ArrayIterator(array('hello', 'world'))
            ),
            '\ArrayIterator and string arguments' => array(
                new \ArrayIterator(array('hello', 'world')),
                'hello world'
            )
        );
    }

    public function testGetCachedFunction()
    {
        $this->assertCacheBackendSetUp();

        global $testFunctionCalls;
        $f = '\CachalotTest\testFunction';

        foreach ($this->getCachedTestCases() as $test => $data) {
            $this->assertEquals($data, static::$cache->getCached($f, $data, self::ttl));
            $this->assertEquals($this->countHits($f, $test), $testFunctionCalls);

            $this->assertEquals($data, static::$cache->getCached($f, $data, self::ttl));
            $this->assertEquals($this->countHits($f, $test), $testFunctionCalls);
        }
    }

    public function testGetCachedClosure()
    {
        $this->assertCacheBackendSetUp();

        $calls1 = 0;
        $f1 = function($arg1, $arg2) use (&$calls1) {
            ++$calls1;
            return array($arg1, $arg2);
        };

        $calls2 = 0;
        $f2 = function() use (&$calls2) {
            ++$calls2;
            return array_reverse(func_get_args());
        };

        foreach ($this->getCachedTestCases() as $test => $data) {
            $this->assertEquals(array_slice($data, 0, 2), static::$cache->getCached($f1, $data, self::ttl, ':closure'));
            $this->assertEquals($this->countHits('closure', $test), $calls1);

            $this->assertEquals(array_slice($data, 0, 2), static::$cache->getCached($f1, $data, self::ttl, ':closure'));
            $this->assertEquals($this->countHits('closure', $test), $calls1);

            $this->assertEquals(array_reverse($data), static::$cache->getCached($f2, $data, self::ttl, ':reversed'));
            $this->assertEquals($this->countHits('reversed', $test), $calls2);

            $this->assertEquals(array_reverse($data), static::$cache->getCached($f2, $data, self::ttl, ':reversed'));
            $this->assertEquals($this->countHits('reversed', $test), $calls2);
        }
    }

    public function testGetCachedCallableObject()
    {
        $this->assertCacheBackendSetUp();

        $f = new TestCallableObject();
        foreach ($this->getCachedTestCases() as $test => $data) {
            $this->assertEquals($data[0], static::$cache->getCached($f, $data, self::ttl));
            $this->assertEquals($this->countHits('object', $test), $f->getCalls());

            $this->assertEquals($data[0], static::$cache->getCached($f, $data, self::ttl));
            $this->assertEquals($this->countHits('object', $test), $f->getCalls());
        }
    }

    public function testGetCachedMethod()
    {
        $this->assertCacheBackendSetUp();

        $testObject = new TestClass();
        foreach ($this->getCachedTestCases() as $test => $data) {
            $this->assertEquals($data[1], static::$cache->getCached(array($testObject, 'method'), $data, self::ttl));
            $this->assertEquals($this->countHits('method', $test), $testObject->getMethodCalls());

            $this->assertEquals($data[1], static::$cache->getCached(array($testObject, 'method'), $data, self::ttl));
            $this->assertEquals($this->countHits('method', $test), $testObject->getMethodCalls());
        }
    }

    public function testGetCachedStaticMethod()
    {
        $this->assertCacheBackendSetUp();

        TestClass::resetStaticMethodCalls();
        $f = array('CachalotTest\TestClass', 'staticMethod');

        foreach ($this->getCachedTestCases() as $test => $data) {
            $this->assertEquals($data[1], static::$cache->getCached($f, $data, self::ttl));
            $this->assertEquals($this->countHits('static-method', $test), TestClass::getStaticMethodCalls());

            $this->assertEquals($data[1], static::$cache->getCached($f, $data, self::ttl));
            $this->assertEquals($this->countHits('static-method', $test), TestClass::getStaticMethodCalls());
        }
    }

    /**
     * @expectedException \InvalidArgumentException
     */
    public function testCachedWithInvalidCallback()
    {
        $this->assertCacheBackendSetUp();

        static::$cache->getCached('hello world');
    }

    public function testInvoke()
    {
        $this->assertCacheBackendSetUp();

        $f = function($result) { return $result; };

        $cache = self::$cache;
        $this->assertEquals('hello world', $cache($f, ['hello world']));
    }

    public function testSetGetDelete()
    {
        $this->assertCacheBackendSetUp();

        foreach (array(
            'int' => 42,
            'string' => 'hello world',
            'array' => array('hello', 'world'),
            'StdClass' => (object) array('hello' => 'world', 'answer' => 42),
            'object' => new \ArrayIterator(array('hello', 'world')),
        ) as $key => $value) {
            $this->assertFalse(static::$cache->contains($key));

            $this->assertTrue(static::$cache->set($key, $value, self::ttl));
            $this->assertTrue(static::$cache->contains($key));
            $this->assertEquals($value, static::$cache->get($key));

            $this->assertTrue(static::$cache->set($key, 'overwriting', self::ttl));
            $this->assertTrue(static::$cache->contains($key));
            $this->assertEquals('overwriting', static::$cache->get($key));

            $this->assertTrue(static::$cache->delete($key));
            $this->assertFalse(static::$cache->contains($key));
        }
    }

    public function testExpiration()
    {
        $this->assertCacheBackendSetUp();

        $calls = 0;
        $f = function($value) use (&$calls) {
            ++$calls;
            return $value;
        };

        static::$cache->getCached($f, array('hello world'), 2, 'test-expiration');
        $this->assertEquals(1, $calls);

        sleep(1);

        static::$cache->getCached($f, array('hello world'), 2, 'test-expiration');
        $this->assertEquals(1, $calls);

        sleep(2);

        static::$cache->get('test');

        static::$cache->getCached($f, array('hello world'), 2, 'test-expiration');
        $this->assertEquals(2, $calls);

        static::$cache->set('expiration-test', 'hello world', 2);
        $this->assertTrue(static::$cache->contains('expiration-test'));

        sleep(3);

        $this->assertFalse(static::$cache->contains('expiration-test'));
    }

    public function testClear()
    {
        $this->assertCacheBackendSetUp();

        $this->assertTrue(static::$cache->set('key-1', 'hello'));
        $this->assertTrue(static::$cache->set('key-2', 'world'));

        $this->assertTrue(static::$cache->clear());

        $this->assertFalse(static::$cache->contains('key-1'));
        $this->assertFalse(static::$cache->contains('key-2'));
    }

    private function assertCacheBackendSetUp()
    {
        if (!static::$cache) {
            $this->markTestSkipped(sprintf(
                'Initialize cache backend in the "setUpBeforeClass" method in %s',
                get_called_class()
            ));
        }
    }

    private $hits = array();

    protected function countHits($function, $test)
    {
        if (!isset($this->hits[$function])) {
            $this->hits[$function] = array();
        }

        if (!in_array($test, $this->hits[$function])) {
            $this->hits[$function][] = $test;
        }

        return count($this->hits[$function]);
    }

}

$testFunctionCalls = 0;
function testFunction()
{
    global $testFunctionCalls;
    ++$testFunctionCalls;

    return func_get_args();
}

class TestCallableObject
{
    /**
     * @var int
     */
    private $calls = 0;

    /**
     * @return int
     */
    public function getCalls()
    {
        return $this->calls;
    }

    /**
     * @return mixed
     */
    public function __invoke($arg)
    {
        ++$this->calls;
        return $arg;
    }

}

class TestClass
{
    /**
     * @var int
     */
    private static $staticMethodCalls = 0;

    /**
     * @var int
     */
    private $methodCalls = 0;

    public static function resetStaticMethodCalls()
    {
        static::$staticMethodCalls = 0;
    }

    /**
     * @return int
     */
    public static function getStaticMethodCalls()
    {
        return static::$staticMethodCalls;
    }

    /**
     * @return mixed
     */
    public static function staticMethod()
    {
        ++static::$staticMethodCalls;
        return end(func_get_args());
    }

    /**
     * @return int
     */
    public function getMethodCalls()
    {
        return $this->methodCalls;
    }

    /**
     * @param mixed $arg1
     * @param mixed $arg2
     * @return mixed
     */
    public function method($arg1, $arg2)
    {
        ++$this->methodCalls;
        return $arg2;
    }

}
