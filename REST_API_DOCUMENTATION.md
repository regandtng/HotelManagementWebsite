# Hotel Management REST API Documentation

## Overview

This is a completely refactored REST API for the Hotel Management System built following **MVC + REST API best practices**. The API provides full CRUD operations for all hotel management resources through standardized REST endpoints.

**Version:** 1.0  
**Base URL:** `http://localhost/Hotel_Management_Website/api/v1`  
**Protocol:** HTTP/HTTPS  
**Response Format:** JSON

---

## Architecture

### Project Structure

```
Hotel_Management_Website/
├── api.php                    # Main API entry point
├── src/
│   ├── Api/
│   │   ├── Controllers/       # Request handlers
│   │   │   ├── ApiController.php
│   │   │   ├── GuestController.php
│   │   │   ├── BookingController.php
│   │   │   ├── RoomController.php
│   │   │   └── ... (other controllers)
│   │   ├── Resources/         # Response formatters
│   │   │   ├── GuestResource.php
│   │   │   ├── BookingResource.php
│   │   │   └── ... (other resources)
│   │   ├── Routes.php         # All API routes defined
│   │   └── BaseApiController.php  # Base controller with common methods
│   └── Shared/
│       ├── Http/
│       │   ├── Request.php    # HTTP request handler
│       │   ├── Response.php   # HTTP response handler
│       │   └── Router.php     # Route dispatcher
│       └── Models/            # Database models
│           ├── BaseModel.php
│           ├── Guest.php
│           ├── Booking.php
│           └── ... (other models)
├── MVC/                       # Original web application
│   ├── Controllers/
│   ├── Models/
│   └── Views/
└── storage/
    └── logs/                  # API logs
```

### Architecture Benefits

1. **Separation of Concerns:** Controllers, Models, and Resources are well-separated
2. **Reusability:** Shared models can be used by both web and API
3. **Maintainability:** Each request type (GET, POST, etc.) is handled in separate methods
4. **Scalability:** Easy to add new endpoints by creating new controllers
5. **Type Safety:** Resource classes ensure consistent response format

---

## REST API Endpoints

### 1. Guests Resource

#### List Guests
```http
GET /api/v1/guests?page=1&per_page=10&search=John
```

**Query Parameters:**
- `page` (int, optional): Page number (default: 1)
- `per_page` (int, optional): Items per page (default: 10, max: 100)
- `search` (string, optional): Search by name, email, or phone

**Response:**
```json
{
    "status": "success",
    "data": [
        {
            "id": 1,
            "name": "John Nguyen",
            "email": "john@example.com",
            "phone": "0123456789",
            "address": "123 Main St",
            "national_id": "123456789",
            "created_at": "2024-04-08 10:00:00",
            "updated_at": "2024-04-08 10:00:00"
        }
    ],
    "meta": {
        "total": 150,
        "per_page": 10,
        "page": 1,
        "last_page": 15
    }
}
```

#### Get Single Guest
```http
GET /api/v1/guests/:id
```

**Example:**
```http
GET /api/v1/guests/1
```

#### Create Guest
```http
POST /api/v1/guests
Content-Type: application/json

{
    "name": "Jane Doe",
    "email": "jane@example.com",
    "phone": "0987654321",
    "address": "456 Oak Ave",
    "national_id": "987654321"
}
```

**Required Fields:**
- `name` (string)
- `email` (string, valid email format)
- `phone` (string)

**Response (201 Created):**
```json
{
    "status": "success",
    "message": "Guest created successfully",
    "data": {
        "id": 151,
        "name": "Jane Doe",
        "email": "jane@example.com",
        ...
    }
}
```

#### Update Guest
```http
PUT /api/v1/guests/:id
Content-Type: application/json

{
    "name": "Jane Smith",
    "phone": "0987654322"
}
```

#### Delete Guest
```http
DELETE /api/v1/guests/:id
```

---

### 2. Bookings Resource

Similar CRUD operations for bookings:

```http
GET    /api/v1/bookings              # List bookings
POST   /api/v1/bookings              # Create booking
GET    /api/v1/bookings/:id          # Get single booking
PUT    /api/v1/bookings/:id          # Update booking
DELETE /api/v1/bookings/:id          # Delete booking
```

**Create Booking Example:**
```http
POST /api/v1/bookings
Content-Type: application/json

{
    "guest_id": 1,
    "room_id": 101,
    "check_in": "2024-04-15",
    "check_out": "2024-04-18",
    "total_price": 1500000,
    "status": "pending"
}
```

---

### 3. Rooms Resource

```http
GET    /api/v1/rooms                 # List rooms
POST   /api/v1/rooms                 # Create room
GET    /api/v1/rooms/:id             # Get single room
PUT    /api/v1/rooms/:id             # Update room
DELETE /api/v1/rooms/:id             # Delete room
```

---

### 4. Room Types Resource

```http
GET    /api/v1/room-types            # List room types
POST   /api/v1/room-types            # Create room type
GET    /api/v1/room-types/:id        # Get single room type
PUT    /api/v1/room-types/:id        # Update room type
DELETE /api/v1/room-types/:id        # Delete room type
```

---

### 5. Services Resource

```http
GET    /api/v1/services              # List services
POST   /api/v1/services              # Create service
GET    /api/v1/services/:id          # Get single service
PUT    /api/v1/services/:id          # Update service
DELETE /api/v1/services/:id          # Delete service
```

---

### 6. Departments Resource

```http
GET    /api/v1/departments           # List departments
POST   /api/v1/departments           # Create department
GET    /api/v1/departments/:id       # Get single department
PUT    /api/v1/departments/:id       # Update department
DELETE /api/v1/departments/:id       # Delete department
```

---

### 7. Employees Resource

```http
GET    /api/v1/employees             # List employees
POST   /api/v1/employees             # Create employee
GET    /api/v1/employees/:id         # Get single employee
PUT    /api/v1/employees/:id         # Update employee
DELETE /api/v1/employees/:id         # Delete employee
```

---

### 8. Payments Resource

```http
GET    /api/v1/payments              # List payments
POST   /api/v1/payments              # Create payment
GET    /api/v1/payments/:id          # Get single payment
PUT    /api/v1/payments/:id          # Update payment
DELETE /api/v1/payments/:id          # Delete payment
```

---

### 9. Discounts Resource

```http
GET    /api/v1/discounts             # List discounts
POST   /api/v1/discounts             # Create discount
GET    /api/v1/discounts/:id         # Get single discount
PUT    /api/v1/discounts/:id         # Update discount
DELETE /api/v1/discounts/:id         # Delete discount
```

---

### 10. Accounts Resource

```http
GET    /api/v1/accounts              # List accounts
POST   /api/v1/accounts              # Create account
GET    /api/v1/accounts/:id          # Get single account
PUT    /api/v1/accounts/:id          # Update account
DELETE /api/v1/accounts/:id          # Delete account
```

---

## Response Format

### Success Response (200 OK)

```json
{
    "status": "success",
    "message": "Optional message",
    "data": { }
}
```

### Paginated Response (200 OK)

```json
{
    "status": "success",
    "data": [ ],
    "meta": {
        "total": 100,
        "per_page": 10,
        "page": 1,
        "last_page": 10
    }
}
```

### Created Response (201 Created)

```json
{
    "status": "success",
    "message": "Resource created successfully",
    "data": { }
}
```

### Error Response

```json
{
    "status": "error",
    "message": "Error description"
}
```

### Validation Error Response (422 Unprocessable Entity)

```json
{
    "status": "error",
    "message": "Validation failed",
    "errors": {
        "email": "email must be a valid email",
        "name": "Trường name là bắt buộc"
    }
}
```

---

## HTTP Status Codes

| Code | Meaning | Example |
|------|---------|---------|
| 200 | OK | GET successful, PUT successful |
| 201 | Created | POST successful |
| 204 | No Content | DELETE successful |
| 400 | Bad Request | Missing required fields |
| 404 | Not Found | Resource doesn't exist |
| 409 | Conflict | Duplicate email |
| 422 | Validation Failed | Invalid input |
| 500 | Server Error | Database error |

---

## Error Handling

### Common Errors

#### 404 Not Found
```json
{
    "status": "error",
    "message": "Guest not found"
}
```

#### 400 Bad Request
```json
{
    "status": "error",
    "message": "ID is required"
}
```

#### 422 Validation Error
```json
{
    "status": "error",
    "message": "Validation failed",
    "errors": {
        "email": "Trường email là bắt buộc",
        "phone": "Trường phone là bắt buộc"
    }
}
```

#### 500 Server Error
```json
{
    "status": "error",
    "message": "Internal server error"
}
```

---

## Testing the API

### Using cURL

**List Guests:**
```bash
curl -X GET "http://localhost/Hotel_Management_Website/api/v1/guests?page=1&per_page=10" \
  -H "Content-Type: application/json"
```

**Create Guest:**
```bash
curl -X POST "http://localhost/Hotel_Management_Website/api/v1/guests" \
  -H "Content-Type: application/json" \
  -d '{
    "name": "John Doe",
    "email": "john@example.com",
    "phone": "0123456789"
  }'
```

**Get Single Guest:**
```bash
curl -X GET "http://localhost/Hotel_Management_Website/api/v1/guests/1" \
  -H "Content-Type: application/json"
```

**Update Guest:**
```bash
curl -X PUT "http://localhost/Hotel_Management_Website/api/v1/guests/1" \
  -H "Content-Type: application/json" \
  -d '{
    "name": "Jane Doe",
    "phone": "0987654321"
  }'
```

**Delete Guest:**
```bash
curl -X DELETE "http://localhost/Hotel_Management_Website/api/v1/guests/1" \
  -H "Content-Type: application/json"
```

### Using Postman

1. Open Postman
2. Create a new request
3. Set the method (GET, POST, PUT, DELETE)
4. Enter the URL (e.g., `http://localhost/Hotel_Management_Website/api/v1/guests`)
5. Set headers: `Content-Type: application/json`
6. Add request body for POST/PUT requests
7. Click Send

---

## Pagination

All list endpoints support pagination with these query parameters:

- `page` - Current page (default: 1)
- `per_page` - Items per page (default: 10, max: 100)

**Example:**
```http
GET /api/v1/guests?page=2&per_page=20
```

The response includes metadata:
```json
{
    "meta": {
        "total": 100,        # Total number of records
        "per_page": 20,      # Items per page
        "page": 2,           # Current page
        "last_page": 5       # Total pages
    }
}
```

---

## Searching

Some endpoints support search with the `search` query parameter:

```http
GET /api/v1/guests?search=John&page=1&per_page=10
```

The search parameter is applied to relevant fields (name, email, phone for guests, etc.)

---

## Data Validation

### Validation Rules

**Guests:**
- `name` - Required, string
- `email` - Required, valid email format, unique
- `phone` - Required, string

**Bookings:**
- `guest_id` - Required, numeric, must exist
- `room_id` - Required, numeric, must exist
- `check_in` - Required, date format
- `check_out` - Required, date format

**Rooms:**
- `room_number` - Required, string
- `room_type_id` - Required, numeric
- `floor` - Required, numeric

**Room Types:**
- `name` - Required, string
- `price_per_night` - Required, numeric

**Services:**
- `name` - Required, string
- `price` - Required, numeric

---

## API Features

### ✅ Implemented

- [x] RESTful endpoints for all resources
- [x] Pagination support
- [x] Search functionality
- [x] Input validation
- [x] Error handling
- [x] Resource formatting
- [x] CORS headers
- [x] JSON responses
- [x] Logging

### 🔄 Future Enhancements

- [ ] Authentication (JWT tokens)
- [ ] Authorization (role-based access)
- [ ] Rate limiting
- [ ] API versioning
- [ ] Bulk operations
- [ ] Filtering
- [ ] Sorting
- [ ] API key management
- [ ] API documentation (Swagger/OpenAPI)

---

## Key Improvements Over Previous Version

| Feature | Before | After |
|---------|--------|-------|
| Routing | Hard-coded switch cases | Centralized Routes.php |
| Controllers | 1 large ApiController | Separate resource controllers |
| Request Handling | Superglobals ($_GET, $_POST) | Request class |
| Response Format | Raw json_encode() | Response class with helpers |
| Validation | No validation | validate() method |
| Models | Multiple files | Centralized BaseModel |
| Code Organization | Mixed | Clean separation |
| Pagination | Not supported | Fully supported |
| Search | Not supported | Fully supported |
| Error Handling | Basic | Comprehensive |
| Maintainability | Difficult | Easy |

---

## File Structure Summary

```
Total Files Created:
├── Core Classes (3)
│   ├── Request.php
│   ├── Response.php
│   └── Router.php
│
├── Models (9)
│   ├── BaseModel.php
│   ├── Guest.php
│   ├── Booking.php
│   ├── Room.php
│   ├── RoomType.php
│   ├── Service.php
│   ├── Department.php
│   ├── Employee.php
│   ├── Payment.php
│   ├── Discount.php
│   └── Account.php
│
├── Controllers (10)
│   ├── ApiController.php
│   ├── GuestController.php
│   ├── BookingController.php
│   ├── RoomController.php
│   ├── RoomTypeController.php
│   ├── ServiceController.php
│   ├── DepartmentController.php
│   ├── EmployeeController.php
│   ├── PaymentController.php
│   ├── DiscountController.php
│   └── AccountController.php
│
├── Resources (10)
│   ├── GuestResource.php
│   ├── BookingResource.php
│   ├── RoomResource.php
│   ├── RoomTypeResource.php
│   ├── ServiceResource.php
│   ├── DepartmentResource.php
│   ├── EmployeeResource.php
│   ├── PaymentResource.php
│   ├── DiscountResource.php
│   └── AccountResource.php
│
├── Base Files (2)
│   ├── BaseApiController.php
│   └── Routes.php
│
└── Entry Point (1)
    └── api.php (updated)

Total: 45 new files implementing professional REST API structure
```

---

## Support & Troubleshooting

### 404 Errors

If you get "404 Not Found", check:
1. URL spelling
2. Resource ID exists
3. HTTP method is correct (GET, POST, PUT, DELETE)

### Validation Errors

If you get validation errors:
1. Check required fields are provided
2. Email format is valid
3. Numeric fields contain numbers
4. Date fields are in correct format

### Database Errors

If database errors occur:
1. Check database connection in `MVC/Core/connectDB.php`
2. Ensure database tables exist
3. Check table column names match model properties
4. Review error logs in `storage/logs/api.log`

---

## License

This API is part of the Hotel Management System project.

---

**Last Updated:** April 8, 2024  
**Version:** 1.0
