#!/bin/bash

# Example curl call for addparticipant endpoint
# Replace the environment variables and course_uid with actual values

BREVET_APP_URL="https://your-brevet-app-url.com"
BREVET_APP_API_KEY="your-api-key-here"
LOPPSERVICE_USER_AGENT="YourLoppservice/1.0"
COURSE_UID="550e8400-e29b-41d4-a716-446655440004"

curl -X POST "${BREVET_APP_URL}/participant/addparticipant/track/${COURSE_UID}" \
  -H "Content-Type: application/json" \
  -H "APIKEY: ${BREVET_APP_API_KEY}" \
  -H "User-Agent: ${LOPPSERVICE_USER_AGENT}" \
  -d '{
    "participant": {
      "person_uid": "550e8400-e29b-41d4-a716-446655440000",
      "firstname": "John",
      "surname": "Doe",
      "birthdate": "1985-03-15",
      "registration_registration_uid": "550e8400-e29b-41d4-a716-446655440001",
      "created_at": "2024-01-15T10:30:00.000000Z",
      "updated_at": "2024-01-15T10:30:00.000000Z",
      "adress": {
        "adress_uid": "550e8400-e29b-41d4-a716-446655440002",
        "adress": "123 Main Street",
        "person_person_uid": "550e8400-e29b-41d4-a716-446655440000",
        "postal_code": "12345",
        "city": "Stockholm",
        "country_id": 1,
        "created_at": "2024-01-15T10:30:00.000000Z",
        "updated_at": "2024-01-15T10:30:00.000000Z"
      },
      "contactinformation": {
        "contactinformation_uid": "550e8400-e29b-41d4-a716-446655440003",
        "tel": "+46701234567",
        "email": "john.doe@example.com",
        "person_person_uid": "550e8400-e29b-41d4-a716-446655440000",
        "created_at": "2024-01-15T10:30:00.000000Z",
        "updated_at": "2024-01-15T10:30:00.000000Z"
      },
      "registration": [
        {
          "registration_uid": "550e8400-e29b-41d4-a716-446655440001",
          "course_uid": "550e8400-e29b-41d4-a716-446655440004",
          "additional_information": "Dietary restrictions: vegetarian",
          "reservation": false,
          "reservation_valid_until": null,
          "startnumber": 142,
          "club_uid": "550e8400-e29b-41d4-a716-446655440005",
          "created_at": "2024-01-15T10:30:00.000000Z",
          "updated_at": "2024-01-15T10:30:00.000000Z"
        }
      ]
    },
    "registration": {
      "registration_uid": "550e8400-e29b-41d4-a716-446655440001",
      "course_uid": "550e8400-e29b-41d4-a716-446655440004",
      "additional_information": "Dietary restrictions: vegetarian",
      "reservation": false,
      "reservation_valid_until": null,
      "startnumber": 142,
      "club_uid": "550e8400-e29b-41d4-a716-446655440005",
      "created_at": "2024-01-15T10:30:00.000000Z",
      "updated_at": "2024-01-15T10:30:00.000000Z"
    },
    "event_uid": "550e8400-e29b-41d4-a716-446655440006",
    "club": {
      "name": "Stockholm Cycling Club"
    },
    "response_uid": "550e8400-e29b-41d4-a716-446655440007",
    "medal": true
  }' 