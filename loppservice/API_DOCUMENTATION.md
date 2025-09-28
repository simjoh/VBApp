# Event API Documentation

## Overview
The Event API provides endpoints to retrieve events, statistics, and information about events in the loppservice.

## Authentication
All endpoints require API key authentication using the `apikey` middleware.

## Endpoints

### 1. Get Events by Event Type
**GET** `/api/integration/event/by-type`

Returns events filtered by event type (MSR, BP, or BRM) with pagination and additional filtering options.

#### Query Parameters
- `event_type` (required): Event type to filter by. Must be one of: MSR, BP, BRM
- `per_page` (optional): Number of events per page (default: 15)
- `page` (optional): Page number (default: 1)
- `organizer_id` (optional): Filter by organizer ID
- `completed` (optional): Filter by completion status (true/false)
- `start_date` (optional): Filter events starting from this date (YYYY-MM-DD)
- `end_date` (optional): Filter events ending before this date (YYYY-MM-DD)
- `sort_by` (optional): Sort field (default: startdate)
- `sort_dir` (optional): Sort direction - asc or desc (default: asc)

#### Response
```json
{
  "event_type": "MSR",
  "total": 3,
  "per_page": 15,
  "current_page": 1,
  "last_page": 1,
  "data": [
    {
      "event_uid": "d32650ff-15f8-4df1-9845-d3dc252a7a84",
      "title": "Midnight Sun Randonnée 2024",
      "description": "Epic bike ride in the midnight sun",
      "startdate": "2024-06-16",
      "enddate": "2024-06-20",
      "completed": 1,
      "event_type": "MSR",
      "organizer_id": null,
      "county_id": 20,
      "event_group_uid": null,
      "eventconfiguration": {
        "max_registrations": 200,
        "registration_opens": "2023-10-01 00:00:00",
        "registration_closes": "2024-06-14 23:59:59",
        "use_stripe_payment": 0,
        "products": [...]
      },
      "route_detail": {
        "distance": 2024,
        "height_difference": 11000,
        "start_time": "08:00",
        "start_place": null,
        "track_link": null
      },
      "created_at": "2023-09-29T20:00:00.000000Z",
      "updated_at": "2025-03-07T22:00:00.000000Z"
    }
  ]
}
```

### 2. Get Event Statistics
**GET** `/api/integration/event/{eventUid}/stats`

Returns comprehensive statistics for a specific event.

#### Response
```json
{
  "event_uid": "d32650ff-15f8-4df1-9845-d3dc252a7a84",
  "event_title": "Midnight Sun Randonnée 2024",
  "total_registrations": 150,
  "confirmed_registrations": 125,
  "total_reservations": 25,
  "max_registrations": 200,
  "registration_percentage": 75.0,
  "optional_products": [
    {
      "product_id": 1007,
      "product_name": "GRAND jersey F/M",
      "count": 45,
      "percentage": 30.0
    },
    {
      "product_id": 1008,
      "product_name": "TOR 3.0 jersey F/M",
      "count": 32,
      "percentage": 21.3
    },
    {
      "product_id": 1013,
      "product_name": "Buffet Dinner",
      "count": 78,
      "percentage": 52.0
    }
  ],
  "registration_trends": {
    "last_7_days": 12,
    "last_30_days": 45
  }
}
```

### 2. Get Event Optional Products
**GET** `/api/integration/event/{eventUid}/optional-products`

Returns all optional products available for a specific event.

#### Response
```json
{
  "event_uid": "d32650ff-15f8-4df1-9845-d3dc252a7a84",
  "event_title": "Midnight Sun Randonnée 2024",
  "optional_products": [
    {
      "product_id": 1007,
      "product_name": "GRAND jersey F/M",
      "category_id": 1
    },
    {
      "product_id": 1008,
      "product_name": "TOR 3.0 jersey F/M",
      "category_id": 1
    },
    {
      "product_id": 1013,
      "product_name": "Buffet Dinner",
      "category_id": 2
    }
  ]
}
```

### 3. Get Event Registrations
**GET** `/api/integration/event/{eventUid}/registrations`

Returns detailed registration information for a specific event (including personal details and optional products).

#### Response
```json
{
  "event_uid": "d32650ff-15f8-4df1-9845-d3dc252a7a84",
  "event_title": "Midnight Sun Randonnée 2024",
  "registrations": [
    {
      "registration_uid": "abc123-def456-ghi789",
      "reservation": false,
      "created_at": "2024-01-15 10:30:00",
      "person": {
        "firstname": "John",
        "lastname": "Doe",
        "email": "john.doe@example.com"
      },
      "optional_products": [
        {
          "product_id": 1007,
          "product_name": "GRAND jersey F/M"
        },
        {
          "product_id": 1013,
          "product_name": "Buffet Dinner"
        }
      ]
    }
  ]
}
```

## Error Responses

### 404 Not Found
```json
{
  "message": "Event not found"
}
```

### 500 Internal Server Error
```json
{
  "message": "Error retrieving statistics"
}
```

## Usage Examples

### Get Events by Type
```bash
# Get all MSR events
curl -X GET \
  -H "apikey: testkey" \
  "http://localhost:8082/loppservice/api/integration/event/by-type?event_type=MSR"

# Get completed BRM events with pagination
curl -X GET \
  -H "apikey: testkey" \
  "http://localhost:8082/loppservice/api/integration/event/by-type?event_type=BRM&completed=true&per_page=5&page=1"

# Get BP events sorted by start date (descending)
curl -X GET \
  -H "apikey: testkey" \
  "http://localhost:8082/loppservice/api/integration/event/by-type?event_type=BP&sort_by=startdate&sort_dir=desc"
```

### Get Event Statistics
```bash
curl -X GET \
  -H "apikey: testkey" \
  "http://localhost:8082/loppservice/api/integration/event/d32650ff-15f8-4df1-9845-d3dc252a7a84/stats"
```

### Get Optional Products
```bash
curl -X GET \
  -H "apikey: testkey" \
  "http://localhost:8082/loppservice/api/integration/event/d32650ff-15f8-4df1-9845-d3dc252a7a84/optional-products"
```

### Get Registrations
```bash
curl -X GET \
  -H "apikey: testkey" \
  "http://localhost:8082/loppservice/api/integration/event/d32650ff-15f8-4df1-9845-d3dc252a7a84/registrations"
```

### Get Non-Participant Optionals

**Endpoint:** `GET /api/integration/non-participant-optionals`

**Description:** Get non-participant optionals (e.g., dinner tickets) for MSR events. Can filter by event_uid or date interval.

**Query Parameters:**
- `event_uid` (optional): Filter by specific event UID - returns ALL non-participant optionals for that event regardless of timing
- `start_date` (optional): Start date for date range filtering (YYYY-MM-DD)
- `end_date` (optional): End date for date range filtering (YYYY-MM-DD)

**Note:** Either `event_uid` or both `start_date` and `end_date` must be provided.

**Example Usage:**
```bash
# Get non-participant optionals for MSR 2025
curl -X GET -H "apikey: testkey" "http://localhost:8082/api/integration/non-participant-optionals?event_uid=a0197755-ea3e-4605-8fa1-1dd5c746f452"

# Get non-participant optionals for a date range
curl -X GET -H "apikey: testkey" "http://localhost:8082/api/integration/non-participant-optionals?start_date=2024-01-01&end_date=2024-12-31"
```

## Notes

- All endpoints are protected by the `apikey` middleware
- Statistics are calculated in real-time from the database
- The `/stats` endpoint is optimized for performance and should be used for dashboard/analytics purposes
- The `/registrations` endpoint provides detailed information but may be slower for large events
- Optional products are grouped by product ID and sorted by popularity (count) in descending order
- Non-participant optionals are returned for the entire event period, not limited by registration timing
