<?php

namespace Henzeb\CacheIndex\Providers;

use Henzeb\CacheIndex\Mixins\IndexMixin;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\ServiceProvider;

class CacheIndexServiceProvider extends ServiceProvider
{
    public function register()
    {
    }

    public function boot(): void
    {
        $facadeRoot = (fn() => Cache::getFacadeAccessor())->bindTo(null, Cache::class)();
        $this->callAfterResolving(
            $facadeRoot,
            function () {
                Cache::mixin(new IndexMixin());
            }
        );

    }
}
