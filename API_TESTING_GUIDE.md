# API Testing Guide

## Quick Testing with cURL

### 1. Test API Info Endpoint

Get information about the API:

```bash
curl -X GET "http://localhost/Hotel_Management_Website/api/v1" \
  -H "Content-Type: application/json"
```

**Expected Response (200 OK):**
```json
{
    "message": "Hotel Management REST API",
    "version": "1.0",
    "endpoints": [
        "guests",
        "bookings",
        "rooms",
        "room-types",
        "services",
        ...
    ],
    "documentation": {
        "GET /{resource}": "List all resources with pagination",
        "GET /{resource}/{id}": "Get a specific resource",
        ...
    }
}
```

---

## Testing GUESTS Resource

### Get All Guests

```bash
curl -X GET "http://localhost/Hotel_Management_Website/api/v1/guests" \
  -H "Content-Type: application/json"
```

### Get All Guests with Pagination

```bash
curl -X GET "http://localhost/Hotel_Management_Website/api/v1/guests?page=1&per_page=10" \
  -H "Content-Type: application/json"
```

### Search Guests

```bash
curl -X GET "http://localhost/Hotel_Management_Website/api/v1/guests?search=John" \
  -H "Content-Type: application/json"
```

### Get Single Guest

```bash
curl -X GET "http://localhost/Hotel_Management_Website/api/v1/guests/1" \
  -H "Content-Type: application/json"
```

**Expected Response (200 OK):**
```json
{
    "status": "success",
    "message": "Guest retrieved successfully",
    "data": {
        "id": 1,
        "name": "John Nguyen",
        "email": "john@example.com",
        "phone": "0123456789",
        "address": "123 Main St",
        "national_id": "123456789",
        "created_at": "2024-04-08 10:00:00",
        "updated_at": "2024-04-08 10:00:00"
    }
}
```

### Create New Guest

```bash
curl -X POST "http://localhost/Hotel_Management_Website/api/v1/guests" \
  -H "Content-Type: application/json" \
  -d '{
    "name": "Jane Smith",
    "email": "jane.smith@example.com",
    "phone": "0987654321",
    "address": "456 Oak Avenue",
    "national_id": "987654321"
  }'
```

**Expected Response (201 Created):**
```json
{
    "status": "success",
    "message": "Guest created successfully",
    "data": {
        "id": 100,
        "name": "Jane Smith",
        "email": "jane.smith@example.com",
        "phone": "0987654321",
        "address": "456 Oak Avenue",
        "national_id": "987654321",
        "created_at": "2024-04-08 14:30:00",
        "updated_at": "2024-04-08 14:30:00"
    }
}
```

### Update Guest

```bash
curl -X PUT "http://localhost/Hotel_Management_Website/api/v1/guests/1" \
  -H "Content-Type: application/json" \
  -d '{
    "name": "John Updated",
    "phone": "0111222333"
  }'
```

**Expected Response (200 OK):**
```json
{
    "status": "success",
    "message": "Guest updated successfully",
    "data": {
        "id": 1,
        "name": "John Updated",
        "email": "john@example.com",
        "phone": "0111222333",
        ...
    }
}
```

### Delete Guest

```bash
curl -X DELETE "http://localhost/Hotel_Management_Website/api/v1/guests/1" \
  -H "Content-Type: application/json"
```

**Expected Response (200 OK):**
```json
{
    "status": "success",
    "message": "Guest deleted successfully",
    "data": null
}
```

---

## Testing BOOKINGS Resource

### Get All Bookings

```bash
curl -X GET "http://localhost/Hotel_Management_Website/api/v1/bookings" \
  -H "Content-Type: application/json"
```

### Get Bookings by Status

```bash
curl -X GET "http://localhost/Hotel_Management_Website/api/v1/bookings?status=pending" \
  -H "Content-Type: application/json"
```

### Create Booking

```bash
curl -X POST "http://localhost/Hotel_Management_Website/api/v1/bookings" \
  -H "Content-Type: application/json" \
  -d '{
    "guest_id": 1,
    "room_id": 101,
    "check_in": "2024-04-15",
    "check_out": "2024-04-18",
    "total_price": 1500000,
    "status": "pending"
  }'
```

### Get Single Booking

```bash
curl -X GET "http://localhost/Hotel_Management_Website/api/v1/bookings/5" \
  -H "Content-Type: application/json"
```

### Update Booking

```bash
curl -X PUT "http://localhost/Hotel_Management_Website/api/v1/bookings/5" \
  -H "Content-Type: application/json" \
  -d '{
    "status": "confirmed",
    "total_price": 1600000
  }'
```

### Delete Booking

```bash
curl -X DELETE "http://localhost/Hotel_Management_Website/api/v1/bookings/5" \
  -H "Content-Type: application/json"
```

---

## Testing ROOMS Resource

### Get All Rooms

```bash
curl -X GET "http://localhost/Hotel_Management_Website/api/v1/rooms" \
  -H "Content-Type: application/json"
```

### Create Room

```bash
curl -X POST "http://localhost/Hotel_Management_Website/api/v1/rooms" \
  -H "Content-Type: application/json" \
  -d '{
    "room_number": "501",
    "room_type_id": 2,
    "floor": 5,
    "status": "available"
  }'
```

---

## Testing ROOM TYPES Resource

### Get All Room Types

```bash
curl -X GET "http://localhost/Hotel_Management_Website/api/v1/room-types" \
  -H "Content-Type: application/json"
```

### Create Room Type

```bash
curl -X POST "http://localhost/Hotel_Management_Website/api/v1/room-types" \
  -H "Content-Type: application/json" \
  -d '{
    "name": "Deluxe Suite",
    "description": "Premium room with ocean view",
    "price_per_night": 500000,
    "max_capacity": 4
  }'
```

---

## Testing SERVICES Resource

### Get All Services

```bash
curl -X GET "http://localhost/Hotel_Management_Website/api/v1/services" \
  -H "Content-Type: application/json"
```

### Create Service

```bash
curl -X POST "http://localhost/Hotel_Management_Website/api/v1/services" \
  -H "Content-Type: application/json" \
  -d '{
    "name": "Spa Treatment",
    "description": "Relaxing massage and spa services",
    "price": 300000
  }'
```

---

## Testing DEPARTMENTS Resource

### Get All Departments

```bash
curl -X GET "http://localhost/Hotel_Management_Website/api/v1/departments" \
  -H "Content-Type: application/json"
```

### Create Department

```bash
curl -X POST "http://localhost/Hotel_Management_Website/api/v1/departments" \
  -H "Content-Type: application/json" \
  -d '{
    "name": "Housekeeping",
    "description": "Room cleaning and maintenance"
  }'
```

---

## Testing EMPLOYEES Resource

### Get All Employees

```bash
curl -X GET "http://localhost/Hotel_Management_Website/api/v1/employees" \
  -H "Content-Type: application/json"
```

### Create Employee

```bash
curl -X POST "http://localhost/Hotel_Management_Website/api/v1/employees" \
  -H "Content-Type: application/json" \
  -d '{
    "name": "Nguyen Van A",
    "email": "nguyen@example.com",
    "phone": "0901234567",
    "department_id": 1,
    "position": "Manager"
  }'
```

---

## Testing PAYMENTS Resource

### Get All Payments

```bash
curl -X GET "http://localhost/Hotel_Management_Website/api/v1/payments" \
  -H "Content-Type: application/json"
```

### Create Payment

```bash
curl -X POST "http://localhost/Hotel_Management_Website/api/v1/payments" \
  -H "Content-Type: application/json" \
  -d '{
    "booking_id": 5,
    "amount": 1500000,
    "payment_method": "credit_card",
    "status": "completed",
    "paid_at": "2024-04-15 10:00:00"
  }'
```

---

## Testing DISCOUNTS Resource

### Get All Discounts

```bash
curl -X GET "http://localhost/Hotel_Management_Website/api/v1/discounts" \
  -H "Content-Type: application/json"
```

### Create Discount

```bash
curl -X POST "http://localhost/Hotel_Management_Website/api/v1/discounts" \
  -H "Content-Type: application/json" \
  -d '{
    "code": "SPRING2024",
    "description": "Spring promotion discount",
    "discount_percent": 15,
    "valid_from": "2024-04-01",
    "valid_to": "2024-06-30"
  }'
```

---

## Testing ACCOUNTS Resource

### Get All Accounts

```bash
curl -X GET "http://localhost/Hotel_Management_Website/api/v1/accounts" \
  -H "Content-Type: application/json"
```

### Create Account

```bash
curl -X POST "http://localhost/Hotel_Management_Website/api/v1/accounts" \
  -H "Content-Type: application/json" \
  -d '{
    "name": "Admin User",
    "email": "admin@example.com",
    "phone": "0901111111",
    "role": "admin"
  }'
```

---

## Error Testing

### Test 404 Not Found

```bash
curl -X GET "http://localhost/Hotel_Management_Website/api/v1/guests/99999" \
  -H "Content-Type: application/json"
```

**Expected Response (404 Not Found):**
```json
{
    "status": "error",
    "message": "Guest not found"
}
```

### Test Validation Error

```bash
curl -X POST "http://localhost/Hotel_Management_Website/api/v1/guests" \
  -H "Content-Type: application/json" \
  -d '{
    "name": "John",
    "email": "invalid-email"
  }'
```

**Expected Response (422 Unprocessable Entity):**
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

---

## Using Postman

### 1. Start Postman

### 2. Create Collection
- Click "Create" → "Collection"
- Name it "Hotel Management API"

### 3. Create Requests

For each endpoint:
1. Click "Add a request"
2. Set Method (GET, POST, PUT, DELETE)
3. Enter URL (e.g., `http://localhost/Hotel_Management_Website/api/v1/guests`)
4. Set Headers:
   - `Content-Type: application/json`
5. For POST/PUT: Add JSON body
6. Click "Save"

### 4. Example for Creating Guest

**Method:** POST  
**URL:** `http://localhost/Hotel_Management_Website/api/v1/guests`  
**Headers:**
```
Content-Type: application/json
```

**Body (raw JSON):**
```json
{
    "name": "John Doe",
    "email": "john@example.com",
    "phone": "0123456789",
    "address": "123 Main Street",
    "national_id": "123456789"
}
```

---

## Testing Checklist

Before deploying, test these:

- [ ] GET /api/v1 (API info)
- [ ] GET /api/v1/guests (list)
- [ ] GET /api/v1/guests?page=1&per_page=5
- [ ] GET /api/v1/guests/1 (single)
- [ ] POST /api/v1/guests (create)
- [ ] PUT /api/v1/guests/1 (update)
- [ ] DELETE /api/v1/guests/1 (delete)
- [ ] GET /api/v1/bookings
- [ ] POST /api/v1/bookings (create)
- [ ] GET /api/v1/rooms
- [ ] GET /api/v1/room-types
- [ ] GET /api/v1/services
- [ ] GET /api/v1/departments
- [ ] GET /api/v1/employees
- [ ] GET /api/v1/payments
- [ ] GET /api/v1/discounts
- [ ] GET /api/v1/accounts
- [ ] Test 404 (non-existent ID)
- [ ] Test validation error (invalid email)
- [ ] Test pagination

---

## Troubleshooting

### API returns 404

Check:
1. URL is correct
2. HTTP method is correct (GET, POST, etc.)
3. Resource ID exists (for GET/:id, PUT/:id, DELETE/:id)
4. Resource name is correct

### API returns validation error

Check:
1. All required fields are provided
2. Email format is valid (for email fields)
3. Numeric fields contain numbers only
4. Date fields are in correct format

### API returns 500 server error

Check:
1. Database connection is working
2. Database tables exist
3. Check `storage/logs/api.log` for detailed error
4. Table column names match model properties

### JSON parsing error

Check:
1. `Content-Type: application/json` header is set
2. JSON body is valid (use JSON validator)
3. All strings are quoted with double quotes

---

## Performance Tips

- Use pagination for large results
- Limit per_page to reasonable number (e.g., 20-50)
- Use search instead of loading all records
- Cache frequently accessed data
- Use indexes on frequently filtered columns

---

## Next Steps

1. Run all tests from the checklist
2. Verify all endpoints work
3. Check error logs
4. Add authentication if needed
5. Deploy to production

---

Happy Testing! 🚀
