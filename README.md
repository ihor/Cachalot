Cachalot
========

Cachalot (cache a lot) is an easy to use caching library. It supposed to do only one thing - return cached callback result.

```php
$cache = new \Cachalot\Cache\ArrayCache();

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