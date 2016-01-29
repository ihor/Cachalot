Cachalot
========

Cachalot (cache a lot) is an easy to use caching library. It supposed to do the only one thing - return cached callback result.

Installation
------------
Define the following requirement in your composer.json file:
```
"require": {
    "ihor/cachalot": "2.3"
}
```

Usage
-----
```php
// with Cachalot cache you can easier cache results of different types of functions
$cache = new \Cachalot\ArrayCache();

// built-in functions
$length = $cache->getCached('strlen', ['hello world']); 

// user defined functions
$unique = $cache->getCached('my_unique', [[1, 2, 3, 1, 2, 3]]);

// static methods
$result = $cache->getCached(['MyCalculator', 'subtract'], [1, 2]);

// instance methods
$product = $cache->getCached([new MyCalculator(), 'multiply'], [1, 2]);

// anonymous functions
$square = $cache->getCached($sqr, [5], \Cachalot\Cache::ONE_DAY, 'greet');

// callable objects
$trimed = $cache->getCached(new Trimmer(), [' hello world ']);
```

Documentation
-------------
### Cache API

##### getCached($callback, array $args = array(), $expireIn = 0, $cacheKeySuffix = null)

Returns cached $callback result

```$callback``` is the function (callable) which results we want to be cached  
```$args``` are the arguments passed to the ```$callback```  
```$expireIn``` sets cache TTL in seconds  
```$cacheIdSuffix``` is needed to avoid collisions when callback is an anoymous function

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
$cache = new Cachalot\ApcCache();
```

##### Cachalot\XcacheCache

```php
$cache = new Cachalot\XcacheCache();
```

##### Cachalot\MemcacheCache

Uses [Memcache PHP extension](http://php.net/manual/en/book.memcache.php) to store results in [Memcached](http://memcached.org)

```php
$memcache = new \Memcache();
$memcache->connect('unix:///usr/local/var/run/memcached.sock', 0);

$cache = new \Cachalot\MemcacheCache($memcache);
```

##### Cachalot\MemcachedCache

Uses [Memcached PHP extension](http://php.net/manual/en/book.memcached.php) to store results in [Memcached](http://memcached.org)

```php
$memcached = new \Memcached();
$memcached->addServer('/usr/local/var/run/memcached.sock', 0);

$cache = new \Cachalot\MemcachedCache($memcached);
```

##### Cachalot\RedisCache

```php
$redis = new \Redis();
$redis->connect('127.0.0.1');
$redis->select(1);

$cache = new \Cachalot\RedisCache($redis);
```

##### Cachalot\CouchbaseCache

Uses [Couchbase PHP SDK 1.x](http://docs.couchbase.com/couchbase-sdk-php-1.2/index.html)

```php
$couchbase = new \Couchbase('127.0.0.1', '', '', 'default');

$cache = new \Cachalot\CouchbaseCache($couchbase);
```

##### Cachalot\Couchbase2Cache

Uses [Couchbase PHP SDK 2.x](http://developer.couchbase.com/documentation/server/4.0/sdks/php-2.0/php-intro.html)

```php
$cluster = new \CouchbaseCluster('couchbase://localhost');
$bucket = $cluster->openBucket('default');

$cache = new \Cachalot\Couchbase2Cache($bucket);
```

##### Cachalot\ArrayCache

```php
$cache = new \Cachalot\ArrayCache();
```

##### Cachalot\BlackholeCache

Never caches results

```php
$cache = new \Cachalot\BlackholeCache();
```
