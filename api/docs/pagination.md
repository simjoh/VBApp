# Pagination Functionality

This document describes the modern pagination functionality that has been added to the `BaseRepository` class, providing flexible and efficient pagination capabilities for all your repositories.

## Overview

The pagination functionality provides two main types of pagination:
1. **Offset/Limit Pagination** - Traditional page-based pagination
2. **Cursor-based Pagination** - Performance-optimized pagination for large datasets

## Features

- **Multiple Pagination Types**: Support for both offset/limit and cursor-based pagination
- **Modern PHP**: Uses PHP 8+ features like readonly properties and named arguments
- **Flexible API**: Easy to implement in any repository
- **Error Handling**: Built-in error handling and validation
- **Performance Optimized**: Automatic query optimization and parameter binding
- **Type Safety**: Strong typing with PHP 8+ features

## PaginationResult Class

The `PaginationResult` class is a modern PHP 8+ class that encapsulates pagination information:

```php
class PaginationResult
{
    public function __construct(
        public readonly array $data,           // The actual data
        public readonly int $total,            // Total number of records
        public readonly int $page,             // Current page (1-based)
        public readonly int $perPage,          // Items per page
        public readonly int $totalPages,       // Total number of pages
        public readonly bool $hasNextPage,     // Has next page
        public readonly bool $hasPreviousPage, // Has previous page
        public readonly ?string $nextCursor = null,     // Next cursor (cursor pagination)
        public readonly ?string $previousCursor = null  // Previous cursor (cursor pagination)
    ) {}
}
```

## BaseRepository Methods

### `executePaginatedQuery()`
Executes offset/limit pagination queries.

```php
protected function executePaginatedQuery(
    string $sql,           // Base SQL query (without LIMIT/OFFSET)
    string $countSql,      // Count SQL query
    string $className,     // Model class name
    int $page = 1,         // Page number (1-based)
    int $perPage = 20,     // Items per page (max 100)
    array $params = [],    // Query parameters
    string $orderBy = ''   // ORDER BY clause
): PaginationResult
```

### `executeCursorPaginatedQuery()`
Executes cursor-based pagination queries.

```php
protected function executeCursorPaginatedQuery(
    string $sql,           // Base SQL query
    string $countSql,      // Count SQL query
    string $className,     // Model class name
    string $cursorField,   // Field to use as cursor
    ?string $cursor = null, // Cursor value
    int $perPage = 20,     // Items per page
    array $params = [],    // Query parameters
    string $direction = 'next' // 'next' or 'previous'
): PaginationResult
```

### `executeWildcardSearchPaginated()`
Combines wildcard search with pagination.

```php
protected function executeWildcardSearchPaginated(
    string $sql,           // SQL query with :search placeholder
    string $searchTerm,    // Search term with wildcards
    string $className,     // Model class name
    int $page = 1,         // Page number
    int $perPage = 20,     // Items per page
    array $additionalParams = [], // Additional parameters
    string $orderBy = ''   // ORDER BY clause
): PaginationResult
```

### `buildCountQuery()`
Automatically builds count queries from base SQL queries.

```php
protected function buildCountQuery(string $sql): string
```

## Implementation in Your Repository

### Step 1: Add SQL Queries
Add pagination queries to your `sqls()` method:

```php
public function sqls($type): string
{
    $sqls = [
        // ... existing queries ...
        'getAllPaginated' => 'SELECT * FROM your_table',
        'countAll' => 'SELECT COUNT(*) FROM your_table',
        'searchPaginated' => 'SELECT * FROM your_table WHERE field LIKE :search',
        'countSearch' => 'SELECT COUNT(*) FROM your_table WHERE field LIKE :search',
    ];
    return $sqls[$type];
}
```

### Step 2: Add Pagination Methods

#### Basic Pagination
```php
public function getAllPaginated(int $page = 1, int $perPage = 20, string $orderBy = 'ORDER BY id ASC'): PaginationResult
{
    return $this->executePaginatedQuery(
        $this->sqls('getAllPaginated'),
        $this->sqls('countAll'),
        YourModel::class,
        $page,
        $perPage,
        [],
        $orderBy
    );
}
```

#### Search with Pagination
```php
public function searchPaginated(string $term, int $page = 1, int $perPage = 20): PaginationResult
{
    return $this->executeWildcardSearchPaginated(
        $this->sqls('searchPaginated'),
        $term,
        YourModel::class,
        $page,
        $perPage
    );
}
```

#### Cursor-based Pagination
```php
public function getCursorPaginated(?string $cursor = null, int $perPage = 20): PaginationResult
{
    return $this->executeCursorPaginatedQuery(
        $this->sqls('getAllPaginated'),
        $this->sqls('countAll'),
        YourModel::class,
        'id', // Use 'id' as cursor field
        $cursor,
        $perPage
    );
}
```

### Step 3: Advanced Search with Pagination
```php
public function advancedSearchPaginated(array $criteria, int $page = 1, int $perPage = 20): PaginationResult
{
    try {
        $conditions = [];
        $params = [];
        
        foreach ($criteria as $field => $value) {
            if (!empty($value)) {
                $condition = $this->buildSearchCondition($field, $value, 't');
                $conditions[] = $condition['sql'];
                $params = array_merge($params, $condition['params']);
            }
        }
        
        if (empty($conditions)) {
            return $this->getAllPaginated($page, $perPage);
        }
        
        $whereClause = implode(' AND ', $conditions);
        $sql = "SELECT * FROM your_table t WHERE " . $whereClause;
        $countSql = "SELECT COUNT(*) FROM your_table t WHERE " . $whereClause;
        
        return $this->executePaginatedQuery(
            $sql,
            $countSql,
            YourModel::class,
            $page,
            $perPage,
            $params
        );
        
    } catch (PDOException $e) {
        error_log("Error in advanced search paginated: " . $e->getMessage());
        return new PaginationResult(
            data: [],
            total: 0,
            page: $page,
            perPage: $perPage,
            totalPages: 0,
            hasNextPage: false,
            hasPreviousPage: false
        );
    }
}
```

## Usage Examples

### Basic Pagination
```php
// Get first page with 20 items
$result = $clubRepo->getAllClubsPaginated(page: 1, perPage: 20);

// Access data and pagination info
$clubs = $result->data;
$total = $result->total;
$hasNext = $result->hasNextPage;

// Get next page
if ($result->hasNextPage) {
    $nextPage = $clubRepo->getAllClubsPaginated(page: 2, perPage: 20);
}
```

### Search with Pagination
```php
// Search for clubs starting with "Stockholm"
$result = $clubRepo->searchClubsByTitlePaginated('Stockholm*', page: 1, perPage: 10);

// Access results
$clubs = $result->data;
$totalFound = $result->total;
```

### Cursor-based Pagination
```php
// Get first page
$result = $clubRepo->getClubsCursorPaginated(cursor: null, perPage: 20);

// Get next page using cursor
if ($result->nextCursor) {
    $nextPage = $clubRepo->getClubsCursorPaginated(
        cursor: $result->nextCursor, 
        perPage: 20
    );
}
```

### Advanced Search with Pagination
```php
$criteria = [
    'title' => '*Cykel*',
    'acp_kod' => '1*'
];

$result = $clubRepo->advancedSearchPaginated($criteria, page: 1, perPage: 15);
```

## API Response Format

The pagination methods return a `PaginationResult` object that can be converted to an array for API responses:

```php
$result = $clubRepo->getAllClubsPaginated(page: 1, perPage: 20);
$response = $result->toArray();

// Response structure:
[
    'data' => [
        ['id' => 1, 'name' => 'Club 1'],
        ['id' => 2, 'name' => 'Club 2'],
        // ... more items
    ],
    'pagination' => [
        'total' => 100,
        'page' => 1,
        'per_page' => 20,
        'total_pages' => 5,
        'has_next_page' => true,
        'has_previous_page' => false,
        'next_cursor' => null,        // Only for cursor pagination
        'previous_cursor' => null     // Only for cursor pagination
    ]
]
```

## Pagination Types Comparison

### Offset/Limit Pagination
**Pros:**
- Simple to understand and implement
- Allows jumping to specific pages
- Good for small to medium datasets
- Familiar to users

**Cons:**
- Can be slow for large datasets
- Inconsistent results if data changes between requests
- Memory intensive for large offsets

**Best for:** Small to medium datasets, user interfaces with page numbers

### Cursor-based Pagination
**Pros:**
- Better performance for large datasets
- Consistent results even if data changes
- Memory efficient
- Better for real-time data

**Cons:**
- More complex to implement
- No jumping to specific pages
- Requires unique, sortable cursor field

**Best for:** Large datasets, real-time data, mobile apps, APIs

## Performance Considerations

### Database Indexing
```sql
-- For offset pagination
CREATE INDEX idx_your_table_id ON your_table(id);

-- For cursor pagination
CREATE INDEX idx_your_table_cursor_field ON your_table(cursor_field, id);
```

### Query Optimization
- Use `LIMIT` clauses to prevent large result sets
- Implement proper indexing on pagination fields
- Consider caching for frequently accessed pages
- Use cursor-based pagination for datasets > 10,000 records

### Memory Management
- Limit `perPage` to reasonable values (max 100)
- Use cursor-based pagination for large datasets
- Consider streaming for very large result sets

## Error Handling

The pagination methods include built-in error handling:

- Returns empty result set on database errors
- Logs errors to `error_log`
- Validates pagination parameters
- Handles edge cases (empty results, invalid pages)

## Best Practices

1. **Choose the right pagination type**:
   - Use offset/limit for small datasets and user interfaces
   - Use cursor-based for large datasets and APIs

2. **Set reasonable limits**:
   - Limit `perPage` to 100 items maximum
   - Consider your database performance

3. **Use proper ordering**:
   - Always use consistent ordering for reliable pagination
   - Include a unique field in ORDER BY for cursor pagination

4. **Handle edge cases**:
   - Check for empty results
   - Validate page numbers
   - Handle invalid cursors

5. **Optimize queries**:
   - Use proper indexing
   - Consider query caching
   - Monitor query performance

## Example Implementation

See `api/src/Domain/Model/Club/ClubRepository.php` for a complete implementation example, including:

- Basic pagination methods
- Search with pagination
- Cursor-based pagination
- Advanced search with pagination
- SQL query definitions
- Error handling

## Testing

You can test the pagination functionality using the example file:

```bash
php api/examples/pagination_usage.php
```

This will demonstrate the various pagination patterns and usage examples. 