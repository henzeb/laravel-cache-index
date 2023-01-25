<?php

namespace Henzeb\CacheIndex\Tests\Unit\CacheIndex\Repositories\IndexRepository;

use Carbon\Carbon;
use Illuminate\Cache\ArrayStore;
use Orchestra\Testbench\TestCase;
use Henzeb\CacheIndex\Repositories\IndexRepository;

use function now;

class PutManyTest extends TestCase
{
    use Helpers;

    public function testPut(): void
    {
        $repo = new IndexRepository(
            new ArrayStore(),
            'myIndex',
        );

        $repo->putMany(
            [
                'myKey' => 'myValue',
                'myKey2' => 'myValue2'
            ]
        );

        $this->assertEquals(
            [
                'myKey',
                'myKey2',
            ],
            $repo->keys()
        );
    }

    public function testPutExpiration(): void
    {
        Carbon::setTestNow();


        $repo = new IndexRepository(
            new ArrayStore(),
            'myIndex',
        );

        $repo->putMany(
            [
                'myKey' => 'myValue',
                'myKey2' => 'myValue2'
            ],
            now()->addSeconds(10)
        );

        $repo->putMany(
            [
                'myKey' => 'myValue',
                'myKey3' => 'myValue3'
            ],
            now()->addSeconds(20)
        );

        $this->assertEquals(
            [
                'myKey',
                'myKey2',
                'myKey3',
            ],
            $this->getRawIndex($repo)
        );
    }

    public function testSetMultipleCallsPutMany(): void
    {
        $repo = $this->mock(IndexRepository::class)
            ->makePartial();

        $repo->expects('putMany')
            ->with(['myKey' => 'myValue'], null)
            ->andReturn(true);

        $repo->expects('putMany')
            ->with(['myKey' => 'myValue'], 10)
            ->andReturn(true);

        $this->assertEquals(
            true,
            $repo->setMultiple(['myKey' => 'myValue'])
        );

        $this->assertEquals(
            true,
            $repo->setMultiple(['myKey' => 'myValue'], 10)
        );
    }
}
