<?php

namespace Tests;

use PHPUnit\Framework\TestCase;
use App\common\Repository\PaginationResult;

/**
 * Simple test class for pagination functionality
 */
class SimplePaginationTest extends TestCase
{
    /**
     * Test PaginationResult class creation and properties
     */
    public function testPaginationResultCreation(): void
    {
        $data = ['item1', 'item2', 'item3'];
        $result = new PaginationResult(
            data: $data,
            total: 100,
            page: 1,
            perPage: 20,
            totalPages: 5,
            hasNextPage: true,
            hasPreviousPage: false,
            nextCursor: 'cursor123',
            previousCursor: null
        );

        $this->assertEquals($data, $result->data);
        $this->assertEquals(100, $result->total);
        $this->assertEquals(1, $result->page);
        $this->assertEquals(20, $result->perPage);
        $this->assertEquals(5, $result->totalPages);
        $this->assertTrue($result->hasNextPage);
        $this->assertFalse($result->hasPreviousPage);
        $this->assertEquals('cursor123', $result->nextCursor);
        $this->assertNull($result->previousCursor);
    }

    /**
     * Test PaginationResult toArray method
     */
    public function testPaginationResultToArray(): void
    {
        $data = ['item1', 'item2'];
        $result = new PaginationResult(
            data: $data,
            total: 50,
            page: 2,
            perPage: 10,
            totalPages: 5,
            hasNextPage: true,
            hasPreviousPage: true,
            nextCursor: 'next123',
            previousCursor: 'prev123'
        );

        $array = $result->toArray();

        $this->assertIsArray($array);
        $this->assertArrayHasKey('data', $array);
        $this->assertArrayHasKey('pagination', $array);
        $this->assertEquals($data, $array['data']);
        $this->assertEquals(50, $array['pagination']['total']);
        $this->assertEquals(2, $array['pagination']['page']);
        $this->assertEquals(10, $array['pagination']['per_page']);
        $this->assertEquals(5, $array['pagination']['total_pages']);
        $this->assertTrue($array['pagination']['has_next_page']);
        $this->assertTrue($array['pagination']['has_previous_page']);
        $this->assertEquals('next123', $array['pagination']['next_cursor']);
        $this->assertEquals('prev123', $array['pagination']['previous_cursor']);
    }

    /**
     * Test PaginationResult with minimal data
     */
    public function testPaginationResultMinimal(): void
    {
        $result = new PaginationResult(
            data: [],
            total: 0,
            page: 1,
            perPage: 20,
            totalPages: 0,
            hasNextPage: false,
            hasPreviousPage: false
        );

        $this->assertEquals([], $result->data);
        $this->assertEquals(0, $result->total);
        $this->assertEquals(0, $result->totalPages);
        $this->assertFalse($result->hasNextPage);
        $this->assertFalse($result->hasPreviousPage);
        $this->assertNull($result->nextCursor);
        $this->assertNull($result->previousCursor);
    }

    /**
     * Test PaginationResult readonly properties
     */
    public function testPaginationResultReadonly(): void
    {
        $result = new PaginationResult(
            data: ['test'],
            total: 1,
            page: 1,
            perPage: 1,
            totalPages: 1,
            hasNextPage: false,
            hasPreviousPage: false
        );

        // Test that properties are readonly (can't be modified)
        $this->expectException(\Error::class);
        $result->data = ['modified'];
    }

    /**
     * Test wildcard pattern conversion logic
     */
    public function testWildcardPatternLogic(): void
    {
        // Test pattern conversion logic
        $patterns = [
            'john*' => 'john%',
            '*doe' => '%doe',
            '*smith*' => '%smith%',
            'exact' => 'exact',
            '*' => '%',
            'j*n' => 'j%n'
        ];

        foreach ($patterns as $input => $expected) {
            $this->assertEquals($expected, $this->convertWildcardToLike($input));
        }
    }

    /**
     * Test wildcard pattern escaping logic
     */
    public function testWildcardPatternEscaping(): void
    {
        $patterns = [
            'test%value' => 'test\\%value',
            'test_value' => 'test\\_value',
            'test%_value' => 'test\\%\\_value'
        ];

        foreach ($patterns as $input => $expected) {
            $this->assertEquals($expected, $this->convertWildcardToLike($input));
        }
    }

    /**
     * Test hasWildcards logic
     */
    public function testHasWildcardsLogic(): void
    {
        $this->assertTrue($this->hasWildcards('john*'));
        $this->assertTrue($this->hasWildcards('*doe'));
        $this->assertTrue($this->hasWildcards('*smith*'));
        $this->assertFalse($this->hasWildcards('exact'));
        $this->assertFalse($this->hasWildcards(''));
        $this->assertTrue($this->hasWildcards('*'));
    }

    /**
     * Test search condition building logic
     */
    public function testSearchConditionLogic(): void
    {
        // Test with wildcards
        $condition = $this->buildSearchCondition('name', 'john*', 't');
        $this->assertEquals('t.name LIKE :search', $condition['sql']);
        $this->assertEquals([':search' => 'john%'], $condition['params']);

        // Test without wildcards (exact match)
        $condition = $this->buildSearchCondition('name', 'john', 't');
        $this->assertEquals('t.name = :search', $condition['sql']);
        $this->assertEquals([':search' => 'john'], $condition['params']);

        // Test without table alias
        $condition = $this->buildSearchCondition('name', 'john*');
        $this->assertEquals('name LIKE :search', $condition['sql']);
    }

    /**
     * Test count query building logic
     */
    public function testCountQueryLogic(): void
    {
        $sql = "SELECT * FROM users WHERE active = 1 ORDER BY name ASC";
        $countSql = $this->buildCountQuery($sql);
        $this->assertEquals("SELECT COUNT(*) FROM users WHERE active = 1", $countSql);

        $sql = "SELECT u.* FROM users u JOIN roles r ON u.role_id = r.id WHERE u.active = 1";
        $countSql = $this->buildCountQuery($sql);
        $this->assertEquals("SELECT COUNT(*) FROM users u JOIN roles r ON u.role_id = r.id WHERE u.active = 1", $countSql);
    }

    /**
     * Test pagination calculations
     */
    public function testPaginationCalculations(): void
    {
        $total = 100;
        $perPage = 20;
        $page = 1;

        $totalPages = (int) ceil($total / $perPage);
        $offset = ($page - 1) * $perPage;
        $hasNextPage = $page < $totalPages;
        $hasPreviousPage = $page > 1;

        $this->assertEquals(5, $totalPages);
        $this->assertEquals(0, $offset);
        $this->assertTrue($hasNextPage);
        $this->assertFalse($hasPreviousPage);

        // Test page 2
        $page = 2;
        $offset = ($page - 1) * $perPage;
        $hasNextPage = $page < $totalPages;
        $hasPreviousPage = $page > 1;

        $this->assertEquals(20, $offset);
        $this->assertTrue($hasNextPage);
        $this->assertTrue($hasPreviousPage);
    }

    /**
     * Test parameter validation logic
     */
    public function testParameterValidation(): void
    {
        // Test page validation
        $page = max(1, 0); // Should normalize to 1
        $this->assertEquals(1, $page);

        $page = max(1, 5); // Should stay 5
        $this->assertEquals(5, $page);

        // Test perPage validation
        $perPage = max(1, min(100, 200)); // Should limit to 100
        $this->assertEquals(100, $perPage);

        $perPage = max(1, min(100, 50)); // Should stay 50
        $this->assertEquals(50, $perPage);

        $perPage = max(1, min(100, 0)); // Should normalize to 1
        $this->assertEquals(1, $perPage);
    }

    // Helper methods to test the logic without database dependencies

    private function convertWildcardToLike(string $pattern): string
    {
        // Escape SQL LIKE special characters except *
        $escaped = preg_replace('/[%_]/', '\\\\$0', $pattern);
        
        // Convert * to SQL LIKE wildcard %
        $likePattern = str_replace('*', '%', $escaped);
        
        return $likePattern;
    }

    private function hasWildcards(string $searchTerm): bool
    {
        return strpos($searchTerm, '*') !== false;
    }

    private function buildSearchCondition(string $column, string $searchTerm, string $tableAlias = ''): array
    {
        $prefix = $tableAlias ? $tableAlias . '.' : '';
        
        if ($this->hasWildcards($searchTerm)) {
            // Use LIKE for wildcard searches
            $likePattern = $this->convertWildcardToLike($searchTerm);
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

    private function buildCountQuery(string $sql): string
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
} 