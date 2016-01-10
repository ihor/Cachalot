Cachalot
========

Cachalot (cache a lot) is an easy to use caching library. It supposed to do only one thing - return cached callback result.

Usage
-----
Create Cachalot cache instance:
```php
$cache = new \Cachalot\ArrayCache();
```

Cache PHP built-in function results:
```php
$sum = $cache->getCached('array_sum', [[1, 2, 3]]); 
```

Cache user defined function results:
```php
function unique(array $input) 
{ 
    return array_unique($input); 
}

$unique = $cache->getCached('unique', [[1, 2, 3, 1, 2, 3]]);
```

Cache static and instance methods results:
```php
class Calculator {
    public static function subtract($x, $y) 
    {  
        return $x - $y; 
    }
    
    public function multiply($x, $y) 
    { 
        return $x * $y; 
    }
}

$sub = $cache->getCached(['Calculator', 'subtract'], [[1, 2]]);

$calculator = new Calculator();
$product = $cache->getCached([$calculator, 'multiply'], [[1, 2]]);
```

Cache anonymous function results:
```php
$greet = function($name) { 
    return 'Hello ' . $name; 
};
$greeting = $cache->getCached($greet, ['World!'], \Cachalot\Cache::ONE_DAY, 'greet');
```

Cache callable object results:
```php
class CountCommand 
{
    public function __invoke(array $input) 
    { 
        return count($input); 
    }
}
$count = $cache->getCached(new CountCommand(), [[, 2, 3]]);
```

Installation
------------
Define the following requirement in your composer.json file:
```
"require": {
    "ihor/cachalot": "2.2"
}
```

Documentation
-------------
### Cache API

##### getCached($callback, array $args = array(), $expireIn = 0, $cacheKeySuffix = null)

Returns cached $callback result

```$callback``` is the function (callable) which results we want to be cached  
```$args``` are the arguments passed to the ```$callback```  
```$expireIn``` is cache TTL in seconds  
```$cacheIdSuffix``` is used to avoid collisions when caches results of anonymous functions  

```php
$sum = $cache->getCached('array_sum', [[1, 2, 3]]);
```

To have possibility to use Cachalot as a regular caching library when needed it contains classic cache methods

##### contains($key)

Returns true if cache contains entry with given key

```php
if ($cache->contains('lastVisit')) {
    echo 'This is not the first visit';
}
```

##### get($key)

Returns cached value by key or false if there is no cache entry for the given key

```php
if ($lastVisitDate = $cache->get('lastVisit')) {
    echo sprintf('Last visit was %s', date('Y-m-d H:i:s', $lastVisitDate));
}
```

##### set($key, $value, $expireIn = 0)

Caches value by key. When ```$expireIn = 0``` the value is cached forever

```php
$cache->set('lastVisit', time());
```

##### delete($key)

Deletes cache entry by key

```php
$cache->delete('lastVisit');
```

##### clear()

Deletes all cache entries

```php
$cache->clear(); // flushed
```

### Back-ends

##### Cachalot\ApcCache

```php
$cache = new Cachalot\ApcCache('dev:'); // creates new cache instance with key prefix "dev:"
```

##### Cachalot\XcacheCache

```php
$cache = new Cachalot\XcacheCache('dev:'); // creates new cache instance with key prefix "dev:"
```

##### Cachalot\MemcacheCache

Uses [Memcache PHP extension](http://php.net/manual/en/book.memcache.php) to store results in [Memcached](http://memcached.org)

```php
$memcache = new \Memcache();
$memcache->connect('unix:///usr/local/var/run/memcached.sock', 0);

$cache = new \Cachalot\MemcacheCache($memcache, 'dev:'); // creates new cache instance with key prefix "dev:"
```

##### Cachalot\MemcachedCache

Uses [Memcached PHP extension](http://php.net/manual/en/book.memcached.php) to store results in [Memcached](http://memcached.org)

```php
$memcached = new \Memcached();
$memcached->addServer('/usr/local/var/run/memcached.sock', 0);

$cache = new \Cachalot\MemcachedCache($memcached, 'dev:'); // creates new cache instance with key prefix "dev:"
```

##### Cachalot\RedisCache

```php
$redis = new \Redis();
$redis->connect('127.0.0.1');
$redis->select(1);

$cache = new \Cachalot\RedisCache($redis, 'dev:'); // creates new cache instance with key prefix "dev:"
```

##### Cachalot\CouchbaseCache

Uses [Couchbase PHP SDK 1](http://docs.couchbase.com/couchbase-sdk-php-1.2/index.html)

```php
$couchbase = new \Couchbase('127.0.0.1', '', '', 'default');

$cache = new \Cachalot\CouchbaseCache($couchbase, 'dev:'); // creates new cache instance with key prefix "dev:"
```

##### Cachalot\Couchbase2Cache

Uses [Couchbase PHP SDK 2](http://developer.couchbase.com/documentation/server/4.0/sdks/php-2.0/php-intro.html)

```php
$cluster = new \CouchbaseCluster('couchbase://localhost');
$bucket = $cluster->openBucket('default');

$cache = new \Cachalot\Couchbase2Cache($bucket, 'dev:'); // creates new cache instance with key prefix "dev:"
```

##### Cachalot\ArrayCache

```php
$cache = new \Cachalot\ArrayCache('dev:'); // creates new cache instance with key prefix "dev:"
```

##### Cachalot\BlackholeCache

Never caches results

```php
$cache = new \Cachalot\BlackholeCache();
```
