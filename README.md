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

$greeting = $cache->getCached($greet, array('World!'));
$unique = $cache->getCached('unique', array(array(1, 2, 3, 1, 2, 3)));
$count = $cache->getCached(new CountCommand(), array(array(1, 2, 3)));
$sum = $cache->getCached('array_sum', array(array(1, 2, 3)));
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
##### getCached($callback, $params = array(), $expireIn = 0, $cacheIdSuffix = null)

Returns cached callback result for given parameters. See the usage above.

```$callback``` is the function which results we want to be cached  
```$params``` are the function parameters  
```$expireIn``` sets cache TTL in seconds  
```$cacheIdSuffix``` allows to avoid collisions when using anonymous functions with adding a suffix to the cache key    

To have possibility to use Cachalot as a regular caching library when needed it contains classic cache methods:

##### contains($id)

Checks if cache contains entry with given id

##### get($id)

Returns cache entry by id. Returns false if entry was not found

##### set($id, $value, $expireIn = 0)

Sets cache entry with TTL by id. When ```$expireIn = 0``` the value is cached forever.

##### delete($id)

Deletes cache entry by id

##### clear()

Clears all cache entries
