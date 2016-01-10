# CHANGELOG

## 2.3.0 / 2016-01-10

- Added [Memcached](https://github.com/ihor/Cachalot/blob/master/Cachalot/MemcachedCache.php) extension support
- Added [Couchbase PHP SDK 2.x](https://github.com/ihor/Cachalot/blob/master/Cachalot/CouchbaseCache2.php) support
- Added selective serialization for [Redis](https://github.com/ihor/Cachalot/blob/master/Cachalot/RedisCache.php) and [Xcache](https://github.com/ihor/Cachalot/blob/master/Cachalot/XcacheCache.php) cache back-ends
- Optimized [ArrayCache](https://github.com/ihor/Cachalot/blob/master/Cachalot/ArrayCache.php) performance
- Fixed cache keys for object arguments passed to ```getCached```
- Fixed collisions for keys longer than supported than cache back-end
- Fixed [CouchbaseCache](https://github.com/ihor/Cachalot/blob/master/Cachalot/CouchbaseCache.php) to follow the same cache API
