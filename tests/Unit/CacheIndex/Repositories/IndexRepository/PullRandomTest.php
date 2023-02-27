<?php

namespace Henzeb\CacheIndex\Tests\Unit\CacheIndex\Repositories\IndexRepository;

use Henzeb\CacheIndex\Repositories\IndexRepository;
use Illuminate\Cache\ArrayStore;
use Orchestra\Testbench\TestCase;

class PullRandomTest extends TestCase
{
    use Helpers;

    public function testEmptyPullRandom(): void
    {
        $repo = new IndexRepository(
            new ArrayStore(),
            'myIndex',
        );

        $this->assertEquals(null, $repo->pullRandom());
    }

    public function testPullRandom(): void
    {
        $repo = new IndexRepository(
            new ArrayStore(),
            'myIndex',
        );

        $repo->add('myKey1', 'myValue1');
        $repo->add('myKey2', 'myValue2');
        $repo->add('myKey3', 'myValue3');
        $repo->add('myKey4', 'myValue4');

        $this->assertStringContainsString('myValue', $repo->pullRandom());

        $this->assertEquals(
            3,
            $repo->count()
        );
    }
}
