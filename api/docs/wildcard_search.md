# Wildcard Search Functionality

This document describes the wildcard search functionality that has been added to the `BaseRepository` class, allowing you to implement flexible search capabilities across all your repositories.

## Overview

The wildcard search functionality provides a common implementation for searching database records using wildcard patterns. It supports the `*` character as a wildcard and automatically handles SQL injection protection and error handling.

## Features

- **Wildcard Support**: Use `*` to match any sequence of characters
- **SQL Injection Protection**: Automatic escaping of special characters
- **Error Handling**: Built-in error handling with logging
- **Flexible API**: Support for both single and multiple result searches
- **Dynamic Queries**: Automatic switching between exact and wildcard matching

## BaseRepository Methods

### `wildcardToLikePattern(string $pattern): string`
Converts wildcard patterns to SQL LIKE patterns.

```php
// Examples:
"john*" → "john%"
"*doe" → "%doe"
"*smith*" → "%smith%"
"exact" → "exact" (no wildcards)
```

### `executeWildcardSearch(string $sql, string $searchTerm, string $className, array $additionalParams = []): array`
Executes a wildcard search query and returns an array of objects.

### `executeWildcardSearchSingle(string $sql, string $searchTerm, string $className, array $additionalParams = []): ?object`
Executes a wildcard search query and returns a single object or null.

### `hasWildcards(string $searchTerm): bool`
Checks if a search term contains wildcard characters.

### `buildSearchCondition(string $column, string $searchTerm, string $tableAlias = ''): array`
Builds dynamic search conditions based on whether wildcards are present.

## Implementation in Your Repository

### Step 1: Add SQL Queries
Add search queries to your `sqls()` method:

```php
public function sqls($type): string
{
    $sqls = [
        // ... existing queries ...
        'searchByField' => 'SELECT * FROM your_table WHERE field LIKE :search',
        'searchByMultipleFields' => 'SELECT * FROM your_table WHERE field1 LIKE :search OR field2 LIKE :search',
    ];
    return $sqls[$type];
}
```

### Step 2: Add Search Methods
Implement search methods in your repository:

```php
public function searchByField(string $value): array
{
    return $this->executeWildcardSearch(
        $this->sqls('searchByField'),
        $value,
        YourModel::class
    );
}

public function searchByFieldSingle(string $value): ?YourModel
{
    return $this->executeWildcardSearchSingle(
        $this->sqls('searchByField'),
        $value,
        YourModel::class
    );
}
```

### Step 3: Advanced Search (Optional)
For complex searches with multiple criteria:

```php
public function advancedSearch(array $criteria): array
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
            return $this->getAll() ?? [];
        }
        
        $whereClause = implode(' AND ', $conditions);
        $sql = "SELECT * FROM your_table t WHERE " . $whereClause;
        
        $statement = $this->connection->prepare($sql);
        
        foreach ($params as $key => $value) {
            $statement->bindParam($key, $value);
        }
        
        $statement->execute();
        
        return $statement->fetchAll(PDO::FETCH_CLASS | PDO::FETCH_PROPS_LATE, YourModel::class, []);
        
    } catch (PDOException $e) {
        error_log("Error in advanced search: " . $e->getMessage());
        return [];
    }
}
```

## Usage Examples

### Basic Wildcard Searches

```php
// Search for clubs starting with "Stockholm"
$clubs = $clubRepo->searchClubsByTitle("Stockholm*");

// Search for clubs ending with "Cykelklubb"
$clubs = $clubRepo->searchClubsByTitle("*Cykelklubb");

// Search for clubs containing "sport"
$clubs = $clubRepo->searchClubsByTitle("*sport*");

// Exact search (no wildcards)
$club = $clubRepo->searchClubByTitleSingle("Stockholm Cykelklubb");
```

### Advanced Search

```php
$criteria = [
    'title' => '*Cykel*',
    'acp_kod' => '1*'
];
$results = $clubRepo->advancedSearch($criteria);
```

### Supported Wildcard Patterns

| Pattern | Description | SQL Equivalent |
|---------|-------------|----------------|
| `*john*` | Contains "john" anywhere | `%john%` |
| `john*` | Starts with "john" | `john%` |
| `*john` | Ends with "john" | `%john` |
| `john` | Exact match | `= 'john'` |
| `*` | Matches everything | `%` |
| `j*n` | Starts with "j" and ends with "n" | `j%n` |

## Error Handling

The wildcard search methods include built-in error handling:

- Returns empty array on database errors
- Logs errors to `error_log`
- Handles SQL injection protection automatically
- Escapes special LIKE characters (`%` and `_`)

## Security Features

- **SQL Injection Protection**: All user input is properly escaped
- **Parameter Binding**: Uses PDO prepared statements
- **Character Escaping**: Special SQL characters are escaped
- **Input Validation**: Handles null and empty values gracefully

## Performance Considerations

- Wildcard searches using `LIKE` can be slower than exact matches
- Consider adding database indexes on frequently searched columns
- For large datasets, consider implementing pagination
- Use exact matches when possible for better performance

## Example Implementation

See `api/src/Domain/Model/Club/ClubRepository.php` for a complete implementation example, including:

- Basic wildcard search methods
- Advanced search with multiple criteria
- SQL query definitions
- Error handling

## Testing

You can test the wildcard search functionality using the example file:

```bash
php api/examples/wildcard_search_usage.php
```

This will demonstrate the various search patterns and usage examples. 