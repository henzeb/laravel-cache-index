<?php

namespace Henzeb\CacheIndex\Tests\Unit\CacheIndex\Repositories\IndexRepository;

use Illuminate\Cache\ArrayStore;
use Orchestra\Testbench\TestCase;
use Henzeb\CacheIndex\Repositories\IndexRepository;

class ForeverTest extends TestCase
{
    use Helpers;

    public function testForever(): void
    {
        $repo = new IndexRepository(
            new ArrayStore(),
            'myIndex'
        );

        $repo->forever('myKey', 'myValue');

        $this->assertEquals(['myKey'], $repo->keys());

        $this->assertStoreHas($repo, 'myKey', 'myValue');
        $this->assertTtl($repo, 'myKey');
    }
}
