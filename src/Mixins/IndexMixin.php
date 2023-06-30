<?php

namespace Henzeb\CacheIndex\Mixins;

use Closure;
use Henzeb\CacheIndex\Repositories\IndexRepository;
use Illuminate\Cache\CacheManager;
use Illuminate\Support\Arr;
use Stringable;

use function resolve;

class IndexMixin
{
    public function index(): Closure
    {
        /**
         * @var $name string|array<Stringable|string|object>
         */
        return function (string|array $name): IndexRepository {
            $name = array_map(
                function (object|string $segment) {
                    if ($segment instanceof Stringable) {
                        return (string)$segment;
                    }

                    if (is_string($segment)) {
                        return $segment;
                    }

                    if (property_exists($segment, 'name')) {
                        return $segment->name;
                    }

                    return $segment::class;
                },
                Arr::wrap($name)
            );


            /**
             * @var CacheManager $this
             */
            return resolve(
                IndexRepository::class,
                [
                    'store' => $this->getStore(),
                    'index' => implode('.', $name)
                ]
            );
        };
    }
}
