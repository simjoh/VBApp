<?php
/**
 * Example usage of wildcard search functionality in repositories
 * 
 * This file demonstrates how to use the wildcard search methods
 * that have been added to the BaseRepository class.
 */

require_once __DIR__ . '/../vendor/autoload.php';

use App\Domain\Model\Club\ClubRepository;
use App\common\Database\MysqlDatabase;

// Example 1: Basic wildcard search in ClubRepository
function exampleClubSearch() {
    // Note: In a real application, you would get the PDO connection from your container
    // $connection = $container->get(PDO::class);
    // $clubRepo = new ClubRepository($connection);
    
    echo "=== Club Search Examples ===\n";
    echo "Note: This is a demonstration of the API, not actual database queries\n";
    
    // Search examples with wildcards
    
    // Search for clubs starting with "Stockholm"
    // $stockholmClubs = $clubRepo->searchClubsByTitle("Stockholm*");
    echo "Clubs starting with 'Stockholm': Use searchClubsByTitle('Stockholm*')\n";
    
    // Search for clubs ending with "Cykelklubb"
    // $cykelklubbClubs = $clubRepo->searchClubsByTitle("*Cykelklubb");
    echo "Clubs ending with 'Cykelklubb': Use searchClubsByTitle('*Cykelklubb')\n";
    
    // Search for clubs containing "sport"
    // $sportClubs = $clubRepo->searchClubsByTitle("*sport*");
    echo "Clubs containing 'sport': Use searchClubsByTitle('*sport*')\n";
    
    // Exact search (no wildcards)
    // $exactClub = $clubRepo->searchClubByTitleSingle("Stockholm Cykelklubb");
    echo "Exact match: Use searchClubByTitleSingle('Stockholm Cykelklubb')\n";
    
    // Search by ACP code with wildcards
    // $acpClubs = $clubRepo->searchClubsByAcpCode("123*");
    echo "ACP code search: Use searchClubsByAcpCode('123*')\n";
    
    // Advanced search with multiple criteria
    $criteria = [
        'title' => '*Cykel*',
        'acp_kod' => '1*'
    ];
    // $advancedResults = $clubRepo->advancedSearch($criteria);
    echo "Advanced search: Use advancedSearch(['title' => '*Cykel*', 'acp_kod' => '1*'])\n";
}

// Example 2: How to implement wildcard search in your own repository
function exampleCustomRepository() {
    echo "\n=== Custom Repository Implementation ===\n";
    
    echo "To implement wildcard search in your own repository:\n";
    echo "1. Extend BaseRepository\n";
    echo "2. Add SQL queries to your sqls() method:\n";
    echo "   'searchByField' => 'SELECT * FROM your_table WHERE field LIKE :search'\n";
    echo "3. Add search methods:\n";
    echo "   public function searchByField(string \$value): array {\n";
    echo "       return \$this->executeWildcardSearch(\n";
    echo "           \$this->sqls('searchByField'),\n";
    echo "           \$value,\n";
    echo "           YourModel::class\n";
    echo "       );\n";
    echo "   }\n";
}

// Example 3: Wildcard patterns supported
function wildcardPatterns() {
    echo "\n=== Supported Wildcard Patterns ===\n";
    echo "*john*     - Contains 'john' anywhere\n";
    echo "john*      - Starts with 'john'\n";
    echo "*john      - Ends with 'john'\n";
    echo "john       - Exact match (no wildcards)\n";
    echo "*          - Matches everything\n";
    echo "j*n        - Starts with 'j' and ends with 'n'\n";
}

// Example 4: Error handling
function errorHandlingExample() {
    echo "\n=== Error Handling ===\n";
    echo "The wildcard search methods include built-in error handling:\n";
    echo "- Returns empty array on database errors\n";
    echo "- Logs errors to error_log\n";
    echo "- Handles SQL injection protection automatically\n";
    echo "- Escapes special LIKE characters (% and _)\n";
}

// Run examples
if (php_sapi_name() === 'cli') {
    exampleClubSearch();
    exampleCustomRepository();
    wildcardPatterns();
    errorHandlingExample();
} else {
    echo "<h2>Wildcard Search Usage Examples</h2>";
    echo "<pre>";
    exampleClubSearch();
    exampleCustomRepository();
    wildcardPatterns();
    errorHandlingExample();
    echo "</pre>";
} 