# WasteWise – REST API Specifications & Documentation

This document provides technical specifications for all RESTful API endpoints implemented in the **WasteWise** application and Flask microservice.

---

## 🔒 Authentication & Authorization Summary

WasteWise REST APIs use **Header-based Key Authentication** or **Session Authentication** depending on the route type:

| Endpoint Type | Access Level | Auth Mechanism | Headers Required |
| :--- | :--- | :--- | :--- |
| **Public APIs** | Public / Guest | None | `Accept: application/json` |
| **Protected Admin APIs** | Admin Only | Dual Auth (`EnsureIsAdmin`): Session OR API Key | `X-API-KEY: <ADMIN_API_KEY>` or Session Cookie |
| **Flask Microservice APIs** | Public / Internal | Service Internal | `Content-Type: application/json` |

---

## 🌐 1. Public REST API Endpoints

### Endpoint 1.1: Waste Search Autocomplete
- **URL**: `GET /api/waste-search`
- **Purpose**: Provides live AJAX waste item search suggestions, disposal instructions, and nearest collection centre coordinates.
- **Authentication**: None (Public)

#### Request Query Parameters
| Parameter | Type | Required | Description | Example |
| :--- | :--- | :---: | :--- | :--- |
| `q` | `string` | No | Search query term | `plastic` |
| `limit` | `integer` | No | Maximum result count (default: 10) | `5` |

#### Example Request
```http
GET /api/waste-search?q=plastic HTTP/1.1
Host: 127.0.0.1:8000
Accept: application/json
```

#### Example Response (HTTP 200 OK)
```json
{
  "success": true,
  "count": 2,
  "query": "plastic",
  "data": [
    {
      "id": 1,
      "name": "Plastic Bottle",
      "category": {
        "id": 2,
        "name": "Plastic Waste",
        "icon": "bi-recycle"
      },
      "disposal_method": {
        "id": 1,
        "title": "Recycling Centre Disposal",
        "description": "Rinse, flatten, and deposit in yellow recycling bins.",
        "hazard_level": "low"
      },
      "handling_instructions": "Remove cap and rinse clean before disposal."
    }
  ]
}
```

---

### Endpoint 1.2: Search Trends Analytics
- **URL**: `GET /api/search-trends`
- **Purpose**: Returns search history analytics records used by the Flask service and Admin dashboard to compute search frequencies and category breakdowns.
- **Authentication**: None (Public/Service)

#### Request Query Parameters
| Parameter | Type | Required | Description | Example |
| :--- | :--- | :---: | :--- | :--- |
| `focus` | `string` | No | Category filter (`all`, `electronic`, `plastic`, `organic`, etc.) | `electronic` |

#### Example Request
```http
GET /api/search-trends?focus=electronic HTTP/1.1
Host: 127.0.0.1:8000
Accept: application/json
```

#### Example Response (HTTP 200 OK)
```json
{
  "success": true,
  "total_searches": 3,
  "focus": "electronic",
  "records": [
    {
      "searched_item_name": "Old Laptop",
      "category": "Electronic Waste",
      "searched_at": "2026-09-02 14:30:00"
    },
    {
      "searched_item_name": "Broken Smartphone",
      "category": "Electronic Waste",
      "searched_at": "2026-09-02 15:10:00"
    }
  ]
}
```

---

## 🛡️ 2. Protected Admin REST API Endpoints

### Endpoint 2.1: Bulk Collection Centre CSV Import
- **URL**: `POST /api/admin/collection-centres/import`
- **Purpose**: Accepts a JSON array of parsed collection centre records, validates fields and foreign entities, updates duplicates, and inserts records inside an atomic `DB::transaction`.
- **Authentication**: Required (`X-API-KEY` matching `ADMIN_API_KEY` or Admin Session Cookie)

#### Request Headers
```http
POST /api/admin/collection-centres/import HTTP/1.1
Host: 127.0.0.1:8000
Content-Type: application/json
X-API-KEY: dev-wastewise-admin-key-2026
```

#### Request Payload Body Example
```json
{
  "centres": [
    {
      "name": "Surat Recycling Hub",
      "category": "Electronic Waste",
      "city": "Surat",
      "address": "Ring Road, Surat",
      "latitude": 21.17024,
      "longitude": 72.83106,
      "phone": "9876543210"
    },
    {
      "name": "Ahmedabad Eco Depot",
      "category": "Plastic Waste",
      "city": "Ahmedabad",
      "address": "CG Road, Ahmedabad",
      "latitude": 23.02250,
      "longitude": 72.57140,
      "phone": "9876543211"
    }
  ]
}
```

#### Successful Response (HTTP 200 OK)
```json
{
  "success": true,
  "imported": 2,
  "message": "Successfully imported 2 collection centres."
}
```

#### Error Responses

##### 1. HTTP 401 Unauthorized (Missing or Invalid API Key)
```json
{
  "success": false,
  "message": "Unauthorized access. Admin privileges or valid X-API-KEY required."
}
```

##### 2. HTTP 422 Unprocessable Entity (Validation Error & Transaction Rollback)
```json
{
  "success": false,
  "message": "CSV data validation failed. Entire batch rolled back.",
  "errors": [
    "Row 1: Category 'Unknown Category' does not exist in database.",
    "Row 2: Invalid latitude '999.0'. Must be between -90 and 90."
  ]
}
```

---

## 🐍 3. Flask Microservice Endpoints (Port 5000)

### Endpoint 3.1: JSON Analytics Endpoint
- **URL**: `POST http://127.0.0.1:5000/api/analyze`
- **Purpose**: Computes top searched item and category percentage breakdown from raw JSON search logs.
- **Authentication**: None

#### Request Payload Body Example
```json
{
  "records": [
    {"waste_item": "Plastic Bottle", "category": "Plastic Waste"},
    {"waste_item": "Plastic Bottle", "category": "Plastic Waste"},
    {"waste_item": "Old Phone", "category": "Electronic Waste"}
  ]
}
```

#### Response Example (HTTP 200 OK)
```json
{
  "success": true,
  "total_searches": 3,
  "highest_search_count": 2,
  "top_searched_item": "Plastic Bottle",
  "top_category": "Plastic Waste",
  "category_breakdown": [
    {"category": "Plastic Waste", "count": 2, "percentage": 66.67},
    {"category": "Electronic Waste", "count": 1, "percentage": 33.33}
  ]
}
```

---

## 📊 Standard HTTP Response Codes

| Status Code | Meaning | Description |
| :---: | :--- | :--- |
| `200 OK` | Success | The request succeeded and returned data. |
| `400 Bad Request` | Client Error | Malformed request body or parameters. |
| `401 Unauthorized` | Auth Error | Missing or invalid authentication key. |
| `403 Forbidden` | Permission Error | User lacks administrative permissions. |
| `404 Not Found` | Resource Error | Requested endpoint or resource does not exist. |
| `422 Unprocessable` | Validation Error | Payload failed business validation; transaction rolled back. |
| `500 Server Error` | Internal Error | Unexpected server side exception. |
