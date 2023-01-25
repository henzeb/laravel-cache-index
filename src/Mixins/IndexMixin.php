<?php

namespace Henzeb\CacheIndex\Mixins;

use Closure;
use Illuminate\Cache\CacheManager;
use Henzeb\CacheIndex\Repositories\IndexRepository;

use function resolve;

class IndexMixin
{
    public function index(): Closure
    {
        return function (string $name): IndexRepository {
            /**
             * @var CacheManager $this
             */
            return resolve(
                IndexRepository::class,
                [
                    'store' => $this->getStore(),
                    'index' => $name
                ]
            );
        };
    }
}
