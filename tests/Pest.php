<?php

use Orchestra\Testbench\TestCase;
use Henzeb\CacheIndex\Tests\Unit\CacheIndex\Repositories\IndexRepository\Helpers;

/*
|--------------------------------------------------------------------------
| Test Case
|--------------------------------------------------------------------------
|
| The closure you provide to your test functions is always bound to a specific PHPUnit test
| case class. By default, that class is "PHPUnit\Framework\TestCase" which is changed
| below for a different one to provide convenient traits and assertion methods.
|
*/

uses(TestCase::class, Helpers::class)
    ->in('Unit/CacheIndex/Repositories/IndexRepository');
    
uses(TestCase::class)
    ->in('Unit/CacheIndex/Mixins');

/*
|--------------------------------------------------------------------------
| Expectations
|--------------------------------------------------------------------------
|
| When you're writing tests, you often need to check that values meet certain conditions. The
| "expect()" function gives you access to a set of "expectations" methods that you can use
| to assert different things. Of course, you may extend the Expectation API at any time.
|
*/

/*
|--------------------------------------------------------------------------
| Functions
|--------------------------------------------------------------------------
|
| While Pest is very powerful out-of-the-box, you may have some testing code specific to your
| project that you don't want to repeat in every file. Here you can also expose helpers as
| global functions to help you to reduce the number of lines of code in your test files.
|
*/