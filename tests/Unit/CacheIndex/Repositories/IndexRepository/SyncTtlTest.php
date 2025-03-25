<?php

use Carbon\Carbon;
use Henzeb\CacheIndex\Repositories\IndexRepository;
use Illuminate\Cache\ArrayStore;

test('sync ttl', function () {
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

    // Use timestamp with float format to ensure consistent comparison
    $expectedExpiry = now()->addSeconds(20)->timestamp + (now()->addSeconds(20)->millisecond / 1000);
    $rawStore = $this->getRawStore($arrayStore);
    
    // Check each key individually since array order might be different
    expect($rawStore)->toHaveKey('indexed_myIndex_myKey1');
    expect($rawStore['indexed_myIndex_myKey1']['value'])->toBe('test1');
    expect($rawStore['indexed_myIndex_myKey1']['expiresAt'])->toEqual($expectedExpiry);
    
    expect($rawStore)->toHaveKey('indexed_myIndex_myKey2');
    expect($rawStore['indexed_myIndex_myKey2']['value'])->toBe('test2');
    expect($rawStore['indexed_myIndex_myKey2']['expiresAt'])->toEqual($expectedExpiry);
    
    expect($rawStore)->toHaveKey('indexed_myIndex_myKey3');
    expect($rawStore['indexed_myIndex_myKey3']['value'])->toBe('test3');
    expect($rawStore['indexed_myIndex_myKey3']['expiresAt'])->toEqual($expectedExpiry);
    
    expect($rawStore)->toHaveKey('index_myIndex');
    expect($rawStore['index_myIndex']['expiresAt'])->toBe(0);
    expect($rawStore['index_myIndex']['value'])->toBe(['myKey1', 'myKey2', 'myKey3']);
});