<?php

namespace Henzeb\CacheIndex\Tests\Unit\CacheIndex\Repositories\IndexRepository;

use PHPUnit\Framework\TestCase;
use Illuminate\Cache\ArrayStore;
use Henzeb\CacheIndex\Repositories\IndexRepository;

class ManyTest extends TestCase
{
    public function testShouldReturnCorrectArrayOfData()
    {
        $repo = new IndexRepository(
            new ArrayStore(),
            'myIndex'
        );
        $repo->put('myKey1', 'myValue1');
        $repo->put('myKey2', 'myValue2');

        $this->assertEquals(
            [
                'myKey1' => 'myValue1',
                'myKey2' => 'myValue2'
            ],
            $repo->many([
                'myKey1',
                'myKey2'
            ])
        );

        $this->assertEquals(
            [
                'myKey1' => 'myValue1',
                'myKey2' => 'myValue2'
            ],
            $repo->getMultiple([
                'myKey1',
                'myKey2'
            ])
        );
    }

    public function testShouldReturnCorrectArrayOfDataWithDefaults()
    {
        $repo = new IndexRepository(
            new ArrayStore(),
            'myIndex'
        );
        $repo->put('myKey1', 'myValue1');
        $repo->put('myKey2', 'myValue2');

        $this->assertEquals(
            [
                'myKey1' => 'myValue1',
                'myKey2' => 'myValue2',
                'myKey3' => 'aDefault',
            ],
            $repo->getMultiple([
                'myKey1',
                'myKey2',
                'myKey3'
            ], 'aDefault')
        );
    }
}
