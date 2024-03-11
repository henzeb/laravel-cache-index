<?php

namespace Henzeb\CacheIndex\Tests\Unit\CacheIndex\Repositories\IndexRepository;

use Carbon\Carbon;
use Henzeb\CacheIndex\Repositories\IndexRepository;
use Illuminate\Cache\ArrayStore;
use Orchestra\Testbench\TestCase;

class MoveTest extends TestCase
{
    use Helpers;

    public function testExpectMove(): void
    {
        $store = new ArrayStore();

        $repo = new IndexRepository(
            $store, 'myIndex'
        );

        $receivingRepo = new IndexRepository(
            $store, 'myIndex2'
        );

        $repo->add('myKey', 'myValue');

        $repo->move('myKey', 'myIndex2');

        $this->assertEquals(
            [
            ],
            $this->getRawIndex($repo)
        );

        $this->assertEquals(
            [
                'myKey'
            ],
            $this->getRawIndex($receivingRepo)
        );

        $this->assertEquals(
            null,
            $repo->get('myKey')
        );

        $this->assertEquals(
            'myValue',
            $receivingRepo->get('myKey')
        );

        $this->assertStoreHasNot($repo, 'myKey');

        $this->assertStoreHas($receivingRepo, 'myKey', 'myValue');
    }

    public function testExpectMoveNonExistent(): void
    {
        $store = new ArrayStore();

        $repo = new IndexRepository(
            $store, 'myIndex'
        );

        $receivingRepo = new IndexRepository(
            $store, 'myIndex2'
        );

        $repo->move('myKey', 'myIndex2');

        $this->assertEquals([], $this->getRawIndex($repo));

        $this->assertEquals(
            [],
            $this->getRawIndex($receivingRepo)
        );

        $this->assertEquals(
            null,
            $repo->get('myKey')
        );

        $this->assertEquals(
            null,
            $receivingRepo->get('myKey')
        );

        $this->assertStoreHasNot($repo, 'myKey');

        $this->assertStoreHasNot($receivingRepo, 'myKey');
    }

    public function testExpectMovesWithExpiration(): void
    {
        Carbon::setTestNow('2024-01-01 00:00:00');

        $store = new ArrayStore();

        $repo = new IndexRepository(
            $store, 'myIndex'
        );

        $receivingRepo = new IndexRepository(
            $store, 'myIndex2'
        );

        $repo->add('myKey', 'myValue', 10);

        $repo->move('myKey', 'myIndex2', 10);

        $this->assertEquals([], $this->getRawIndex($repo));

        $this->assertEquals(
            [
                'myKey',
            ],
            $this->getRawIndex($receivingRepo)
        );

        $this->assertStoreHas($receivingRepo, 'myKey', 'myValue');

        $this->assertTtl($receivingRepo, 'myKey', 10);
    }
}
