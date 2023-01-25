<?php

namespace Henzeb\CacheIndex\Tests\Unit\CacheIndex\Repositories\IndexRepository;

use Carbon\Carbon;
use Illuminate\Cache\ArrayStore;
use Orchestra\Testbench\TestCase;
use Henzeb\CacheIndex\Repositories\IndexRepository;

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

        Carbon::setTestNow();

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
            now()->addSeconds(10)
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
            now()->addSeconds(11)
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
