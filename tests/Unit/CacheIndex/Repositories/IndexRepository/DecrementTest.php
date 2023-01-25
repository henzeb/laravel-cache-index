<?php

namespace Henzeb\CacheIndex\Tests\Unit\CacheIndex\Repositories\IndexRepository;

use Carbon\Carbon;
use Illuminate\Cache\ArrayStore;
use Orchestra\Testbench\TestCase;
use Illuminate\Contracts\Cache\Store;
use Henzeb\CacheIndex\Repositories\IndexRepository;

use function now;

class DecrementTest extends TestCase
{
    use Helpers;

    public function testDecrement(): void
    {


        $repo = new IndexRepository(
            new ArrayStore(),
            'myIndex',
        );

        $repo->decrement('myKey');

        $this->assertStoreHas($repo, 'myKey', -1);

        $this->assertEquals(
            ['myKey'],
            $repo->keys()
        );

        $repo->decrement('myKey' , 2);

        $this->assertStoreHas($repo, 'myKey', -3);

        $this->assertEquals(
            ['myKey'],
            $repo->keys()
        );
    }
}
