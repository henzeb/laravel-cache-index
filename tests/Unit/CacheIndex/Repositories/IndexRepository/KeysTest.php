<?php

namespace Henzeb\CacheIndex\Tests\Unit\CacheIndex\Repositories\IndexRepository;

use Carbon\Carbon;
use Henzeb\CacheIndex\Repositories\IndexRepository;
use Illuminate\Cache\ArrayStore;
use Orchestra\Testbench\TestCase;

class KeysTest extends TestCase
{
    use Helpers;

    public function testKeys(): void
    {
        $arrayStore = new ArrayStore();
        $repo = new IndexRepository(
            $arrayStore,
            'myIndex',
            'array'
        );

        Carbon::setTestNow('2024-01-01 00:00:00');

        $repo->add('myKey', 'value');

        $repo->add('myExpiringKey', 'anotherValue', 10);

        $this->assertEquals(
            [
                'myKey',
                'myExpiringKey'
            ],
            $repo->keys()
        );

        Carbon::setTestNow(
            now()->addSeconds(9)
        );

        $this->assertEquals(
            [
                'myKey',
                'myExpiringKey'
            ],
            $repo->keys()
        );

        $this->assertEquals(
            'anotherValue',
            $arrayStore->get('indexed_myIndex_myExpiringKey')
        );

        Carbon::setTestNow(
            now()->addSeconds(10)
        );

        $this->assertEquals(
            [
                'myKey',
            ],
            $repo->keys()
        );

        $this->assertNull(
            $arrayStore->get('indexed_myIndex_myExpiringKey')
        );
    }

}
