# Hotel Management API - Testing Guide

## Tổng quan
API RESTful cho hệ thống quản lý khách sạn với authentication JWT và bảo mật SQL injection.

## Cài đặt Postman
1. Import file `Hotel_Management_API.postman_collection.json` vào Postman
2. Cập nhật biến `base_url` thành URL của bạn (mặc định: `http://localhost/Hotel_Management_Website`)

## Authentication
Tất cả endpoints (trừ login) đều yêu cầu JWT token trong header:
```
Authorization: Bearer <your_jwt_token>
```

### 1. Đăng nhập với cURL
```bash
curl -X POST "http://localhost/Hotel_Management_Website/api/v1/auth/login" \
  -H "Content-Type: application/json" \
  -d '{
    "username": "admin",
    "password": "password123"
  }'
```

**Response:**
```json
{
  "success": true,
  "data": {
    "token": "eyJ0eXAiOiJKV1QiLCJhbGciOiJIUzI1NiJ9...",
    "user": {
      "id": 1,
      "username": "admin",
      "role": "admin"
    }
  },
  "message": "Đăng nhập thành công"
}
```

### 2. Sử dụng token cho các request khác
```bash
TOKEN="your_jwt_token_here"
curl -X GET "http://localhost/Hotel_Management_Website/api/v1/guests" \
  -H "Authorization: Bearer $TOKEN" \
  -H "Content-Type: application/json"
```

## API Endpoints

### Authentication
- `POST /api/v1/auth/login` - Đăng nhập
- `GET /api/v1/auth/me` - Lấy thông tin user hiện tại
- `POST /api/v1/auth/logout` - Đăng xuất

### Guests (Khách hàng)
- `GET /api/v1/guests` - Lấy danh sách khách hàng (có phân trang và tìm kiếm)
- `POST /api/v1/guests` - Tạo khách hàng mới
- `GET /api/v1/guests/{id}` - Lấy chi tiết khách hàng
- `PUT /api/v1/guests/{id}` - Cập nhật khách hàng
- `DELETE /api/v1/guests/{id}` - Xóa khách hàng

### Bookings (Đặt phòng)
- `GET /api/v1/bookings` - Lấy danh sách đặt phòng
- `POST /api/v1/bookings` - Tạo đặt phòng mới
- `GET /api/v1/bookings/{id}` - Lấy chi tiết đặt phòng
- `PUT /api/v1/bookings/{id}` - Cập nhật đặt phòng
- `DELETE /api/v1/bookings/{id}` - Xóa đặt phòng

### Rooms (Phòng)
- `GET /api/v1/rooms` - Lấy danh sách phòng
- `POST /api/v1/rooms` - Tạo phòng mới
- `GET /api/v1/rooms/{id}` - Lấy chi tiết phòng
- `PUT /api/v1/rooms/{id}` - Cập nhật phòng
- `DELETE /api/v1/rooms/{id}` - Xóa phòng

### Services (Dịch vụ)
- `GET /api/v1/services` - Lấy danh sách dịch vụ
- `POST /api/v1/services` - Tạo dịch vụ mới
- `GET /api/v1/services/{id}` - Lấy chi tiết dịch vụ
- `PUT /api/v1/services/{id}` - Cập nhật dịch vụ
- `DELETE /api/v1/services/{id}` - Xóa dịch vụ

### Employees (Nhân viên)
- `GET /api/v1/employees` - Lấy danh sách nhân viên
- `POST /api/v1/employees` - Tạo nhân viên mới
- `GET /api/v1/employees/{id}` - Lấy chi tiết nhân viên
- `PUT /api/v1/employees/{id}` - Cập nhật nhân viên
- `DELETE /api/v1/employees/{id}` - Xóa nhân viên

## Query Parameters
- `page`: Số trang (mặc định: 1)
- `per_page`: Số item mỗi trang (mặc định: 10, tối đa: 100)
- `search`: Từ khóa tìm kiếm (cho guests)

## Response Format
```json
{
  "success": true,
  "data": {...},
  "message": "Success message",
  "meta": {
    "page": 1,
    "per_page": 10,
    "total": 100
  }
}
```

## Error Responses
```json
{
  "success": false,
  "message": "Error message",
  "errors": {...}
}
```

## Bảo mật đã triển khai
- ✅ JWT Authentication
- ✅ SQL Injection protection (Prepared statements)
- ✅ Input validation
- ✅ Role-based access control
- ✅ Error logging

## Test Scenarios với cURL

### 1. Test Authentication
```bash
# Login
curl -X POST "http://localhost/Hotel_Management_Website/api/v1/auth/login" \
  -H "Content-Type: application/json" \
  -d '{"username": "admin", "password": "password123"}'

# Get current user (sử dụng token từ response login)
curl -X GET "http://localhost/Hotel_Management_Website/api/v1/auth/me" \
  -H "Authorization: Bearer YOUR_TOKEN_HERE"
```

### 2. Test CRUD Operations
```bash
TOKEN="your_token_here"

# Create guest
curl -X POST "http://localhost/Hotel_Management_Website/api/v1/guests" \
  -H "Authorization: Bearer $TOKEN" \
  -H "Content-Type: application/json" \
  -d '{
    "name": "John Doe",
    "email": "john@example.com",
    "phone": "+1234567890"
  }'

# Get guests with search
curl -X GET "http://localhost/Hotel_Management_Website/api/v1/guests?search=john&page=1&per_page=5" \
  -H "Authorization: Bearer $TOKEN"

# Update guest
curl -X PUT "http://localhost/Hotel_Management_Website/api/v1/guests/1" \
  -H "Authorization: Bearer $TOKEN" \
  -H "Content-Type: application/json" \
  -d '{
    "name": "John Smith",
    "email": "johnsmith@example.com"
  }'

# Delete guest
curl -X DELETE "http://localhost/Hotel_Management_Website/api/v1/guests/1" \
  -H "Authorization: Bearer $TOKEN"
```

### 3. Test Error Handling
```bash
# Unauthorized access
curl -X GET "http://localhost/Hotel_Management_Website/api/v1/guests"

# Invalid token
curl -X GET "http://localhost/Hotel_Management_Website/api/v1/guests" \
  -H "Authorization: Bearer invalid_token"

# Validation error
curl -X POST "http://localhost/Hotel_Management_Website/api/v1/guests" \
  -H "Authorization: Bearer $TOKEN" \
  -H "Content-Type: application/json" \
  -d '{"email": "invalid-email"}'
```

## Troubleshooting
- **401 Unauthorized**: Token không hợp lệ hoặc hết hạn
- **403 Forbidden**: Không có quyền truy cập
- **404 Not Found**: Endpoint hoặc resource không tồn tại
- **422 Unprocessable Entity**: Dữ liệu validation lỗi
- **500 Internal Server Error**: Lỗi server (check logs trong `src/storage/logs/api.log`)

## Database Schema Notes
API sử dụng các bảng với tên tiếng Việt:
- `hotels_guests` (guests)
- `bookings_booking` (bookings)
- `rooms_room` (rooms)
- `hotels_employees` (employees)
- `services_service` (services)
- `payments_payment` (payments)
- `discounts_discount` (discounts)
- `departments_department` (departments)
- `room_types_roomtype` (room_types)
- `accounts_account` (accounts)
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
