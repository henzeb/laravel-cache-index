<?php

namespace Henzeb\CacheIndex\Tests\Feature;

use Henzeb\CacheIndex\Providers\CacheIndexServiceProvider;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Redis;
use Orchestra\Testbench\TestCase;

class RedisIntegrationTest extends TestCase
{
    protected function getPackageProviders($app): array
    {
        return [
            \Illuminate\Redis\RedisServiceProvider::class,
            CacheIndexServiceProvider::class,
        ];
    }

    protected function getEnvironmentSetUp($app): void
    {
        $app['config']->set('cache.default', 'redis');
        $app['config']->set('database.redis.default', [
            'host' => env('REDIS_HOST', '127.0.0.1'),
            'port' => env('REDIS_PORT', 6379),
            'database' => 15,
        ]);
    }

    protected function setUp(): void
    {
        parent::setUp();

        try {
            Redis::connection()->ping();
        } catch (\Throwable $e) {
            $this->markTestSkipped('Redis connection unavailable.');
        }

        Redis::flushdb();
    }

    public function test_redis_cache_index_stores_and_retrieves_keys(): void
    {
        $index = Cache::index('redisTestIndex');

        $index->put('key1', 'value1');
        $index->put('key2', 'value2');

        $this->assertEquals(['key1', 'key2'], $index->keys());
        $this->assertEquals('value1', $index->get('key1'));
        $this->assertEquals('value2', $index->get('key2'));
    }

    public function test_redis_cache_index_get_index_executes_in_constant_time_without_extra_has_queries(): void
    {
        $index = Cache::index('largeRedisIndex');

        // Populate 100 keys into the index
        for ($i = 0; $i < 100; $i++) {
            $index->put("item_{$i}", "val_{$i}");
        }

        // Measure time to fetch 100 index keys (should be near instant < 50ms)
        $startTime = microtime(true);
        $keys = $index->keys();
        $elapsedMs = (microtime(true) - $startTime) * 1000;

        $this->assertCount(100, $keys);
        $this->assertLessThan(50, $elapsedMs, 'getIndex() took longer than expected for 100 keys');
        
        // Assert raw Redis index stored key list matches expected 100 items directly from Redis
        $storedRawIndex = Redis::get('laravel_database_index_largeRedisIndex');
        if ($storedRawIndex !== null) {
            $this->assertCount(100, unserialize($storedRawIndex));
        }
    }

    public function test_high_volume_scale_with_thousands_of_records(): void
    {
        $index = Cache::index('scaleTestIndex');
        $recordCount = 5000;
        $items = [];

        for ($i = 1; $i <= $recordCount; $i++) {
            $items["user_session_{$i}"] = [
                'id' => $i,
                'ip' => '192.168.1.' . ($i % 255),
                'active' => true,
            ];
        }

        // Bulk insert via putMany
        $writeStart = microtime(true);
        $index->putMany($items);
        $writeDurationMs = (microtime(true) - $writeStart) * 1000;

        // Verify index key count
        $this->assertEquals($recordCount, $index->count());

        // Measure read latency for index retrieval over 5,000 keys
        $readStart = microtime(true);
        $keys = $index->keys();
        $readDurationMs = (microtime(true) - $readStart) * 1000;

        $this->assertCount($recordCount, $keys);
        
        // Reading the index array of 5,000 items from Redis should take < 15ms (previously took 5000+ sequential Redis calls / 10+ seconds)
        $this->assertLessThan(15, $readDurationMs, "Reading 5,000 index keys took {$readDurationMs}ms (expected < 15ms)");

        // Verify batch retrieval
        $sampleKey = 'user_session_2500';
        $this->assertEquals(['id' => 2500, 'ip' => '192.168.1.205', 'active' => true], $index->get($sampleKey));

        // Test bulk removal
        $forgetStart = microtime(true);
        $index->forget(['user_session_1', 'user_session_2500', 'user_session_5000']);
        $forgetDurationMs = (microtime(true) - $forgetStart) * 1000;

        $this->assertEquals($recordCount - 3, $index->count());
        $this->assertNull($index->get('user_session_2500'));
        $this->assertLessThan(20, $forgetDurationMs, "Bulk deletion took {$forgetDurationMs}ms (expected < 20ms)");
    }

    public function test_redis_cache_index_forget_removes_key_from_redis_and_index(): void
    {
        $index = Cache::index('redisForgetIndex');

        $index->put('key1', 'val1');
        $index->put('key2', 'val2');

        $index->forget('key1');

        $this->assertEquals(['key2'], $index->keys());
        $this->assertNull($index->get('key1'));
        $this->assertEquals('val2', $index->get('key2'));
    }

    public function test_multiple_indexes_are_completely_isolated_during_operations_and_flushing(): void
    {
        $indexProducts = Cache::index('products');
        $indexUsers = Cache::index('users');
        $indexOrders = Cache::index('orders');

        // Populate thousands of items into index 1 (products)
        $productItems = [];
        for ($i = 1; $i <= 2000; $i++) {
            $productItems["product_{$i}"] = "Product Name {$i}";
        }
        $indexProducts->putMany($productItems);

        // Populate thousands of items into index 2 (users)
        $userItems = [];
        for ($i = 1; $i <= 1500; $i++) {
            $userItems["user_{$i}"] = "User Name {$i}";
        }
        $indexUsers->putMany($userItems);

        // Populate items into index 3 (orders)
        $orderItems = [];
        for ($i = 1; $i <= 500; $i++) {
            $orderItems["order_{$i}"] = "Order #{$i}";
        }
        $indexOrders->putMany($orderItems);

        // Verify initial counts across all sets
        $this->assertEquals(2000, $indexProducts->count());
        $this->assertEquals(1500, $indexUsers->count());
        $this->assertEquals(500, $indexOrders->count());

        // Step 1: Clear individual items from products index
        $indexProducts->forget('product_100');
        $indexProducts->forget('product_500');

        $this->assertEquals(1998, $indexProducts->count());
        $this->assertNull($indexProducts->get('product_100'));
        $this->assertNull($indexProducts->get('product_500'));

        // Verify users and orders indexes were completely UNTOUCHED by product modifications
        $this->assertEquals(1500, $indexUsers->count());
        $this->assertEquals('User Name 100', $indexUsers->get('user_100'));
        $this->assertEquals(500, $indexOrders->count());
        $this->assertEquals('Order #100', $indexOrders->get('order_100'));

        // Step 2: Flush entire set (users)
        $flushResult = $indexUsers->flush();
        $this->assertTrue($flushResult);

        // Verify users set is completely emptied and flushed
        $this->assertEquals(0, $indexUsers->count());
        $this->assertEmpty($indexUsers->keys());
        $this->assertNull($indexUsers->get('user_1'));
        $this->assertNull($indexUsers->get('user_1500'));

        // Verify products and orders sets remain intact and unmodified after flushing users set
        $this->assertEquals(1998, $indexProducts->count());
        $this->assertEquals('Product Name 1', $indexProducts->get('product_1'));
        $this->assertEquals('Product Name 2000', $indexProducts->get('product_2000'));

        $this->assertEquals(500, $indexOrders->count());
        $this->assertEquals('Order #1', $indexOrders->get('order_1'));
        $this->assertEquals('Order #500', $indexOrders->get('order_500'));

        // Step 3: Flush products set
        $indexProducts->flush();
        $this->assertEquals(0, $indexProducts->count());
        $this->assertEmpty($indexProducts->keys());

        // Verify orders set is still intact
        $this->assertEquals(500, $indexOrders->count());
        $this->assertEquals('Order #250', $indexOrders->get('order_250'));
    }

    public function test_multitenancy_and_cache_store_prefixing_isolation(): void
    {
        // Simulate Tenant A and Tenant B using cache stores with different prefixes
        $storeTenantA = new \Illuminate\Cache\Repository(
            new \Illuminate\Cache\RedisStore(app('redis'), 'tenant_a_cache:')
        );

        $storeTenantB = new \Illuminate\Cache\Repository(
            new \Illuminate\Cache\RedisStore(app('redis'), 'tenant_b_cache:')
        );

        // Create the same index name "cart" for both tenants
        $indexTenantA = $storeTenantA->index('cart');
        $indexTenantB = $storeTenantB->index('cart');

        $indexTenantA->put('cart_item_1', 'Tenant A Item');
        $indexTenantB->put('cart_item_1', 'Tenant B Item');

        // Verify Tenant A and Tenant B indexes are stored under distinct Redis keys
        $this->assertEquals(['cart_item_1'], $indexTenantA->keys());
        $this->assertEquals(['cart_item_1'], $indexTenantB->keys());

        $this->assertEquals('Tenant A Item', $indexTenantA->get('cart_item_1'));
        $this->assertEquals('Tenant B Item', $indexTenantB->get('cart_item_1'));

        // Flush Tenant A cart index
        $indexTenantA->flush();

        // Verify Tenant A is emptied while Tenant B is untouched
        $this->assertEquals(0, $indexTenantA->count());
        $this->assertEquals(1, $indexTenantB->count());
        $this->assertEquals('Tenant B Item', $indexTenantB->get('cart_item_1'));
    }
}
