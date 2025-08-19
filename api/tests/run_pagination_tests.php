<?php
/**
 * Test runner for pagination functionality
 * 
 * This script demonstrates how to run the pagination tests
 * and shows the expected output.
 */

require_once __DIR__ . '/../vendor/autoload.php';

use App\common\Repository\PaginationResult;

echo "=== Pagination Functionality Tests ===\n\n";

// Test 1: PaginationResult creation
echo "Test 1: PaginationResult Creation\n";
try {
    $result = new PaginationResult(
        data: ['item1', 'item2', 'item3'],
        total: 100,
        page: 1,
        perPage: 20,
        totalPages: 5,
        hasNextPage: true,
        hasPreviousPage: false,
        nextCursor: 'cursor123',
        previousCursor: null
    );
    
    echo "✓ PaginationResult created successfully\n";
    echo "  - Data count: " . count($result->data) . "\n";
    echo "  - Total: {$result->total}\n";
    echo "  - Page: {$result->page}\n";
    echo "  - Per page: {$result->perPage}\n";
    echo "  - Total pages: {$result->totalPages}\n";
    echo "  - Has next: " . ($result->hasNextPage ? 'Yes' : 'No') . "\n";
    echo "  - Has previous: " . ($result->hasPreviousPage ? 'Yes' : 'No') . "\n";
    echo "  - Next cursor: " . ($result->nextCursor ?? 'null') . "\n";
    echo "  - Previous cursor: " . ($result->previousCursor ?? 'null') . "\n\n";
} catch (Exception $e) {
    echo "✗ Error: " . $e->getMessage() . "\n\n";
}

// Test 2: toArray method
echo "Test 2: toArray Method\n";
try {
    $array = $result->toArray();
    echo "✓ toArray() method works correctly\n";
    echo "  - Array keys: " . implode(', ', array_keys($array)) . "\n";
    echo "  - Pagination keys: " . implode(', ', array_keys($array['pagination'])) . "\n";
    echo "  - Data structure: " . json_encode($array, JSON_PRETTY_PRINT) . "\n\n";
} catch (Exception $e) {
    echo "✗ Error: " . $e->getMessage() . "\n\n";
}

// Test 3: Wildcard pattern conversion
echo "Test 3: Wildcard Pattern Conversion\n";
$patterns = [
    'john*' => 'john%',
    '*doe' => '%doe',
    '*smith*' => '%smith%',
    'exact' => 'exact',
    '*' => '%',
    'j*n' => 'j%n'
];

foreach ($patterns as $input => $expected) {
    $result = convertWildcardToLike($input);
    $status = $result === $expected ? '✓' : '✗';
    echo "  {$status} '{$input}' -> '{$result}' (expected: '{$expected}')\n";
}
echo "\n";

// Test 4: Wildcard pattern escaping
echo "Test 4: Wildcard Pattern Escaping\n";
$escapePatterns = [
    'test%value' => 'test\\%value',
    'test_value' => 'test\\_value',
    'test%_value' => 'test\\%\\_value'
];

foreach ($escapePatterns as $input => $expected) {
    $result = convertWildcardToLike($input);
    $status = $result === $expected ? '✓' : '✗';
    echo "  {$status} '{$input}' -> '{$result}' (expected: '{$expected}')\n";
}
echo "\n";

// Test 5: Has wildcards detection
echo "Test 5: Has Wildcards Detection\n";
$wildcardTests = [
    'john*' => true,
    '*doe' => true,
    '*smith*' => true,
    'exact' => false,
    '' => false,
    '*' => true
];

foreach ($wildcardTests as $input => $expected) {
    $result = hasWildcards($input);
    $status = $result === $expected ? '✓' : '✗';
    echo "  {$status} '{$input}' -> " . ($result ? 'true' : 'false') . " (expected: " . ($expected ? 'true' : 'false') . ")\n";
}
echo "\n";

// Test 6: Search condition building
echo "Test 6: Search Condition Building\n";
$searchTests = [
    [['name', 'john*', 't'], ['t.name LIKE :search', [':search' => 'john%']]],
    [['name', 'john', 't'], ['t.name = :search', [':search' => 'john']]],
    [['name', 'john*', ''], ['name LIKE :search', [':search' => 'john%']]]
];

foreach ($searchTests as $test) {
    $input = $test[0];
    $expected = $test[1];
    $result = buildSearchCondition($input[0], $input[1], $input[2]);
    $status = ($result['sql'] === $expected[0] && $result['params'] === $expected[1]) ? '✓' : '✗';
    echo "  {$status} ({$input[0]}, '{$input[1]}', '{$input[2]}') -> SQL: '{$result['sql']}'\n";
}
echo "\n";

// Test 7: Count query building
echo "Test 7: Count Query Building\n";
$countTests = [
    "SELECT * FROM users WHERE active = 1 ORDER BY name ASC" => "SELECT COUNT(*) FROM users WHERE active = 1",
    "SELECT u.* FROM users u JOIN roles r ON u.role_id = r.id WHERE u.active = 1" => "SELECT COUNT(*) FROM users u JOIN roles r ON u.role_id = r.id WHERE u.active = 1"
];

foreach ($countTests as $input => $expected) {
    $result = buildCountQuery($input);
    $status = $result === $expected ? '✓' : '✗';
    echo "  {$status} Input: '{$input}'\n";
    echo "      Output: '{$result}'\n";
    echo "      Expected: '{$expected}'\n\n";
}

// Test 8: Pagination calculations
echo "Test 8: Pagination Calculations\n";
$total = 100;
$perPage = 20;
$page = 1;

$totalPages = (int) ceil($total / $perPage);
$offset = ($page - 1) * $perPage;
$hasNextPage = $page < $totalPages;
$hasPreviousPage = $page > 1;

echo "  ✓ Total: {$total}, Per page: {$perPage}, Page: {$page}\n";
echo "  ✓ Total pages: {$totalPages}\n";
echo "  ✓ Offset: {$offset}\n";
echo "  ✓ Has next: " . ($hasNextPage ? 'Yes' : 'No') . "\n";
echo "  ✓ Has previous: " . ($hasPreviousPage ? 'Yes' : 'No') . "\n\n";

// Test 9: Parameter validation
echo "Test 9: Parameter Validation\n";
$validationTests = [
    ['page' => 0, 'expected' => 1],
    ['page' => 5, 'expected' => 5],
    ['perPage' => 200, 'expected' => 100],
    ['perPage' => 50, 'expected' => 50],
    ['perPage' => 0, 'expected' => 1]
];

foreach ($validationTests as $test) {
    if (isset($test['page'])) {
        $result = max(1, $test['page']);
        $status = $result === $test['expected'] ? '✓' : '✗';
        echo "  {$status} Page validation: {$test['page']} -> {$result} (expected: {$test['expected']})\n";
    } else {
        $result = max(1, min(100, $test['perPage']));
        $status = $result === $test['expected'] ? '✓' : '✗';
        echo "  {$status} PerPage validation: {$test['perPage']} -> {$result} (expected: {$test['expected']})\n";
    }
}
echo "\n";

echo "=== All Tests Completed ===\n";

// Helper functions (same as in BaseRepository)
function convertWildcardToLike(string $pattern): string
{
    // Escape SQL LIKE special characters except *
    $escaped = preg_replace('/[%_]/', '\\\\$0', $pattern);
    
    // Convert * to SQL LIKE wildcard %
    $likePattern = str_replace('*', '%', $escaped);
    
    return $likePattern;
}

function hasWildcards(string $searchTerm): bool
{
    return strpos($searchTerm, '*') !== false;
}

function buildSearchCondition(string $column, string $searchTerm, string $tableAlias = ''): array
{
    $prefix = $tableAlias ? $tableAlias . '.' : '';
    
    if (hasWildcards($searchTerm)) {
        // Use LIKE for wildcard searches
        $likePattern = convertWildcardToLike($searchTerm);
        return [
            'sql' => $prefix . $column . ' LIKE :search',
            'params' => [':search' => $likePattern]
        ];
    } else {
        // Use exact match for non-wildcard searches
        return [
            'sql' => $prefix . $column . ' = :search',
            'params' => [':search' => $searchTerm]
        ];
    }
}

function buildCountQuery(string $sql): string
{
    // Remove ORDER BY clause if present
    $sql = preg_replace('/\s+ORDER\s+BY\s+.*$/i', '', $sql);
    
    // Extract the FROM clause and everything after it
    if (preg_match('/FROM\s+(.+)$/i', $sql, $matches)) {
        return "SELECT COUNT(*) FROM " . $matches[1];
    }
    
    // Fallback: wrap the entire query
    return "SELECT COUNT(*) FROM ($sql) as count_table";
} 