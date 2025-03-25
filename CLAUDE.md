# Laravel Cache Index Development Guide

## Commands
- Run all tests: `composer test`
- Run a specific test: `vendor/bin/pest --filter="test name"` 
- Run tests in a directory: `vendor/bin/pest tests/Unit/CacheIndex/Repositories/IndexRepository`
- Run with coverage: `composer test-coverage`
- Run with TestDox: `composer test-dox`
- Parallel testing: `vendor/bin/pest --parallel`

## Code Guidelines
- Follow PSR-2 Coding Standard
- PHP 8.1+ required
- Laravel 10.0+ or 11.0+ required
- Use type hints consistently (especially for iterables)
- Use template types for generic cache values (TCacheValue)
- Method names use camelCase
- Class names use PascalCase
- Namespaces follow PSR-4 structure

## Testing Guidelines
- Each method needs a dedicated test file
- Tests should be organized in the same structure as src/
- All PRs must include tests for new features

## PR Guidelines
- One feature per PR
- Maintain coherent commit history
- All tests must pass
- Update documentation as needed
- Follow semantic versioning