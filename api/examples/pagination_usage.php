<?php
/**
 * Example usage of pagination functionality in repositories
 * 
 * This file demonstrates how to use the pagination methods
 * that have been added to the BaseRepository class.
 */

require_once __DIR__ . '/../vendor/autoload.php';

use App\common\Repository\PaginationResult;

// Example 1: Basic pagination usage
function exampleBasicPagination() {
    echo "=== Basic Pagination Examples ===\n";
    echo "Note: This is a demonstration of the API, not actual database queries\n";
    
    // Example of how to use pagination in a repository
    echo "1. Get all clubs with pagination:\n";
    echo "   \$result = \$clubRepo->getAllClubsPaginated(page: 1, perPage: 20);\n";
    echo "   \$clubs = \$result->data;\n";
    echo "   \$total = \$result->total;\n";
    echo "   \$hasNext = \$result->hasNextPage;\n\n";
    
    echo "2. Search clubs with pagination:\n";
    echo "   \$result = \$clubRepo->searchClubsByTitlePaginated('Stockholm*', page: 1, perPage: 10);\n";
    echo "   \$clubs = \$result->data;\n\n";
    
    echo "3. Advanced search with pagination:\n";
    echo "   \$criteria = ['title' => '*Cykel*', 'acp_kod' => '1*'];\n";
    echo "   \$result = \$clubRepo->advancedSearchPaginated(\$criteria, page: 1, perPage: 15);\n\n";
    
    echo "4. Cursor-based pagination (for large datasets):\n";
    echo "   \$result = \$clubRepo->getClubsCursorPaginated(cursor: null, perPage: 20);\n";
    echo "   \$nextCursor = \$result->nextCursor;\n";
    echo "   \$result2 = \$clubRepo->getClubsCursorPaginated(cursor: \$nextCursor, perPage: 20);\n\n";
}

// Example 2: PaginationResult object structure
function examplePaginationResultStructure() {
    echo "=== PaginationResult Structure ===\n";
    
    echo "The PaginationResult object contains:\n";
    echo "- data: array of objects\n";
    echo "- total: total number of records\n";
    echo "- page: current page number (1-based)\n";
    echo "- perPage: items per page\n";
    echo "- totalPages: total number of pages\n";
    echo "- hasNextPage: boolean indicating if there's a next page\n";
    echo "- hasPreviousPage: boolean indicating if there's a previous page\n";
    echo "- nextCursor: cursor for next page (cursor-based pagination)\n";
    echo "- previousCursor: cursor for previous page (cursor-based pagination)\n\n";
    
    echo "Converting to array:\n";
    echo "\$array = \$result->toArray();\n";
    echo "// Returns: ['data' => [...], 'pagination' => [...]]\n\n";
}

// Example 3: Implementation in your own repository
function exampleCustomRepositoryImplementation() {
    echo "=== Custom Repository Implementation ===\n";
    
    echo "To implement pagination in your own repository:\n\n";
    
    echo "1. Add SQL queries to your sqls() method:\n";
    echo "   'getAllPaginated' => 'SELECT * FROM your_table'\n";
    echo "   'countAll' => 'SELECT COUNT(*) FROM your_table'\n\n";
    
    echo "2. Add pagination methods:\n";
    echo "   public function getAllPaginated(int \$page = 1, int \$perPage = 20): PaginationResult {\n";
    echo "       return \$this->executePaginatedQuery(\n";
    echo "           \$this->sqls('getAllPaginated'),\n";
    echo "           \$this->sqls('countAll'),\n";
    echo "           YourModel::class,\n";
    echo "           \$page,\n";
    echo "           \$perPage\n";
    echo "       );\n";
    echo "   }\n\n";
    
    echo "3. For wildcard search with pagination:\n";
    echo "   public function searchPaginated(string \$term, int \$page = 1, int \$perPage = 20): PaginationResult {\n";
    echo "       return \$this->executeWildcardSearchPaginated(\n";
    echo "           \$this->sqls('searchByField'),\n";
    echo "           \$term,\n";
    echo "           YourModel::class,\n";
    echo "           \$page,\n";
    echo "           \$perPage\n";
    echo "       );\n";
    echo "   }\n\n";
}

// Example 4: Pagination types
function examplePaginationTypes() {
    echo "=== Pagination Types ===\n";
    
    echo "1. Offset/Limit Pagination (Traditional):\n";
    echo "   - Uses page numbers (1, 2, 3, ...)\n";
    echo "   - Good for small to medium datasets\n";
    echo "   - Allows jumping to specific pages\n";
    echo "   - Can be inefficient for large datasets\n\n";
    
    echo "2. Cursor-based Pagination:\n";
    echo "   - Uses cursors (unique identifiers)\n";
    echo "   - Better performance for large datasets\n";
    echo "   - No skipping pages\n";
    echo "   - More complex to implement\n\n";
    
    echo "3. Keyset Pagination:\n";
    echo "   - Similar to cursor-based\n";
    echo "   - Uses multiple columns as cursor\n";
    echo "   - Good for complex sorting\n\n";
}

// Example 5: Best practices
function exampleBestPractices() {
    echo "=== Best Practices ===\n";
    
    echo "1. Limit perPage to reasonable values (max 100)\n";
    echo "2. Use cursor-based pagination for large datasets\n";
    echo "3. Always include total count for offset pagination\n";
    echo "4. Use proper indexing on pagination fields\n";
    echo "5. Consider caching for frequently accessed pages\n";
    echo "6. Handle edge cases (empty results, invalid pages)\n";
    echo "7. Use consistent ordering for reliable pagination\n\n";
}

// Example 6: API response format
function exampleApiResponseFormat() {
    echo "=== API Response Format ===\n";
    
    echo "Typical API response structure:\n";
    echo "{\n";
    echo "  \"data\": [\n";
    echo "    {\"id\": 1, \"name\": \"Club 1\"},\n";
    echo "    {\"id\": 2, \"name\": \"Club 2\"}\n";
    echo "  ],\n";
    echo "  \"pagination\": {\n";
    echo "    \"total\": 100,\n";
    echo "    \"page\": 1,\n";
    echo "    \"per_page\": 20,\n";
    echo "    \"total_pages\": 5,\n";
    echo "    \"has_next_page\": true,\n";
    echo "    \"has_previous_page\": false,\n";
    echo "    \"next_cursor\": \"abc123\",\n";
    echo "    \"previous_cursor\": null\n";
    echo "  }\n";
    echo "}\n\n";
}

// Run examples
if (php_sapi_name() === 'cli') {
    exampleBasicPagination();
    examplePaginationResultStructure();
    exampleCustomRepositoryImplementation();
    examplePaginationTypes();
    exampleBestPractices();
    exampleApiResponseFormat();
} else {
    echo "<h2>Pagination Usage Examples</h2>";
    echo "<pre>";
    exampleBasicPagination();
    examplePaginationResultStructure();
    exampleCustomRepositoryImplementation();
    examplePaginationTypes();
    exampleBestPractices();
    exampleApiResponseFormat();
    echo "</pre>";
} 