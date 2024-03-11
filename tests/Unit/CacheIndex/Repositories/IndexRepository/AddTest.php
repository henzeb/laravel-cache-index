<?php

namespace Henzeb\CacheIndex\Tests\Unit\CacheIndex\Repositories\IndexRepository;

use Carbon\Carbon;
use Henzeb\CacheIndex\Repositories\IndexRepository;
use Illuminate\Cache\ArrayStore;
use Orchestra\Testbench\TestCase;

class AddTest extends TestCase
{
    use Helpers;

    public function testAdd(): void
    {
        $repo = new IndexRepository(
            new ArrayStore(),
            'myIndex',
        );

        $repo->add('myKey', 'myValue');


        $this->assertEquals(
            [
                'myKey'
            ],
            $repo->keys()
        );

        $this->assertStoreHas($repo, 'myKey', 'myValue');
    }

    public function testAddAgain(): void
    {
        Carbon::setTestNow('2024-01-01 00:00:00');

        $mock = new ArrayStore();

        $repo = new IndexRepository(
            $mock,
            'myIndex',
            'array'
        );

        Carbon::setTestNow(now());
        $repo->add('myKey', 'myValue', now()->addSeconds(10));

        $this->assertTtl($repo, 'myKey', 10);

        $repo->add('myKey', 'myValue', now()->addSeconds(20));

        $this->assertTtl($repo, 'myKey', 10);


        $this->assertEquals(
            [
                'myKey'
            ],
            $repo->keys()
        );
    }

    public function testAddWithAddMethodOnStore(): void
    {
        $store = new class extends ArrayStore {
            public function add(string $key, mixed $value, $ttl)
            {
                return true;
            }
        };

        $mock = $this->mock($store::class)->makePartial();
        $mock->expects('add')->andReturnTrue();

        $repo = new IndexRepository(
            $mock,
            'myIndex',
            'array'
        );
        $repo->add('myKey', 'myValue', 10);
    }
}
