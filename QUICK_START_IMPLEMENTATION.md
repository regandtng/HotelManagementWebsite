# Quick Start - Cải Thiện REST API Của Bạn (Thực Hành)

## 🚀 Bắt Đầu Nhanh

Dưới đây là hướng dẫn thực hành bước-đến-bước để cải thiện API của bạn.

---

## PHẦN 1: So Sánh - Trước & Sau

### ❌ **Hiện Tại (Cách cũ - Hard-coded)**

File: `api.php`
```php
<?php
require_once "bridge.php";
require_once __DIR__ . "/MVC/Controllers/ApiController.php";

$api = new ApiController();
$api->handle();  // ← Tất cả logic trong ApiController
?>
```

File: `MVC/Controllers/ApiController.php` (150+ dòng)
```php
<?php
class ApiController extends controller {
    public function handle() {
        $resource = $this->segments[0] ?? null;
        
        switch ($resource) {
            case 'bookings':
                $this->handleBookings($id);
                break;
            case 'guests':
                $this->handleGuests($id);  // ← Hard-code cho mỗi resource
                break;
            case 'rooms':
                $this->handleRooms($id);
                break;
            // ... nhiều case khác
        }
    }
    
    private function handleGuests($id) {
        // Tất cả logic GET, POST, PUT, DELETE cho guests đấy
        // → Khó bảo trì, khó test
    }
}
?>
```

**Vấn đề:**
- ❌ Khó thêm endpoint mới
- ❌ Khó test từng resource riêng
- ❌ Logic lộn xộn trong 1 file
- ❌ Khó share code giữa các endpoints

---

### ✅ **Sau Khi Cải Thiện (Cách mới - Clean Architecture)**

#### 1. File `api.php` - Đơn giản hơn
```php
<?php
// Autoload classes
spl_autoload_register(function($class) {
    $path = __DIR__ . '/src/' . str_replace('\\', '/', $class) . '.php';
    if (file_exists($path)) {
        require_once $path;
    }
});

use Shared\Http\Request;
use Shared\Http\Response;
use Shared\Http\Router;

$request = new Request();
$response = new Response();
$router = new Router($request, $response);

// Chỉ cần load routes, không cần switch cases
$router->loadRoutes(__DIR__ . '/src/Api/Routes.php');
$router->dispatch();
?>
```

#### 2. File `src/Api/Routes.php` - Khai báo Routes
```php
<?php
// Tất cả routes cùng một chỗ, dễ nhìn
return [
    ['GET', '/api/v1/guests', 'GuestController', 'index'],
    ['POST', '/api/v1/guests', 'GuestController', 'store'],
    ['GET', '/api/v1/guests/:id', 'GuestController', 'show'],
    ['PUT', '/api/v1/guests/:id', 'GuestController', 'update'],
    ['DELETE', '/api/v1/guests/:id', 'GuestController', 'destroy'],
    
    ['GET', '/api/v1/bookings', 'BookingController', 'index'],
    ['POST', '/api/v1/bookings', 'BookingController', 'store'],
    // ... etc
];
?>
```

#### 3. File `src/Api/Controllers/GuestController.php` - Logic rõ ràng
```php
<?php
namespace Api\Controllers;

class GuestController extends BaseApiController {
    
    // Mỗi action riêng
    public function index() {
        // Chỉ logic cho liệt kê guests
    }
    
    public function show() {
        // Chỉ logic cho lấy 1 guest
    }
    
    public function store() {
        // Chỉ logic cho thêm guests
    }
    
    public function update() {
        // Chỉ logic cho cập nhật guests
    }
    
    public function destroy() {
        // Chỉ logic cho xóa guests
    }
}
?>
```

**Lợi ích:**
- ✅ Dễ thêm endpoint mới (thêm route + controller action)
- ✅ Dễ test từng action riêng
- ✅ Logic sạch sẽ, dễ hiểu
- ✅ Dễ share base functionality qua BaseApiController

---

## PHẦN 2: Bước-Thực-Hiện-Từng-Cái-Một

### **Bước 1: Tạo cấu trúc thư mục**

```powershell
# Mở PowerShell và chay dần dần:

# 1. Tạo các thư mục chính
mkdir "C:\xampp\htdocs\Hotel_Management_Website\src"
mkdir "C:\xampp\htdocs\Hotel_Management_Website\src\Api"
mkdir "C:\xampp\htdocs\Hotel_Management_Website\src\Api\Controllers"
mkdir "C:\xampp\htdocs\Hotel_Management_Website\src\Api\Resources"
mkdir "C:\xampp\htdocs\Hotel_Management_Website\src\Shared"
mkdir "C:\xampp\htdocs\Hotel_Management_Website\src\Shared\Http"
mkdir "C:\xampp\htdocs\Hotel_Management_Website\src\Shared\Auth"
mkdir "C:\xampp\htdocs\Hotel_Management_Website\src\Shared\Models"
mkdir "C:\xampp\htdocs\Hotel_Management_Website\config"

cd "C:\xampp\htdocs\Hotel_Management_Website"
```

### **Bước 2: Copy Models cũ sang Models mới**

Bạn cần move (hoặc copy) các Models từ `MVC/Models/` sang `src/Shared/Models/`

```
Cũ:  MVC/Models/GuestModel.php
Mới: src/Shared/Models/Guest.php  (đổi tên, không cần "Model" suffix)
```

**Chỉnh sửa models:**

File `src/Shared/Models/Guest.php` (cần update để sử dụng namespace)
```php
<?php
namespace Shared\Models;

class Guest {
    private $db;
    
    public function __construct() {
        // Kết nối DB (giữ nguyên logic cũ)
        require_once __DIR__ . '/../../settings/connect.php';
        $this->db = $connection; // hoặc PDO instance
    }
    
    public function find($id) {
        // Logic tìm 1 guest
    }
    
    public function all() {
        // Logic lấy tất cả
    }
    
    public function create($data) {
        // Logic thêm mới
    }
    
    public function update($id, $data) {
        // Logic cập nhật
    }
    
    public function delete($id) {
        // Logic xóa
    }
    
    public function paginate($page, $perPage) {
        // Thêm pagination
        $offset = ($page - 1) * $perPage;
        // SELECT ... LIMIT $offset, $perPage
    }
}
?>
```

---

## PHẦN 3: Test API Có Và Không Có Cải Thiện

### **Test 1: Liệt kê Guests**

#### Request
```
GET http://localhost/Hotel_Management_Website/api/v1/guests?page=1&per_page=10
```

#### Response - Hiện tại (Cũ)
```json
{
    "guests": [...]  // ← Không có meta data, khó phân trang
}
```

#### Response - Sau cải thiện (Mới)
```json
{
    "status": "success",
    "data": [
        {
            "id": 1,
            "name": "John Nguyen",
            "email": "john@example.com",
            "phone": "0123456789",
            "created_at": "2024-04-08"
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

---

### **Test 2: Lỗi Validation**

#### Request
```
POST http://localhost/Hotel_Management_Website/api/v1/guests
Content-Type: application/json

{
    "name": "",  // ← Thiếu name
    "email": "invalid-email"  // ← Email không hợp lệ
}
```

#### Response - Hiện tại (Cũ)
```json
{
    "error": "Validation failed"  // ← Không chi tiết
}
```

#### Response - Sau cải thiện (Mới)
```json
{
    "status": "error",
    "message": "Validation failed",
    "errors": {
        "name": "name is required",
        "email": "email must be a valid email"
    }
}
```

---

### **Test 3: Not Found (404)**

#### Request
```
GET http://localhost/Hotel_Management_Website/api/v1/guests/99999
```

#### Response - Mới
```json
{
    "status": "error",
    "message": "Guest not found",
    "code": 404
}
```

---

## PHẦN 4: Ví Dụ Thực Tế - Tạo Endpoint Booking

### **Bước 1: Thêm Routes**

File: `src/Api/Routes.php`
```php
<?php
return [
    // ... routes cũ ...
    
    // BOOKINGS
    ['GET', '/api/v1/bookings', 'BookingController', 'index'],
    ['POST', '/api/v1/bookings', 'BookingController', 'store'],
    ['GET', '/api/v1/bookings/:id', 'BookingController', 'show'],
    ['PUT', '/api/v1/bookings/:id', 'BookingController', 'update'],
    ['DELETE', '/api/v1/bookings/:id', 'BookingController', 'destroy'],
];
?>
```

### **Bước 2: Tạo Resource**

File: `src/Api/Resources/BookingResource.php`
```php
<?php
namespace Api\Resources;

class BookingResource {
    public static function collection($bookings) {
        return array_map([self::class, 'transform'], $bookings);
    }
    
    public static function transform($booking) {
        return [
            'id' => $booking['id'] ?? $booking->id,
            'guest_id' => $booking['guest_id'] ?? $booking->guest_id,
            'room_id' => $booking['room_id'] ?? $booking->room_id,
            'check_in' => $booking['check_in'] ?? $booking->check_in,
            'check_out' => $booking['check_out'] ?? $booking->check_out,
            'total_price' => $booking['total_price'] ?? $booking->total_price,
            'status' => $booking['status'] ?? $booking->status,
            'created_at' => $booking['created_at'] ?? $booking->created_at,
        ];
    }
}
?>
```

### **Bước 3: Tạo Controller**

File: `src/Api/Controllers/BookingController.php`
```php
<?php
namespace Api\Controllers;

use Api\BaseApiController;
use Api\Resources\BookingResource;

class BookingController extends BaseApiController {
    
    public function index() {
        try {
            $page = $this->request->query('page', 1);
            $perPage = $this->request->query('per_page', 10);
            $status = $this->request->query('status');
            
            $bookingModel = $this->model('Booking');
            
            $query = $bookingModel;
            if ($status) {
                $query = $query->where('status', $status);
            }
            
            $bookings = $query->paginate($page, $perPage);
            $total = $bookingModel->where('status', $status ?? null)->count();
            
            return $this->response->paginate(
                BookingResource::collection($bookings),
                $total,
                $page,
                $perPage
            );
        } catch (\Exception $e) {
            return $this->response->error($e->getMessage(), 500);
        }
    }
    
    public function store() {
        try {
            $data = $this->request->all();
            
            $this->validate($data, [
                'guest_id' => ['required'],
                'room_id' => ['required'],
                'check_in' => ['required'],
                'check_out' => ['required'],
            ]);
            
            // Thêm kiểm tra room khả dụng
            // ...
            
            $bookingModel = $this->model('Booking');
            $booking = $bookingModel->create($data);
            
            return $this->response->json([
                'status' => 'success',
                'message' => 'Booking created successfully',
                'data' => BookingResource::transform($booking),
            ], 201);
        } catch (\Exception $e) {
            return $this->response->error($e->getMessage(), 500);
        }
    }
    
    // ... show, update, destroy ...
}
?>
```

### **Bước 4: Test với Postman/Curl**

```bash
# Test GET /api/v1/bookings
curl -X GET "http://localhost/Hotel_Management_Website/api/v1/bookings?page=1&per_page=10" \
  -H "Content-Type: application/json"

# Test POST /api/v1/bookings
curl -X POST "http://localhost/Hotel_Management_Website/api/v1/bookings" \
  -H "Content-Type: application/json" \
  -d '{
      "guest_id": 1,
      "room_id": 101,
      "check_in": "2024-04-15",
      "check_out": "2024-04-18",
      "total_price": 1500000
  }'
```

---

## PHẦN 5: Cheat Sheet - Những Thay Đổi Chính

| Thành Phần | Cũ | Mới |
|-----------|----|----|
| **Entry Point** | `api.php` với switch cases | `api.php` + `Router.php` |
| **Routes** | Hard-coded trong ApiController | Centralized `Routes.php` |
| **Controller** | 1 file ApiController (300+ lines) | Tách ra: GuestController, BookingController, etc. |
| **Input** | `$_POST`, `$_GET` trực tiếp | `$request->input()` |
| **Output** | `json_encode($data)` | `$response->json($data)` |
| **Errors** | `respond(404, ['error' => '...'])` | `$response->notFound(...)` |
| **Validation** | Không có | `$this->validate($data, $rules)` |
| **Pagination** | Không support | Hỗ trợ qua `$response->paginate()` |
| **Resource Format** | Raw data từ DB | Qua Resource classes |
| **Models** | Trong MVC/Models/ | Trong src/Shared/Models/ |

---

## PHẦN 6: Prioritize - Ưu Tiên Cải Thiện

**Nếu bạn chỉ có 1 giờ:**
1. ✅ Tạo Request class
2. ✅ Tạo Response class
3. ✅ Tạo Router class
4. ✅ Refactor Routes.php

**Nếu bạn có 4 giờ:** Thêm
5. ✅ Tạo BaseApiController
6. ✅ Tạo Resource classes
7. ✅ Move & refactor Models

**Nếu bạn có 8 giờ:** Thêm
8. ✅ Thêm Validation
9. ✅ Thêm Middleware/Auth
10. ✅ Viết API Documentation

---

## 📚 Tài Liệu Tham Khảo

- **API Best Practices**: https://restfulapi.net/
- **PHP PSR Standards**: https://www.php-fig.org/
- **JWT Authentication**: https://jwt.io/
- **Status Codes**: https://httpwg.org/specs/rfc7231.html

---

## ✨ Kết Luận

Cải thiện REST API không phải là thay đổi toàn bộ, mà là:
1. Tách biệt logic rõ ràng
2. Tuân theo chuẩn REST
3. Dễ bảo trì và mở rộng

**Với những cải thiện trên, API của bạn sẽ:**
- ✅ Sạch sẽ và dễ bảo trì
- ✅ Dễ test và debug
- ✅ Dễ mở rộng với endpoints mới
- ✅ Tuân theo best practices
- ✅ Production-ready! 🚀
