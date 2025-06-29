# Database Cleanup and Comparison Tools

This directory contains scripts for cleaning up and comparing data between VBApp and Loppservice databases.

## Prerequisites

- PHP 7.4 or higher
- PDO MySQL extension enabled
- Database access to both VBApp and Loppservice databases

## Configuration

Both scripts use the following database connection settings. Update these in each script before running:

```php
$host = '192.168.1.221';           // Database host
$vbapp_port = '3310';              // VBApp database port
$loppservice_port = '3309';        // Loppservice database port
$vbapp_dbname = 'vasterbottenbrevet_se';         // VBApp database name
$loppservice_dbname = 'vasterbottenbrevet_se_db_2'; // Loppservice database name
$username = 'root';                // Database username
$password = 'secret';              // Database password
```

## Running the Scripts

### Step 1: Backup Your Databases

Before running any cleanup scripts, make sure to backup both databases:

```bash
# For VBApp database
mysqldump -h [host] -P [port] -u [username] -p [database_name] > vbapp_backup.sql

# For Loppservice database
mysqldump -h [host] -P [port] -u [username] -p [database_name] > loppservice_backup.sql
```

### Step 2: Run Database Cleanup

The cleanup script removes people without registrations from the Loppservice database and their associated records:

```bash
php cleanup_database.php
```

This script will:
- Find people without any registrations
- Remove these people and their associated records (addresses and contact information)
- Report the number of records cleaned up

### Step 3: Verify Results

Run the comparison script to check the state of both databases:

```bash
php compare_uids.php
```

This script will show:
- Total number of people in each database
- Number of matching records
- Number of missing/extra records
- Registration analysis

## Expected Output

### Cleanup Script Output
```
Found X people without registrations
Deleted X people and their associated records
Database cleanup completed successfully
```

### Comparison Script Output
```
┌─────────────────────────────────────────────────────────────┐
│                      COMPARISON SUMMARY                      │
├───────────────────────────────────┬─────────────────────────┤
│ Total People in Loppservice      │ XXX                   │
│ Total Competitors in VBApp       │ XXX                   │
│ Matching UIDs                    │ XXX                   │
│ Missing from VBApp               │ XXX                   │
│ Extra in VBApp                   │ XXX                   │
└───────────────────────────────────┴─────────────────────────┘
```

## Troubleshooting

1. If you get connection errors:
   - Verify database host and port settings
   - Check that the database user has proper permissions
   - Ensure the databases exist and are accessible

2. If no records are found:
   - Verify database names are correct
   - Check table names match your schema
   - Ensure the queries are using the correct column names

3. If you get PHP errors:
   - Verify PHP version is compatible
   - Check that PDO MySQL extension is installed
   - Ensure all required files are in place

## Safety Notes

- Always backup your databases before running cleanup scripts
- Test the scripts on a development environment first
- Verify the results after each step
- Keep the backup until you're sure everything is working correctly 