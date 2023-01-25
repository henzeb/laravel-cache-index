<?php

namespace Henzeb\CacheIndex\Tests\Unit\CacheIndex\Repositories\IndexRepository;

use Illuminate\Cache\ArrayStore;
use Orchestra\Testbench\TestCase;
use Henzeb\CacheIndex\Repositories\IndexRepository;

class ForgetTest extends TestCase
{
    use Helpers;

    public function testForget(): void
    {
        $repo = new IndexRepository(
            new ArrayStore(),
            'myIndex',
        );

        $repo->add('myKey', 'myValue');

        $this->assertEquals(['myKey'], $repo->keys());

        $repo->forget('myKey');

        $this->assertEquals(
            [],
            $repo->keys()
        );

        $this->assertStoreHasNot($repo, 'myKey');
    }

    public function testDeleteCallsForget(): void
    {
        $repo = $this->mock(IndexRepository::class)
            ->makePartial();

        $repo->expects('forget')
            ->with('myKey')
            ->andReturn(true);

        $this->assertEquals(
            true,
            $repo->delete('myKey', 'myValue')
        );
    }
}
