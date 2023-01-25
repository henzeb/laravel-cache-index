<?php

namespace Henzeb\CacheIndex\Tests\Unit\CacheIndex\Repositories\IndexRepository;

use Illuminate\Cache\ArrayStore;
use Orchestra\Testbench\TestCase;
use Henzeb\CacheIndex\Repositories\IndexRepository;

class SupportsTagsTest extends TestCase
{
    public function testExpectsNotToSupportTags(): void
    {
        $this->assertFalse(
            (
            new IndexRepository(
                new ArrayStore(), 'myIndex'
            )
            )->supportsTags()
        );
    }
}
