<?php

namespace Henzeb\CacheIndex\Tests\Unit\CacheIndex\Repositories\IndexRepository;

use Carbon\Carbon;
use Illuminate\Cache\ArrayStore;
use Orchestra\Testbench\TestCase;
use Illuminate\Support\Facades\Cache;
use Henzeb\CacheIndex\Repositories\IndexRepository;

class CopyTest extends TestCase
{
    use Helpers;

    public function testExpectCopy(): void
    {
        $store = new ArrayStore();

        $repo = new IndexRepository(
            $store, 'myIndex'
        );

        $receivingRepo = new IndexRepository(
            $store, 'myIndex2'
        );

        $repo->add('myKey', 'myValue');

        $repo->copy('myKey', 'myIndex2');

        $this->assertEquals(
            [
                'myKey'
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
            'myValue',
            $repo->get('myKey')
        );

        $this->assertEquals(
            'myValue',
            $receivingRepo->get('myKey')
        );
    }

    public function testExpectCopyNonExistent(): void
    {
        $store = new ArrayStore();

        $repo = new IndexRepository(
            $store, 'myIndex'
        );

        $receivingRepo = new IndexRepository(
            $store, 'myIndex2'
        );

        $repo->copy('myKey', 'myIndex2');

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
    }

    public function testExpectCopyWithExpiration(): void
    {
        Carbon::setTestNow(Carbon::createFromTimestamp(0));

        $store = new ArrayStore();

        $repo = new IndexRepository(
            $store, 'myIndex'
        );

        $receivingRepo = new IndexRepository(
            $store, 'myIndex2'
        );

        $repo->add('myKey', 'myValue', 10);

        $repo->copy('myKey', 'myIndex2', 20);

        $this->assertEquals(
            [
                'myKey'
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
            'myValue',
            $repo->get('myKey')
        );

        $this->assertEquals(
            'myValue',
            $receivingRepo->get('myKey')
        );

        $this->assertTtl($repo, 'myKey', 10);

        $this->assertTtl($receivingRepo, 'myKey', 20);

    }
}
