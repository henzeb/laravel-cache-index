<?php

namespace Henzeb\CacheIndex\Tests\Unit\CacheIndex\Repositories\IndexRepository;

use Carbon\Carbon;
use Henzeb\CacheIndex\Repositories\IndexRepository;
use Illuminate\Cache\ArrayStore;
use Orchestra\Testbench\TestCase;
use function now;

class SyncTtlTest extends TestCase
{
    use Helpers;

    public function testSyncTtl(): void
    {
        Carbon::setTestNow('2024-01-01 00:00:00');

        $arrayStore = new ArrayStore();
        $repo = new IndexRepository(
            $arrayStore,
            'myIndex',
        );

        $repo->put('myKey1', 'test1');
        $repo->put('myKey2', 'test2', 10);
        $repo->put('myKey3', 'test3', 18);

        $repo->syncTtl(20);

        $seconds = now()->addSeconds(20)->timestamp . '.' . now()->addSeconds(20)->millisecond;

        $this->assertEquals(
            [
                'indexed_myIndex_myKey1' => [
                    'value' => 'test1',
                    'expiresAt' => $seconds
                ],
                'indexed_myIndex_myKey2' => [
                    'value' => 'test2',
                    'expiresAt' => $seconds
                ],
                'indexed_myIndex_myKey3' => [
                    'value' => 'test3',
                    'expiresAt' => $seconds
                ],
                'index_myIndex' => [
                    'expiresAt' => 0,
                    'value' => [
                        'myKey1',
                        'myKey2',
                        'myKey3'
                    ],
                ]
            ],
            $this->getRawStore($arrayStore)
        );
    }
}
