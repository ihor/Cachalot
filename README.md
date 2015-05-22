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
**getCached($callback, $params = array(), $expireIn = 0, $cacheIdSuffix = null)**

Returns cached callback result for given parameters. See usage above.

* $callback - callback to be cached
* $params - callback parameters
* $expireIn - cache TTL
* $cacheIdSuffix - cache suffix to avoid collisions when using anonymous functions


**contains($id)**

Checks if cache contains entry with given id

**get($id)**

Returns cache entry by id. Returns false if id was not found.

**set($id, $value, $expireIn = 0)**

Set cache entry with TTL by id. When $expireIn = 0 the value is cached forever.

**delete($id)**

Deletes cache entry by id.

**clear()**

Clears all cache entries.
