<?php

namespace Henzeb\CacheIndex\Tests\Unit\CacheIndex\Repositories\IndexRepository;

use Carbon\Carbon;
use Illuminate\Cache\ArrayStore;
use Orchestra\Testbench\TestCase;
use Henzeb\CacheIndex\Repositories\IndexRepository;

class PutTest extends TestCase
{
    use Helpers;

    public function testPut(): void
    {
        $store = new ArrayStore();
        $repo = new IndexRepository(
            $store,
            'myIndex',
        );

        $repo->put('myKey', 'myValue');

        $this->assertEquals(
            [
                'myKey'
            ],
            $repo->keys()
        );

        $this->assertEquals(
            'myValue',
            $store->get($repo->getPrefix() . 'myKey')
        );
    }

    public function testPutExpirationOverwrite(): void
    {
        Carbon::setTestNow(now());


        $store = new ArrayStore();
        $repo = new IndexRepository(
            $store,
            'myIndex',
        );

        $repo->put('myKey', 'myValue', 10);

        $repo->put('myKey', 'myValue', 20);

        Carbon::setTestNow(now()->addSeconds(11));

        $this->assertEquals('myValue', $repo->get('myKey'));
    }

    public function testSetCallsPut(): void
    {
        $repo = $this->mock(IndexRepository::class)
            ->makePartial();

        $repo->expects('put')
            ->with('myKey', 'myValue', null)
            ->andReturn(true);

        $repo->expects('put')
            ->with('myKey', 'myValue', 10)
            ->andReturn(true);

        $this->assertEquals(
            true,
            $repo->set('myKey', 'myValue')
        );

        $this->assertEquals(
            true,
            $repo->set('myKey', 'myValue', 10)
        );
    }
}
