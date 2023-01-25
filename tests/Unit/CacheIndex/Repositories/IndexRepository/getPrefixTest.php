<?php

namespace Henzeb\CacheIndex\Tests\Unit\CacheIndex\Repositories\IndexRepository;

use PHPUnit\Framework\TestCase;
use Illuminate\Cache\ArrayStore;
use Henzeb\CacheIndex\Repositories\IndexRepository;

class getPrefixTest extends TestCase
{
    public function testGetPrefix(): void
    {
        $repo = new IndexRepository(new ArrayStore(), 'myIndex');
        $this->assertEquals('indexed_myIndex_', $repo->getPrefix());
    }
}
