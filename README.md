Cachalot
========

Cachalot (cache a lot) is an easy to use caching library. It supposed to do only one thing - return cached callback result.

Usage
-----
```php
$cache = new \Cachalot\ArrayCache();

$greet = function($name) {
    return 'Hello ' . $name;
};

function unique(array $input) {
    return array_unique($input);
}

class CountCommand {
    public function __invoke(array $input) {
        return count($input);
    }
}

class Calculator {
    public static function subtract($x, $y) { 
        return $x - $y;
    }
    
    public function multiply($x, $y) {
        return $x * $y;
    }
}

// Built-in function
$sum = $cache->getCached('array_sum', [[1, 2, 3]]);

// User defined function
$unique = $cache->getCached('unique', [[1, 2, 3, 1, 2, 3]]);

// Anonymous function
$greeting = $cache->getCached($greet, ['World!'], 0, 'greet');

// Callable object
$count = $cache->getCached(new CountCommand(), [[, 2, 3]]);

// Static method
$sub = $cache->getCached(['Calculator', 'subtract'], [[1, 2]]);
 
// Object method
$calculator = new Calculator();
$product = $cache->getCached([$calculator, 'multiply'], [[1, 2]]);
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

```php
$couchbase = new \Couchbase('127.0.0.1', '', '', 'default');

$cache = new \Cachalot\CouchbaseCache($redis, 'dev:'); // creates new cache instance with key prefix "dev:"
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
