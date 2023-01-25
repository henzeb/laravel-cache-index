<?php

namespace Henzeb\CacheIndex\Tests\Unit\CacheIndex\Repositories\IndexRepository;

use Illuminate\Cache\ArrayStore;
use Orchestra\Testbench\TestCase;
use Illuminate\Contracts\Cache\Store;
use Henzeb\CacheIndex\Repositories\IndexRepository;

class PullTest extends TestCase
{
    use Helpers;

    public function testPull(): void
    {
        $repo = new IndexRepository(
            new ArrayStore(),
            'myIndex',
        );

        $repo->add('myKey', 'myValue');

        $this->assertEquals(['myKey'], $repo->keys());

        $repo->pull('myKey');

        $this->assertEquals(
            [],
            $repo->keys()
        );
    }
}
