<?php

namespace Henzeb\CacheIndex\Tests\Unit\CacheIndex\Repositories\IndexRepository;

use Illuminate\Cache\ArrayStore;
use Orchestra\Testbench\TestCase;
use Henzeb\CacheIndex\Repositories\IndexRepository;

class IncrementTest extends TestCase
{
    use Helpers;

    public function testIncrement(): void
    {
        $repo = new IndexRepository(
            new ArrayStore(),
            'myIndex',
        );

        $repo->increment('myKey');

        $this->assertStoreHas($repo, 'myKey', 1);

        $this->assertEquals(
            ['myKey'],
            $repo->keys()
        );

        $repo->increment('myKey', 2);

        $this->assertStoreHas($repo, 'myKey', 3);

        $this->assertEquals(
            ['myKey'],
            $repo->keys()
        );
    }
}
