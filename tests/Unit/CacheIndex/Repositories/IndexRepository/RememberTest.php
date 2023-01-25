<?php

namespace Henzeb\CacheIndex\Tests\Unit\CacheIndex\Repositories\IndexRepository;

use Carbon\Carbon;
use Illuminate\Cache\ArrayStore;
use Orchestra\Testbench\TestCase;
use Henzeb\CacheIndex\Repositories\IndexRepository;

class RememberTest extends TestCase
{
    use Helpers;

    public function testRemember(): void
    {
        $function = function () {
            return 'myValue';
        };
        $store = new ArrayStore();

        $repo = new IndexRepository(
            $store,
            'myIndex',
        );
        Carbon::setTestNow();
        $this->assertEquals(
            'myValue',
            $repo->remember('myKey', 10, $function)
        );

        $this->assertEquals(['myKey'], $repo->keys());
    }

    public function testRememberWithoutTtl(): void
    {
        $function = function () {
            return 'myValue';
        };
        $store = new ArrayStore();

        $repo = new IndexRepository(
            $store,
            'myIndex'
        );
        $this->assertEquals('myValue', $repo->remember('myKey', null, $function));

        $this->assertEquals(['myKey'], $repo->keys());
    }
}
