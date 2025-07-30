<?php

namespace Tests;

use PHPUnit\Framework\TestCase;
use App\common\Repository\PaginationResult;
use App\common\Repository\BaseRepository;
use App\Domain\Model\Club\ClubRepository;
use PDO;
use PDOException;
use PHPUnit\Framework\MockObject\MockObject;

/**
 * Test class for pagination functionality
 */
class PaginationTest extends TestCase
{
    private MockObject $mockConnection;
    private TestRepository $testRepository;

    protected function setUp(): void
    {
        // Create a mock PDO connection for testing
        $this->mockConnection = $this->createMock(PDO::class);
        $this->testRepository = new TestRepository($this->mockConnection);
    }

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
     * Test wildcard pattern conversion
     */
    public function testWildcardToLikePattern(): void
    {
        $this->assertEquals('john%', $this->testRepository->testWildcardToLikePattern('john*'));
        $this->assertEquals('%doe', $this->testRepository->testWildcardToLikePattern('*doe'));
        $this->assertEquals('%smith%', $this->testRepository->testWildcardToLikePattern('*smith*'));
        $this->assertEquals('exact', $this->testRepository->testWildcardToLikePattern('exact'));
        $this->assertEquals('%', $this->testRepository->testWildcardToLikePattern('*'));
        $this->assertEquals('j%n', $this->testRepository->testWildcardToLikePattern('j*n'));
    }

    /**
     * Test wildcard pattern escaping
     */
    public function testWildcardPatternEscaping(): void
    {
        // Test that SQL LIKE special characters are escaped
        $this->assertEquals('test\\%value', $this->testRepository->testWildcardToLikePattern('test%value'));
        $this->assertEquals('test\\_value', $this->testRepository->testWildcardToLikePattern('test_value'));
        $this->assertEquals('test\\%\\_value', $this->testRepository->testWildcardToLikePattern('test%_value'));
    }

    /**
     * Test hasWildcards method
     */
    public function testHasWildcards(): void
    {
        $this->assertTrue($this->testRepository->testHasWildcards('john*'));
        $this->assertTrue($this->testRepository->testHasWildcards('*doe'));
        $this->assertTrue($this->testRepository->testHasWildcards('*smith*'));
        $this->assertFalse($this->testRepository->testHasWildcards('exact'));
        $this->assertFalse($this->testRepository->testHasWildcards(''));
        $this->assertTrue($this->testRepository->testHasWildcards('*'));
    }

    /**
     * Test buildSearchCondition method
     */
    public function testBuildSearchCondition(): void
    {
        // Test with wildcards
        $condition = $this->testRepository->testBuildSearchCondition('name', 'john*', 't');
        $this->assertEquals('t.name LIKE :search', $condition['sql']);
        $this->assertEquals([':search' => 'john%'], $condition['params']);

        // Test without wildcards (exact match)
        $condition = $this->testRepository->testBuildSearchCondition('name', 'john', 't');
        $this->assertEquals('t.name = :search', $condition['sql']);
        $this->assertEquals([':search' => 'john'], $condition['params']);

        // Test without table alias
        $condition = $this->testRepository->testBuildSearchCondition('name', 'john*');
        $this->assertEquals('name LIKE :search', $condition['sql']);
    }

    /**
     * Test buildCountQuery method
     */
    public function testBuildCountQuery(): void
    {
        $sql = "SELECT * FROM users WHERE active = 1 ORDER BY name ASC";
        $countSql = $this->testRepository->testBuildCountQuery($sql);
        $this->assertEquals("SELECT COUNT(*) FROM users WHERE active = 1", $countSql);

        $sql = "SELECT u.* FROM users u JOIN roles r ON u.role_id = r.id WHERE u.active = 1";
        $countSql = $this->testRepository->testBuildCountQuery($sql);
        $this->assertEquals("SELECT COUNT(*) FROM users u JOIN roles r ON u.role_id = r.id WHERE u.active = 1", $countSql);
    }

    /**
     * Test paginated query execution with mock data
     */
    public function testExecutePaginatedQuery(): void
    {
        // Mock the PDO statement for count query
        $countStatement = $this->createMock(\PDOStatement::class);
        $countStatement->expects($this->once())
            ->method('execute')
            ->willReturn(true);
        $countStatement->expects($this->once())
            ->method('fetchColumn')
            ->willReturn(100);

        // Mock the PDO statement for data query
        $dataStatement = $this->createMock(\PDOStatement::class);
        $dataStatement->expects($this->once())
            ->method('execute')
            ->willReturn(true);
        $dataStatement->expects($this->once())
            ->method('fetchAll')
            ->willReturn([
                ['id' => 1, 'name' => 'Test 1'],
                ['id' => 2, 'name' => 'Test 2']
            ]);

        // Mock PDO prepare method
        $this->mockConnection->expects($this->exactly(2))
            ->method('prepare')
            ->willReturnOnConsecutiveCalls($countStatement, $dataStatement);

        $result = $this->testRepository->testExecutePaginatedQuery(
            'SELECT * FROM test_table',
            'SELECT COUNT(*) FROM test_table',
            \stdClass::class,
            1,
            20,
            [],
            'ORDER BY id ASC'
        );

        $this->assertInstanceOf(PaginationResult::class, $result);
        $this->assertEquals(100, $result->total);
        $this->assertEquals(1, $result->page);
        $this->assertEquals(20, $result->perPage);
        $this->assertEquals(5, $result->totalPages);
        $this->assertTrue($result->hasNextPage);
        $this->assertFalse($result->hasPreviousPage);
    }

    /**
     * Test cursor-based pagination
     */
    public function testExecuteCursorPaginatedQuery(): void
    {
        // Mock the PDO statement for count query
        $countStatement = $this->createMock(\PDOStatement::class);
        $countStatement->expects($this->once())
            ->method('execute')
            ->willReturn(true);
        $countStatement->expects($this->once())
            ->method('fetchColumn')
            ->willReturn(100);

        // Mock the PDO statement for data query
        $dataStatement = $this->createMock(\PDOStatement::class);
        $dataStatement->expects($this->once())
            ->method('execute')
            ->willReturn(true);
        $dataStatement->expects($this->once())
            ->method('fetchAll')
            ->willReturn([
                (object)['id' => 1, 'name' => 'Test 1'],
                (object)['id' => 2, 'name' => 'Test 2']
            ]);

        // Mock PDO prepare method
        $this->mockConnection->expects($this->exactly(2))
            ->method('prepare')
            ->willReturnOnConsecutiveCalls($countStatement, $dataStatement);

        $result = $this->testRepository->testExecuteCursorPaginatedQuery(
            'SELECT * FROM test_table',
            'SELECT COUNT(*) FROM test_table',
            \stdClass::class,
            'id',
            null,
            20,
            [],
            'next'
        );

        $this->assertInstanceOf(PaginationResult::class, $result);
        $this->assertEquals(100, $result->total);
        $this->assertEquals(20, $result->perPage);
        $this->assertTrue($result->hasNextPage);
        $this->assertFalse($result->hasPreviousPage);
    }

    /**
     * Test wildcard search with pagination
     */
    public function testExecuteWildcardSearchPaginated(): void
    {
        // Mock the PDO statement for count query
        $countStatement = $this->createMock(\PDOStatement::class);
        $countStatement->expects($this->once())
            ->method('execute')
            ->willReturn(true);
        $countStatement->expects($this->once())
            ->method('fetchColumn')
            ->willReturn(50);

        // Mock the PDO statement for data query
        $dataStatement = $this->createMock(\PDOStatement::class);
        $dataStatement->expects($this->once())
            ->method('execute')
            ->willReturn(true);
        $dataStatement->expects($this->once())
            ->method('fetchAll')
            ->willReturn([
                ['id' => 1, 'name' => 'John Doe'],
                ['id' => 2, 'name' => 'John Smith']
            ]);

        // Mock PDO prepare method
        $this->mockConnection->expects($this->exactly(2))
            ->method('prepare')
            ->willReturnOnConsecutiveCalls($countStatement, $dataStatement);

        $result = $this->testRepository->testExecuteWildcardSearchPaginated(
            'SELECT * FROM users WHERE name LIKE :search',
            'john*',
            \stdClass::class,
            1,
            20
        );

        $this->assertInstanceOf(PaginationResult::class, $result);
        $this->assertEquals(50, $result->total);
        $this->assertEquals(1, $result->page);
        $this->assertEquals(20, $result->perPage);
    }

    /**
     * Test error handling in paginated queries
     */
    public function testPaginatedQueryErrorHandling(): void
    {
        // Mock PDO to throw an exception
        $this->mockConnection->expects($this->once())
            ->method('prepare')
            ->willThrowException(new PDOException('Database error'));

        $result = $this->testRepository->testExecutePaginatedQuery(
            'SELECT * FROM test_table',
            'SELECT COUNT(*) FROM test_table',
            \stdClass::class,
            1,
            20
        );

        $this->assertInstanceOf(PaginationResult::class, $result);
        $this->assertEquals([], $result->data);
        $this->assertEquals(0, $result->total);
        $this->assertEquals(0, $result->totalPages);
        $this->assertFalse($result->hasNextPage);
        $this->assertFalse($result->hasPreviousPage);
    }

    /**
     * Test pagination parameter validation
     */
    public function testPaginationParameterValidation(): void
    {
        // Mock the PDO statement for count query
        $countStatement = $this->createMock(\PDOStatement::class);
        $countStatement->expects($this->once())
            ->method('execute')
            ->willReturn(true);
        $countStatement->expects($this->once())
            ->method('fetchColumn')
            ->willReturn(100);

        // Mock the PDO statement for data query
        $dataStatement = $this->createMock(\PDOStatement::class);
        $dataStatement->expects($this->once())
            ->method('execute')
            ->willReturn(true);
        $dataStatement->expects($this->once())
            ->method('fetchAll')
            ->willReturn([]);

        // Mock PDO prepare method
        $this->mockConnection->expects($this->exactly(2))
            ->method('prepare')
            ->willReturnOnConsecutiveCalls($countStatement, $dataStatement);

        // Test with invalid page number (should be normalized to 1)
        $result = $this->testRepository->testExecutePaginatedQuery(
            'SELECT * FROM test_table',
            'SELECT COUNT(*) FROM test_table',
            \stdClass::class,
            0, // Invalid page
            20
        );

        $this->assertEquals(1, $result->page);

        // Test with invalid perPage (should be limited to 100)
        $result = $this->testRepository->testExecutePaginatedQuery(
            'SELECT * FROM test_table',
            'SELECT COUNT(*) FROM test_table',
            \stdClass::class,
            1,
            200 // Too large
        );

        $this->assertEquals(100, $result->perPage);
    }

    /**
     * Test cursor value extraction
     */
    public function testGetCursorValue(): void
    {
        // Test with object that has a getter method
        $objectWithGetter = new class {
            private $id = 'test123';
            public function getId() { return $this->id; }
        };
        
        $cursorValue = $this->testRepository->testGetCursorValue($objectWithGetter, 'id');
        $this->assertEquals('test123', $cursorValue);

        // Test with object that has a public property
        $objectWithProperty = new \stdClass();
        $objectWithProperty->id = 'test456';
        
        $cursorValue = $this->testRepository->testGetCursorValue($objectWithProperty, 'id');
        $this->assertEquals('test456', $cursorValue);

        // Test with non-existent property
        $cursorValue = $this->testRepository->testGetCursorValue($objectWithProperty, 'nonexistent');
        $this->assertNull($cursorValue);
    }
}

/**
 * Test repository class that exposes protected methods for testing
 */
class TestRepository extends BaseRepository
{
    public function sqls($type): string
    {
        return 'SELECT * FROM test_table';
    }

    public function testWildcardToLikePattern(string $pattern): string
    {
        return $this->wildcardToLikePattern($pattern);
    }

    public function testHasWildcards(string $searchTerm): bool
    {
        return $this->hasWildcards($searchTerm);
    }

    public function testBuildSearchCondition(string $column, string $searchTerm, string $tableAlias = ''): array
    {
        return $this->buildSearchCondition($column, $searchTerm, $tableAlias);
    }

    public function testBuildCountQuery(string $sql): string
    {
        return $this->buildCountQuery($sql);
    }

    public function testExecutePaginatedQuery(
        string $sql,
        string $countSql,
        string $className,
        int $page = 1,
        int $perPage = 20,
        array $params = [],
        string $orderBy = ''
    ): PaginationResult {
        return $this->executePaginatedQuery($sql, $countSql, $className, $page, $perPage, $params, $orderBy);
    }

    public function testExecuteCursorPaginatedQuery(
        string $sql,
        string $countSql,
        string $className,
        string $cursorField,
        ?string $cursor = null,
        int $perPage = 20,
        array $params = [],
        string $direction = 'next'
    ): PaginationResult {
        return $this->executeCursorPaginatedQuery($sql, $countSql, $className, $cursorField, $cursor, $perPage, $params, $direction);
    }

    public function testExecuteWildcardSearchPaginated(
        string $sql,
        string $searchTerm,
        string $className,
        int $page = 1,
        int $perPage = 20,
        array $additionalParams = [],
        string $orderBy = ''
    ): PaginationResult {
        return $this->executeWildcardSearchPaginated($sql, $searchTerm, $className, $page, $perPage, $additionalParams, $orderBy);
    }

    public function testGetCursorValue(object $object, string $field): ?string
    {
        return $this->getCursorValue($object, $field);
    }
} 