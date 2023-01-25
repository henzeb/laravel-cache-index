<?php

namespace Henzeb\CacheIndex\Tests\Unit\CacheIndex\Repositories\IndexRepository;

use Illuminate\Cache\ArrayStore;
use Orchestra\Testbench\TestCase;
use Henzeb\CacheIndex\Repositories\IndexRepository;

class CountTest extends TestCase
{
    public function testExpectCount(): void
    {
        $repo = new IndexRepository(
            new ArrayStore(), 'myIndex'
        );

   //     $this->assertEquals(0, $repo->count());

        foreach (\array_fill(0, 10, 'myKey') as $key => $value) {
            $repo->add($value . $key, $value);
            $this->assertEquals($key + 1, $repo->count());
        }
    }
}
