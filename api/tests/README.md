# Pagination Tests

This directory contains comprehensive tests for the pagination functionality implemented in the BaseRepository.

## Test Files

### 1. `SimplePaginationTest.php`
A PHPUnit test class that tests the core pagination functionality without database dependencies. This test focuses on:

- **PaginationResult class**: Creation, properties, and toArray() method
- **Wildcard pattern conversion**: Converting `*` patterns to SQL LIKE patterns
- **Pattern escaping**: Proper escaping of SQL LIKE special characters
- **Search condition building**: Dynamic SQL condition generation
- **Count query building**: Automatic count query generation
- **Pagination calculations**: Page, offset, and navigation calculations
- **Parameter validation**: Input validation and normalization

### 2. `run_pagination_tests.php`
A standalone test runner script that demonstrates all pagination functionality without requiring PHPUnit. This script:

- Tests all core functionality
- Provides detailed output with pass/fail indicators
- Shows expected vs actual results
- Can be run directly from command line

## Running the Tests

### Option 1: PHPUnit Tests (Recommended)
```bash
# Run the simple pagination tests
php vendor/bin/phpunit tests/SimplePaginationTest.php

# Run all tests in the tests directory
php vendor/bin/phpunit tests/
```

### Option 2: Standalone Test Runner
```bash
# Run the test runner script
php tests/run_pagination_tests.php
```

## Test Coverage

The tests cover the following areas:

### PaginationResult Class
- ✅ Object creation with all properties
- ✅ Readonly property behavior
- ✅ toArray() method functionality
- ✅ Edge cases (empty data, null cursors)

### Wildcard Search
- ✅ Pattern conversion (`*` → `%`)
- ✅ SQL LIKE character escaping (`%`, `_`)
- ✅ Wildcard detection
- ✅ Search condition building
- ✅ Table alias support

### Pagination Logic
- ✅ Page number calculations
- ✅ Offset calculations
- ✅ Total pages calculation
- ✅ Navigation flags (hasNextPage, hasPreviousPage)
- ✅ Parameter validation and normalization

### Query Building
- ✅ Count query generation
- ✅ ORDER BY clause removal
- ✅ Complex query handling (JOINs, subqueries)

## Expected Test Results

When running the tests, you should see output like:

```
=== Pagination Functionality Tests ===

Test 1: PaginationResult Creation
✓ PaginationResult created successfully
  - Data count: 3
  - Total: 100
  - Page: 1
  - Per page: 20
  - Total pages: 5
  - Has next: Yes
  - Has previous: No
  - Next cursor: cursor123
  - Previous cursor: null

Test 2: toArray Method
✓ toArray() method works correctly
  - Array keys: data, pagination
  - Pagination keys: total, page, per_page, total_pages, has_next_page, has_previous_page, next_cursor, previous_cursor

Test 3: Wildcard Pattern Conversion
  ✓ 'john*' -> 'john%' (expected: 'john%')
  ✓ '*doe' -> '%doe' (expected: '%doe')
  ✓ '*smith*' -> '%smith%' (expected: '%smith%')
  ✓ 'exact' -> 'exact' (expected: 'exact')
  ✓ '*' -> '%' (expected: '%')
  ✓ 'j*n' -> 'j%n' (expected: 'j%n')

...

=== All Tests Completed ===
```

## Test Dependencies

### For PHPUnit Tests
- PHPUnit 9+ (included in composer.json)
- PHP 8.0+ (for readonly properties and named arguments)

### For Standalone Tests
- PHP 8.0+
- No additional dependencies required

## Adding New Tests

To add new tests for pagination functionality:

1. **For PHPUnit tests**: Add new test methods to `SimplePaginationTest.php`
2. **For standalone tests**: Add new test cases to `run_pagination_tests.php`

### Example: Adding a new PHPUnit test
```php
public function testNewFeature(): void
{
    // Arrange
    $input = 'test*pattern';
    
    // Act
    $result = convertWildcardToLike($input);
    
    // Assert
    $this->assertEquals('test%pattern', $result);
}
```

### Example: Adding a new standalone test
```php
echo "Test X: New Feature\n";
$testCases = [
    'input1' => 'expected1',
    'input2' => 'expected2'
];

foreach ($testCases as $input => $expected) {
    $result = yourFunction($input);
    $status = $result === $expected ? '✓' : '✗';
    echo "  {$status} '{$input}' -> '{$result}' (expected: '{$expected}')\n";
}
echo "\n";
```

## Troubleshooting

### Common Issues

1. **Autoloader not found**: Make sure you're running tests from the project root directory
2. **PHPUnit not found**: Run `composer install` to install dependencies
3. **PHP version too low**: Ensure you're using PHP 8.0+ for readonly properties

### Debugging

To debug test failures:

1. Run individual tests to isolate issues
2. Check the expected vs actual output
3. Verify that the test data matches the implementation
4. Ensure all dependencies are properly loaded

## Integration Testing

For integration testing with actual database connections, you can:

1. Create a separate test class that extends the BaseRepository
2. Use a test database with sample data
3. Test actual pagination queries against the database
4. Verify that the pagination results match expected data

Example integration test structure:
```php
class PaginationIntegrationTest extends TestCase
{
    private PDO $testConnection;
    private YourRepository $repository;
    
    protected function setUp(): void
    {
        // Setup test database connection
        $this->testConnection = new PDO('mysql:host=localhost;dbname=test_db', 'user', 'pass');
        $this->repository = new YourRepository($this->testConnection);
    }
    
    public function testRealPagination(): void
    {
        // Insert test data
        // Run pagination query
        // Assert results
    }
}
``` 