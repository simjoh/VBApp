#!/bin/bash

# Base URL - change this according to your environment
BASE_URL="http://localhost/api/integration/event-group"

echo "Testing EventGroup API endpoints..."
echo "==================================="

# Create a new event group
echo "1. Creating new event group..."
CREATE_RESPONSE=$(curl -s -X POST "${BASE_URL}" \
-H 'Content-Type: application/json' \
-d '{
    "name": "Summer Events 2024",
    "description": "Collection of summer cycling events",
    "event_uids": ["event-uid-1", "event-uid-2"]
}')
echo "Response: ${CREATE_RESPONSE}"
echo

# Extract the UID from the creation response (assuming it's in the data.uid field)
EVENT_GROUP_UID=$(echo ${CREATE_RESPONSE} | grep -o '"uid":"[^"]*' | cut -d'"' -f4)

if [ -n "$EVENT_GROUP_UID" ]; then
    echo "Created event group with UID: ${EVENT_GROUP_UID}"
    
    # Get all event groups
    echo "2. Getting all event groups..."
    curl -s -X GET "${BASE_URL}/all" \
    -H 'Content-Type: application/json'
    echo
    
    # Get specific event group
    echo "3. Getting specific event group..."
    curl -s -X GET "${BASE_URL}/${EVENT_GROUP_UID}" \
    -H 'Content-Type: application/json'
    echo
    
    # Update event group
    echo "4. Updating event group..."
    curl -s -X PUT "${BASE_URL}" \
    -H 'Content-Type: application/json' \
    -d "{
        \"uid\": \"${EVENT_GROUP_UID}\",
        \"name\": \"Updated Summer Events 2024\",
        \"description\": \"Updated collection of summer cycling events\",
        \"event_uids\": [\"event-uid-1\", \"event-uid-3\"]
    }"
    echo
    
    # Delete event group
    echo "5. Deleting event group..."
    curl -s -X DELETE "${BASE_URL}/${EVENT_GROUP_UID}" \
    -H 'Content-Type: application/json'
    echo
else
    echo "Failed to get event group UID from creation response"
fi

echo "==================================="
echo "API testing completed" 