<?php

namespace Henzeb\CacheIndex\Providers;

use Illuminate\Support\Facades\Cache;
use Illuminate\Support\ServiceProvider;
use Henzeb\CacheIndex\Mixins\IndexMixin;

class CacheIndexServiceProvider extends ServiceProvider
{
    public function register()
    {
    }

    public function boot(): void
    {
        Cache::mixin(new IndexMixin());
    }
}
