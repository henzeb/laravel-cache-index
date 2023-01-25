<?php

namespace Henzeb\CacheIndex\Tests\Unit\CacheIndex\Repositories\IndexRepository;

use Mockery;
use Illuminate\Cache\ArrayStore;
use Orchestra\Testbench\TestCase;
use Henzeb\CacheIndex\Repositories\IndexRepository;

class RememberForeverTest extends TestCase
{
    use Helpers;

    public function testRememberForever(): void
    {
        $function = function () {
            return 'myValue';
        };


        $repo = new IndexRepository(
            new ArrayStore(),
            'myIndex'
        );

        $this->assertEquals(
            'myValue',
            $repo->rememberForever('myKey', $function)
        );

        $this->assertEquals(['myKey'], $repo->keys());
    }

    public function testSearCallsRememberForever(): void
    {
        $callback = fn() => 'test3';

        $mock = Mockery::mock(IndexRepository::class)->makePartial();
        $mock->expects('rememberForever')
            ->with('string', $callback)
            ->andReturns('test3');

        $this->assertEquals(
            'test3',
            $mock->sear('string', $callback)
        );
    }
}
