# Standalone Tests for VBApp API

This directory contains standalone test scripts for testing various components of the VBApp API without requiring a full test framework setup.

## Available Tests

### 1. `test_gpx_parse.php`
**Purpose:** Basic GPX file parsing and validation test.

**What it tests:**
- GPX file parsing using `GpxService::parseGpxFile()`
- GPX validation using `GpxService::validateGpx()`
- Basic track information extraction (name, track points, waypoints)
- Full GPX XML structure output

**Usage:**
```bash
php api/standalone/tests/test_gpx_parse.php
```

**Requirements:**
- Requires `Testwaypoint(1).gpx` file in `api/uploads/` directory

---

### 2. `test_acp_brevet_calculator.php`
**Purpose:** Test ACP Brevet control time calculations.

**What it tests:**
- ACP Brevet calculator for different control distances (0, 50, 200, 400, 600 km)
- Opening and closing time calculations
- Special rule verification for 0 km control (should close 1 hour after start)
- Time formatting and validation

**Usage:**
```bash
php api/standalone/tests/test_acp_brevet_calculator.php
```

**Expected Output:**
- Opening and closing times for each control
- Verification that 0 km control closes exactly 1 hour after start time
- Comparison with expected ACP rules

---

### 3. `test_synthetic_brevet.php`
**Purpose:** Test GPX-based brevet calculations with synthetic data.

**What it tests:**
- GPX parsing with synthetic brevet test data
- ACP Brevet time calculations for each checkpoint
- Distance calculations along track
- Open/close times for controls at various distances

**Usage:**
```bash
php api/standalone/tests/test_synthetic_brevet.php
```

**Requirements:**
- Requires `syntethic_brevet_test.gpx` file in the same directory

**Expected Output:**
- Checkpoint information with calculated times
- Verification of ACP rules compliance
- Distance-based time calculations

---

### 4. `test_site_matching.php`
**Purpose:** Test site matching functionality for waypoints.

**What it tests:**
- Distance calculation between waypoints and existing sites
- Site matching within distance thresholds
- Handling of unmatched waypoints
- Different threshold sensitivity testing

**Usage:**
```bash
php api/standalone/tests/test_site_matching.php
```

**Expected Output:**
- Waypoint to site matching results
- Distance calculations using Haversine formula
- Threshold sensitivity analysis
- Recommendations for integration

---

### 5. `test_gpx_with_site_matching.php`
**Purpose:** Comprehensive test combining GPX parsing with site matching.

**What it tests:**
- GPX parsing with waypoint extraction
- Site matching for each waypoint
- ACP Brevet time calculations
- Integration of existing sites vs. new site creation
- Complete workflow from GPX to checkpoints

**Usage:**
```bash
php api/standalone/tests/test_gpx_with_site_matching.php
```

**Expected Output:**
- Complete checkpoint processing with site matching
- Summary of matched vs. new sites needed
- Full integration demonstration

---

### 6. `test_integrated_site_matching.php` ⭐ **NEW**
**Purpose:** Test the production-ready integrated site matching system.

**What it tests:**
- `SiteMatchingService` with proper dependency injection
- Enhanced `GpxService` with site matching capability
- Complete integration with mock database
- Service configuration and threshold management
- Detailed matching information and fallback handling

**Usage:**
```bash
php api/standalone/tests/test_integrated_site_matching.php
```

**Expected Output:**
- Direct site matching service functionality
- GPX processing with automatic site matching
- Detailed matching information for each checkpoint
- Service configuration and threshold testing
- Complete integration verification

---

### 7. `test_refactored_gpx_track_creation.php` ⭐ **REFACTORED**
**Purpose:** Test the refactored GPX track creation functionality.

**What it tests:**
- Data transformation logic moved from Action to Service
- Validation logic for required GPX data
- Business logic separation and proper error handling
- Consistency with established system patterns

**Usage:**
```bash
php api/standalone/tests/test_refactored_gpx_track_creation.php
```

**Expected Output:**
- Validation testing for required data
- Data transformation verification
- Business logic testing
- Refactoring compliance verification

---

## Production Implementation

### ✅ **GPX Track Creation Refactored**

The `createTrackFromGpx` functionality has been refactored to follow proper separation of concerns:

#### **Before Refactoring:**
- Business logic mixed with HTTP concerns in Action method
- Data transformation, validation, and service orchestration in one place
- Inconsistent with other action methods in the system

#### **After Refactoring:**
- **TrackService::createTrackFromGpxData()** - Handles all business logic
- **TrackAction::createTrackFromGpx()** - Handles only HTTP concerns
- Proper error handling with BrevetException
- Consistent with established patterns

#### **Benefits:**
- ✅ **Separation of Concerns:** Business logic separated from HTTP handling
- ✅ **Testability:** Service method can be unit tested independently
- ✅ **Reusability:** Service method can be used by other parts of the system
- ✅ **Consistency:** Follows same pattern as other action methods
- ✅ **Maintainability:** Clear boundaries and responsibilities

### ✅ **Site Matching System Implemented**

The site matching functionality has been fully implemented with proper separation of concerns:

#### **1. SiteMatchingService** (`api/src/Domain/Model/Site/Service/SiteMatchingService.php`)
- **Responsibility:** Handle all site matching logic
- **Features:**
  - Find nearest site within distance threshold
  - Find all sites within radius
  - Match waypoints to existing sites
  - Create new sites from waypoints
  - Configurable match thresholds
  - Haversine distance calculations

#### **2. Enhanced GpxService** (`api/src/common/Gpx/GpxService.php`)
- **Responsibility:** GPX processing with optional site matching
- **Features:**
  - Backward compatible (works without SiteMatchingService)
  - Automatic site matching when service is available
  - Detailed matching information
  - Configurable match thresholds per call

#### **3. Dependency Injection** (`api/config/container.php`)
- **Responsibility:** Wire services together
- **Configuration:**
  - SiteMatchingService with default 1.0km threshold
  - GpxService automatically receives SiteMatchingService
  - All services properly injected

### **Usage in Production**

```php
// Automatic site matching (via container)
$gpxService = $container->get(\App\common\Gpx\GpxService::class);
$checkpoints = $gpxService->gpxToCheckpoints($gpx, $startDateTime, $totalDistance, 1.0);

// Direct site matching service usage
$siteMatchingService = $container->get(\App\Domain\Model\Site\Service\SiteMatchingService::class);
$matchResult = $siteMatchingService->matchWaypointToSite($lat, $lon, $name, $description);
```

## Test Data

### Demo Sites
The tests use demo site data representing:
- Stockholm Central Station (59.3293, 18.0686)
- Göteborg Central Station (57.7089, 11.9746)
- Malmö Central Station (55.6095, 13.0038)

### Test Waypoints
Various waypoint configurations are tested:
- Exact matches to existing sites
- Near matches within threshold
- New locations requiring site creation

## Key Features Tested

### 1. GPX Processing
- ✅ File parsing and validation
- ✅ Waypoint extraction
- ✅ Track point processing
- ✅ Distance calculations

### 2. ACP Brevet Calculations
- ✅ Opening time calculations
- ✅ Closing time calculations
- ✅ Special rules (0 km control)
- ✅ Distance-based time limits

### 3. Site Matching
- ✅ Distance-based matching
- ✅ Threshold sensitivity
- ✅ Existing site reuse
- ✅ New site creation handling

### 4. Integration
- ✅ Complete workflow testing
- ✅ Error handling
- ✅ Data consistency
- ✅ Dependency injection
- ✅ Separation of concerns

### 5. Production Readiness
- ✅ Service architecture
- ✅ Configuration management
- ✅ Fallback support
- ✅ Mock testing capabilities

## Architecture Benefits

### **Separation of Concerns**
- **SiteMatchingService:** Handles only site matching logic
- **GpxService:** Handles only GPX processing
- **Container:** Handles dependency injection
- **Repository:** Handles data access

### **Testability**
- Services can be tested independently
- Mock repositories for testing
- Configurable thresholds
- Clear interfaces

### **Maintainability**
- Single responsibility principle
- Dependency injection
- Clear service boundaries
- Easy to extend and modify

### **Production Ready**
- Proper error handling
- Configuration management
- Backward compatibility
- Performance considerations

## Running All Tests

To run all tests in sequence:
```bash
cd api/standalone/tests/
php test_gpx_parse.php
php test_acp_brevet_calculator.php
php test_synthetic_brevet.php
php test_site_matching.php
php test_gpx_with_site_matching.php
php test_integrated_site_matching.php
php test_refactored_gpx_track_creation.php
```

## Notes

- These tests are standalone and don't require PHPUnit
- They use demo data to avoid database dependencies
- All tests include detailed output and explanations
- Tests can be easily modified for different scenarios
- **Production implementation is complete and ready for use**
- Site matching system follows SOLID principles
- Dependency injection ensures proper service wiring
- Backward compatibility maintained throughout 