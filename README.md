# Cache Index for Laravel

[![Build Status](https://github.com/henzeb/laravel-cache-index/workflows/tests/badge.svg)](https://github.com/henzeb/laravel-cache-index/actions)
[![Latest Version on Packagist](https://img.shields.io/packagist/v/henzeb/laravel-cache-index.svg?style=flat-square)](https://packagist.org/packages/henzeb/laravel-cache-index)
[![Total Downloads](https://img.shields.io/packagist/dt/henzeb/laravel-cache-index.svg?style=flat-square)](https://packagist.org/packages/henzeb/laravel-cache-index)
[![Test Coverage](https://api.codeclimate.com/v1/badges/64de174ad1e0c2680361/test_coverage)](https://codeclimate.com/github/henzeb/laravel-cache-index/test_coverage)
[![License](https://img.shields.io/packagist/l/henzeb/laravel-cache-index)](https://packagist.org/packages/henzeb/laravel-cache-index)

When you have a situation where you need to be able to track keys, you can
use [tags](https://laravel.com/docs/master/cache#cache-tags). Unfortunately
tags aren't supported by all drivers, and retrieving keys is only doable the
hacky way with redis.

Cache Index for Laravel provides a driver-independent way for managing such
indexes.

## Installation

Just install with the following command.

```bash
composer require henzeb/laravel-cache-index
```

## Usage

Under the hood, `index` returned an extended `Illuminate\Cache\Repository`
object, which manages your index.

See [Laravel doc's](https://laravel.com/docs/master/cache)
for the possible methods.

keys are prefixed with the index name, so that you can't accidentally
overwrite any values belonging to that index.

Note: tags are currently not supported when using `index`.

````php
Cache::index('myIndex')->add('test', 'my value');
Cache::index('myIndex')->put('put', 'my value');

Cache::driver('file')->index('myIndex')->remember('filed', 'my value in file');

Cache::index('myIndex')->get('put'); // returns 'my value'
Cache::get('put'); // returns null

Cache::index('myIndex')->flush(); // only flushes keys in index

````

### Keys

Retrieving a list of keys is very easy.

````php
Cache::index('myIndex')->keys(); // returns ['test', 'put']
Cache::driver('file')->index()->keys('myIndex'); // returns ['filed']
````

### Count

With this method, you can know how many keys are inside your index.

````php
Cache::index('myIndex'); // using default driver
Cache::driver('file')->index('myIndex'); // using file driver
````

### Copy & Move

#### Copy

Makes a copy of a cached item, and adds the key in the new index.

It returns true if it's successful, false otherwise.

````php
Cache::index('myIndex')->copy('myKey', 'targetIndex');
Cache::index('myIndex')->copy('myKey', 'targetIndex', 10);
````

#### Move

Does the same as `Copy`, except that it removes its own copy.

It returns true if it's successful, false otherwise.

````php
Cache::index('myIndex')->move('myKey', 'targetIndex');
Cache::index('myIndex')->move('myKey', 'targetIndex', 10);
````

Note: Be aware that either copy and move do nothing with the TTL. It creates
a new copy of the cached item with a new TTL if given.

### pop

Just like with arrays, takes and removes the last indexed key and returns the value
associated with that index.

### shift

Just like with arrays, takes and removes the first indexed key and returns the value
associated with that index.

### random

returns a random value.

### randomKey

returns a random key.

### pullRandom

Returns a random value and pulls it from the cache.

### syncTtl

Allows you to synchronize the ttl between indexed keys.

````php
Cache::index('myIndex')->syncTtl(10);
Cache::index('myIndex')->syncTtl(now()->addSeconds(10));
````

## Testing this package

```bash
composer test
```

## Changelog

Please see [CHANGELOG](CHANGELOG.md) for more information what has changed
recently.

## Contributing

Please see [CONTRIBUTING](CONTRIBUTING.md) for details.

## Security

If you discover any security related issues, please email
henzeberkheij@gmail.com instead of using the issue tracker.

## Credits

- [Henze Berkheij](https://github.com/henzeb)

## License

The GNU AGPLv. Please see [License File](LICENSE.md) for more information.
