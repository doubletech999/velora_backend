# Velora API Documentation

## Base URL
```
http://localhost:8000/api
```

## Authentication
All API endpoints (except registration and login) require authentication using Bearer tokens.

### Headers
```
Authorization: Bearer {token}
Content-Type: application/json
```

## Authentication Endpoints

### Register User
**POST** `/register`

**Request Body:**
```json
{
    "name": "John Doe",
    "email": "john@example.com",
    "password": "password123",
    "password_confirmation": "password123",
    "role": "user", // optional: user, guide
    "language": "en" // optional: en, ar
}
```

**Response:**
```json
{
    "success": true,
    "message": "User registered successfully",
    "user": {
        "id": 1,
        "name": "John Doe",
        "email": "john@example.com",
        "role": "user",
        "language": "en"
    },
    "token": "1|vEgOCQa1hp78USeJoTeD0zomNQbDShJRkn5jiek75f730050"
}
```

### Login User
**POST** `/login`

**Request Body:**
```json
{
    "email": "john@example.com",
    "password": "password123"
}
```

**Response:**
```json
{
    "success": true,
    "message": "Login successful",
    "user": {
        "id": 1,
        "name": "John Doe",
        "email": "john@example.com",
        "role": "user",
        "language": "en"
    },
    "token": "2|0wdJbVtzmet1IcM9HGSR1MG1xltBiJgVrbakdWBQf2dc141c"
}
```

### Logout User
**POST** `/logout`

**Response:**
```json
{
    "success": true,
    "message": "Logged out successfully"
}
```

### Get Current User
**GET** `/user`

**Response:**
```json
{
    "success": true,
    "user": {
        "id": 1,
        "name": "John Doe",
        "email": "john@example.com",
        "role": "user",
        "language": "en"
    }
}
```

## Sites Endpoints

### Get All Sites
**GET** `/sites`

**Query Parameters:**
- `type` (optional): Filter by site type (historical, natural, cultural)
- `search` (optional): Search by site name
- `page` (optional): Page number for pagination

**Response:**
```json
{
    "success": true,
    "data": {
        "current_page": 1,
        "data": [
            {
                "id": 1,
                "name": "Church of the Nativity",
                "description": "The Church of the Nativity in Bethlehem...",
                "latitude": "31.70400000",
                "longitude": "35.20660000",
                "type": "historical",
                "image_url": "https://example.com/church-nativity.jpg",
                "created_at": "2025-08-28T13:52:52.000000Z",
                "updated_at": "2025-08-28T13:52:52.000000Z"
            }
        ],
        "total": 6,
        "per_page": 10
    }
}
```

### Get Single Site
**GET** `/sites/{id}`

**Response:**
```json
{
    "success": true,
    "data": {
        "id": 1,
        "name": "Church of the Nativity",
        "description": "The Church of the Nativity in Bethlehem...",
        "latitude": "31.70400000",
        "longitude": "35.20660000",
        "type": "historical",
        "image_url": "https://example.com/church-nativity.jpg",
        "created_at": "2025-08-28T13:52:52.000000Z",
        "updated_at": "2025-08-28T13:52:52.000000Z"
    }
}
```

### Create Site
**POST** `/sites`

**Request Body:**
```json
{
    "name": "New Tourist Site",
    "description": "Description of the site",
    "latitude": 31.5,
    "longitude": 35.2,
    "type": "cultural",
    "image_url": "https://example.com/image.jpg"
}
```

### Update Site
**PUT** `/sites/{id}`

**Request Body:** (same as create, all fields optional)

### Delete Site
**DELETE** `/sites/{id}`

## Guides Endpoints

### Get All Guides
**GET** `/guides`

### Get Single Guide
**GET** `/guides/{id}`

### Create Guide
**POST** `/guides`

### Update Guide
**PUT** `/guides/{id}`

### Delete Guide
**DELETE** `/guides/{id}`

## Trips Endpoints

### Get All Trips
**GET** `/trips`

### Get Single Trip
**GET** `/trips/{id}`

### Create Trip
**POST** `/trips`

### Update Trip
**PUT** `/trips/{id}`

### Delete Trip
**DELETE** `/trips/{id}`

## Reviews Endpoints

### Get All Reviews
**GET** `/reviews`

### Get Single Review
**GET** `/reviews/{id}`

### Create Review
**POST** `/reviews`

### Update Review
**PUT** `/reviews/{id}`

### Delete Review
**DELETE** `/reviews/{id}`

## Bookings Endpoints

### Get All Bookings
**GET** `/bookings`

### Get Single Booking
**GET** `/bookings/{id}`

### Create Booking
**POST** `/bookings`

### Update Booking
**PUT** `/bookings/{id}`

### Delete Booking
**DELETE** `/bookings/{id}`

## Error Responses

### Validation Error (422)
```json
{
    "success": false,
    "message": "Validation errors",
    "errors": {
        "email": ["The email field is required."]
    }
}
```

### Unauthorized (401)
```json
{
    "success": false,
    "message": "Invalid credentials"
}
```

### Not Found (404)
```json
{
    "success": false,
    "message": "Site not found"
}
```

## Testing Examples

### Test Registration
```bash
curl -X POST http://localhost:8000/api/register \
  -H "Content-Type: application/json" \
  -d '{
    "name": "Test User",
    "email": "test@example.com",
    "password": "password123",
    "password_confirmation": "password123",
    "role": "user",
    "language": "en"
  }'
```

### Test Login
```bash
curl -X POST http://localhost:8000/api/login \
  -H "Content-Type: application/json" \
  -d '{
    "email": "test@example.com",
    "password": "password123"
  }'
```

### Test Get Sites
```bash
curl -X GET http://localhost:8000/api/sites \
  -H "Authorization: Bearer YOUR_TOKEN_HERE"
```

### Test Filter Sites by Type
```bash
curl -X GET "http://localhost:8000/api/sites?type=historical" \
  -H "Authorization: Bearer YOUR_TOKEN_HERE"
```

### Test Create Site
```bash
curl -X POST http://localhost:8000/api/sites \
  -H "Content-Type: application/json" \
  -H "Authorization: Bearer YOUR_TOKEN_HERE" \
  -d '{
    "name": "Test Site",
    "description": "A test tourist site",
    "latitude": 31.5,
    "longitude": 35.2,
    "type": "cultural",
    "image_url": "https://example.com/test.jpg"
  }'
```

