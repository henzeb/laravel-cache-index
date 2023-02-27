<?php

namespace Henzeb\CacheIndex\Tests\Unit\CacheIndex\Repositories\IndexRepository;

use Henzeb\CacheIndex\Repositories\IndexRepository;
use Illuminate\Cache\ArrayStore;
use Orchestra\Testbench\TestCase;

class RandomKeyTest extends TestCase
{
    use Helpers;

    public function testEmptyRandomKey(): void
    {
        $repo = new IndexRepository(
            new ArrayStore(),
            'myIndex',
        );

        $this->assertEquals(null, $repo->randomKey());
    }

    public function testRandomKey(): void
    {
        $repo = new IndexRepository(
            new ArrayStore(),
            'myIndex',
        );

        $repo->add('myKey1', 'myValue1');
        $repo->add('myKey2', 'myValue2');
        $repo->add('myKey3', 'myValue3');
        $repo->add('myKey4', 'myValue4');

        $item = $repo->randomKey();

        $this->assertStringContainsString('myKey', $item);

        $this->assertEquals(
            4,
            $repo->count()
        );
    }
}
