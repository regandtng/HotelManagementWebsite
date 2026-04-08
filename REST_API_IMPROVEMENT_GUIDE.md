# Hướng Dẫn Cải Thiện REST API - Bước Thực Hiện

## 📋 Mục Đích
Biến dự án từ ~50% chuẩn tắc thành ~85% chuẩn tắc REST API

---

## BƯỚC 1: Tổ Chức Lại Thư Mục (Recommended Structure)

### Cấu trúc mới:
```
Hotel_Management_Website/
├── api.php                           # Entry point cho API
├── index.php                         # Entry point cho Web
├── config/
│   ├── database.php                  # Cấu hình DB
│   └── api.php                       # Cấu hình API
├── src/
│   ├── Api/
│   │   ├── Controllers/
│   │   │   ├── GuestController.php
│   │   │   ├── BookingController.php
│   │   │   ├── RoomController.php
│   │   │   ├── RoomTypeController.php
│   │   │   └── ServiceController.php
│   │   ├── Resources/
│   │   │   ├── GuestResource.php
│   │   │   ├── BookingResource.php
│   │   │   └── ...
│   │   ├── Middleware/
│   │   │   ├── AuthMiddleware.php
│   │   │   ├── ValidationMiddleware.php
│   │   │   └── CorsMiddleware.php
│   │   ├── Routes.php               # Định nghĩa tất cả routes
│   │   └── ApiServiceProvider.php   # Khởi động API
│   ├── App/
│   │   ├── Controllers/             # Web controllers (HTML)
│   │   ├── Views/
│   │   └── Middleware/
│   ├── Shared/
│   │   ├── Models/                  # Shared between Web & API
│   │   │   ├── Guest.php
│   │   │   ├── Booking.php
│   │   │   └── ...
│   │   ├── Http/
│   │   │   ├── Request.php
│   │   │   ├── Response.php
│   │   │   └── StatusCode.php
│   │   ├── Exceptions/
│   │   │   ├── ApiException.php
│   │   │   ├── ValidationException.php
│   │   │   └── NotFoundException.php
│   │   └── Helpers/
│   │       ├── DateHelper.php
│   │       └── StringHelper.php
├── public/
│   ├── css/
│   ├── js/
│   └── images/
├── storage/
│   ├── logs/
│   └── uploads/
└── .env                              # Environment variables
```

---

## BƯỚC 2: Tạo Các Classes Cơ Bản

### 2.1 Request Class - `src/Shared/Http/Request.php`

```php
<?php
namespace Shared\Http;

class Request {
    private $method;
    private $path;
    private $input;
    private $headers;

    public function __construct() {
        $this->method = $_SERVER['REQUEST_METHOD'];
        $this->path = $this->parsePath();
        $this->input = $this->parseInput();
        $this->headers = $this->parseHeaders();
    }

    /**
     * Lấy HTTP method (GET, POST, PUT, DELETE, PATCH)
     */
    public function getMethod() {
        return strtoupper($this->method);
    }

    /**
     * Lấy request path (/api/guests/1)
     */
    public function getPath() {
        return $this->path;
    }

    /**
     * Lấy một giá trị input
     * Ví dụ: $request->input('name') -> Lấy $_POST['name'] hoặc JSON body['name']
     */
    public function input($key = null, $default = null) {
        if ($key === null) {
            return $this->input;
        }
        return $this->input[$key] ?? $default;
    }

    /**
     * Lấy tất cả input
     */
    public function all() {
        return $this->input;
    }

    /**
     * Lấy một header
     * Ví dụ: $request->header('Authorization')
     */
    public function header($name, $default = null) {
        $name = 'HTTP_' . strtoupper(str_replace('-', '_', $name));
        return $this->headers[$name] ?? $default;
    }

    /**
     * Kiểm tra header có tồn tại không
     */
    public function hasHeader($name) {
        return $this->header($name) !== null;
    }

    /**
     * Lấy URL parameter (từ URL path)
     * Ví dụ: /api/guests/123 -> segments = ['guests', '123']
     */
    public function segments() {
        $path = trim($this->path, '/');
        return $path === '' ? [] : explode('/', $path);
    }

    /**
     * Lấy query string parameter
     * Ví dụ: ?page=1&per_page=10 -> $request->query('page')
     */
    public function query($key = null, $default = null) {
        if ($key === null) {
            return $_GET;
        }
        return $_GET[$key] ?? $default;
    }

    /**
     * Kiểm tra dữ liệu có tồn tại không
     */
    public function has($key) {
        return isset($this->input[$key]);
    }

    /**
     * Kiểm tra method
     */
    public function isMethod($method) {
        return strtoupper($method) === $this->getMethod();
    }

    // ============ Private Methods ============

    private function parsePath() {
        $path = parse_url($_SERVER['REQUEST_URI'], PHP_URL_PATH);
        $script = $_SERVER['SCRIPT_NAME'];

        if (strpos($path, $script) === 0) {
            $path = substr($path, strlen($script));
        } elseif (!empty($_SERVER['PATH_INFO'])) {
            $path = $_SERVER['PATH_INFO'];
        }

        return $path;
    }

    private function parseInput() {
        $input = [];

        // Lấy từ $_GET
        $input += $_GET ?? [];

        // Lấy từ $_POST
        $input += $_POST ?? [];

        // Lấy từ JSON body (nếu có)
        if (in_array($this->getMethod(), ['POST', 'PUT', 'PATCH'])) {
            $json = json_decode(file_get_contents('php://input'), true);
            $input += $json ?? [];
        }

        return $input;
    }

    private function parseHeaders() {
        $headers = [];
        foreach ($_SERVER as $key => $value) {
            if (strpos($key, 'HTTP_') === 0) {
                $headers[$key] = $value;
            }
        }
        return $headers;
    }
}
?>
```

### 2.2 Response Class - `src/Shared/Http/Response.php`

```php
<?php
namespace Shared\Http;

class Response {
    private $status = 200;
    private $data = [];

    public function __construct() {
        header('Content-Type: application/json; charset=utf-8');
    }

    /**
     * Trả về response thành công
     * Ví dụ: response()->json(['name' => 'John'], 200)
     */
    public function json($data, $status = 200) {
        $this->status = $status;
        $this->data = $data;
        $this->send();
    }

    /**
     * Trả về danh sách dữ liệu có pagination
     * Ví dụ: response()->paginate($guests, 100, 1, 10)
     */
    public function paginate($items, $total, $page, $perPage, $status = 200) {
        return $this->json([
            'status' => 'success',
            'data' => $items,
            'meta' => [
                'total' => $total,
                'per_page' => $perPage,
                'page' => $page,
                'last_page' => ceil($total / $perPage),
            ]
        ], $status);
    }

    /**
     * Trả về lỗi
     * Ví dụ: response()->error('Not found', 404)
     */
    public function error($message, $status = 400, $errors = null) {
        return $this->json([
            'status' => 'error',
            'message' => $message,
            'errors' => $errors,
        ], $status);
    }

    /**
     * Trả về 404
     */
    public function notFound($message = 'Resource not found') {
        return $this->error($message, 404);
    }

    /**
     * Trả về 401 Unauthorized
     */
    public function unauthorized($message = 'Unauthorized') {
        return $this->error($message, 401);
    }

    /**
     * Trả về 403 Forbidden
     */
    public function forbidden($message = 'Forbidden') {
        return $this->error($message, 403);
    }

    /**
     * Trả về 422 Validation failed
     */
    public function validationError($errors) {
        return $this->json([
            'status' => 'error',
            'message' => 'Validation failed',
            'errors' => $errors,
        ], 422);
    }

    /**
     * Trả về 500 Internal server error
     */
    public function internalError($message = 'Internal server error') {
        return $this->error($message, 500);
    }

    // ============ Private Methods ============

    private function send() {
        http_response_code($this->status);
        echo json_encode($this->data, JSON_UNESCAPED_UNICODE | JSON_PRETTY_PRINT);
        exit();
    }

    public function setStatus($status) {
        $this->status = $status;
        return $this;
    }
}
?>
```

### 2.3 Status Code Constants - `src/Shared/Http/StatusCode.php`

```php
<?php
namespace Shared\Http;

class StatusCode {
    // 2xx Success
    const OK = 200;
    const CREATED = 201;
    const ACCEPTED = 202;
    const NO_CONTENT = 204;

    // 3xx Redirection
    const MOVED_PERMANENTLY = 301;
    const FOUND = 302;
    const NOT_MODIFIED = 304;

    // 4xx Client Error
    const BAD_REQUEST = 400;
    const UNAUTHORIZED = 401;
    const FORBIDDEN = 403;
    const NOT_FOUND = 404;
    const METHOD_NOT_ALLOWED = 405;
    const CONFLICT = 409;
    const UNPROCESSABLE_ENTITY = 422;
    const TOO_MANY_REQUESTS = 429;

    // 5xx Server Error
    const INTERNAL_SERVER_ERROR = 500;
    const NOT_IMPLEMENTED = 501;
    const SERVICE_UNAVAILABLE = 503;
}
?>
```

---

## BƯỚC 3: Tạo Router Tập Trung

### 3.1 Routes Definition - `src/Api/Routes.php`

```php
<?php
return [
    // ============ GUESTS ============
    ['GET', '/api/v1/guests', 'GuestController', 'index'],
    ['GET', '/api/v1/guests/:id', 'GuestController', 'show'],
    ['POST', '/api/v1/guests', 'GuestController', 'store'],
    ['PUT', '/api/v1/guests/:id', 'GuestController', 'update'],
    ['DELETE', '/api/v1/guests/:id', 'GuestController', 'destroy'],

    // ============ BOOKINGS ============
    ['GET', '/api/v1/bookings', 'BookingController', 'index'],
    ['GET', '/api/v1/bookings/:id', 'BookingController', 'show'],
    ['POST', '/api/v1/bookings', 'BookingController', 'store'],
    ['PUT', '/api/v1/bookings/:id', 'BookingController', 'update'],
    ['DELETE', '/api/v1/bookings/:id', 'BookingController', 'destroy'],

    // ============ ROOMS ============
    ['GET', '/api/v1/rooms', 'RoomController', 'index'],
    ['GET', '/api/v1/rooms/:id', 'RoomController', 'show'],
    ['POST', '/api/v1/rooms', 'RoomController', 'store'],
    ['PUT', '/api/v1/rooms/:id', 'RoomController', 'update'],
    ['DELETE', '/api/v1/rooms/:id', 'RoomController', 'destroy'],

    // ============ ROOM TYPES ============
    ['GET', '/api/v1/room-types', 'RoomTypeController', 'index'],
    ['GET', '/api/v1/room-types/:id', 'RoomTypeController', 'show'],
    ['POST', '/api/v1/room-types', 'RoomTypeController', 'store'],
    ['PUT', '/api/v1/room-types/:id', 'RoomTypeController', 'update'],
    ['DELETE', '/api/v1/room-types/:id', 'RoomTypeController', 'destroy'],

    // ============ SERVICES ============
    ['GET', '/api/v1/services', 'ServiceController', 'index'],
    ['GET', '/api/v1/services/:id', 'ServiceController', 'show'],
    ['POST', '/api/v1/services', 'ServiceController', 'store'],
    ['PUT', '/api/v1/services/:id', 'ServiceController', 'update'],
    ['DELETE', '/api/v1/services/:id', 'ServiceController', 'destroy'],
];
?>
```

### 3.2 Router Class - `src/Shared/Http/Router.php`

```php
<?php
namespace Shared\Http;

class Router {
    private $routes = [];
    private $request;
    private $response;
    private $middleware = [];

    public function __construct(Request $request, Response $response) {
        $this->request = $request;
        $this->response = $response;
    }

    /**
     * Đăng ký middleware
     */
    public function middleware($name, callable $handler) {
        $this->middleware[$name] = $handler;
        return $this;
    }

    /**
     * Load routes từ file
     */
    public function loadRoutes($routesFile) {
        $routes = require $routesFile;
        foreach ($routes as $route) {
            $this->addRoute($route[0], $route[1], $route[2], $route[3]);
        }
        return $this;
    }

    /**
     * Thêm một route
     */
    public function addRoute($method, $path, $controller, $action) {
        $this->routes[] = [
            'method' => strtoupper($method),
            'path' => $path,
            'controller' => $controller,
            'action' => $action,
        ];
        return $this;
    }

    /**
     * Dispatch request đến controller phù hợp
     */
    public function dispatch() {
        $requestMethod = $this->request->getMethod();
        $requestPath = $this->request->getPath();

        foreach ($this->routes as $route) {
            if ($this->matchRoute($route, $requestMethod, $requestPath)) {
                return $this->handleRoute($route);
            }
        }

        return $this->response->notFound();
    }

    // ============ Private Methods ============

    private function matchRoute($route, $method, $path) {
        if ($route['method'] !== $method) {
            return false;
        }

        return $this->matchPath($route['path'], $path);
    }

    private function matchPath($pattern, $path) {
        // Chuyển /api/guests/:id thành regex
        $pattern = preg_replace('/:(\w+)/', '(?P<$1>[^/]+)', $pattern);
        $pattern = '#^' . $pattern . '$#';

        return preg_match($pattern, $path);
    }

    private function handleRoute($route) {
        $controllerName = $route['controller'];
        $action = $route['action'];

        // Tạo instance của controller
        $controllerClass = "Api\\Controllers\\$controllerName";
        
        if (!class_exists($controllerClass)) {
            return $this->response->error('Controller not found', 500);
        }

        $controller = new $controllerClass($this->request, $this->response);

        if (!method_exists($controller, $action)) {
            return $this->response->error('Action not found', 500);
        }

        return call_user_func([$controller, $action]);
    }
}
?>
```

---

## BƯỚC 4: Tạo Base API Controller

### `src/Api/BaseApiController.php`

```php
<?php
namespace Api;

use Shared\Http\Request;
use Shared\Http\Response;

class BaseApiController {
    protected $request;
    protected $response;

    public function __construct(Request $request, Response $response) {
        $this->request = $request;
        $this->response = $response;
    }

    /**
     * Lấy model
     */
    protected function model($modelName) {
        $modelClass = "Shared\\Models\\$modelName";
        if (!class_exists($modelClass)) {
            throw new \Exception("Model $modelName not found");
        }
        return new $modelClass();
    }

    /**
     * Validate dữ liệu
     */
    protected function validate($data, $rules) {
        $errors = [];
        foreach ($rules as $field => $fieldRules) {
            $value = $data[$field] ?? null;
            
            if (in_array('required', $fieldRules) && empty($value)) {
                $errors[$field] = "$field is required";
            }
            
            if (in_array('email', $fieldRules) && !empty($value) && !filter_var($value, FILTER_VALIDATE_EMAIL)) {
                $errors[$field] = "$field must be a valid email";
            }
            
            // Thêm validation rules khác...
        }
        
        if (count($errors) > 0) {
            return $this->response->validationError($errors);
        }

        return true;
    }

    /**
     * Kiểm tra authentication
     */
    protected function checkAuth() {
        $token = $this->request->header('Authorization');
        if (!$token) {
            return $this->response->unauthorized('Missing authorization token');
        }

        // Validate token (xem phần JWT bên dưới)
        // ...
    }
}
?>
```

---

## BƯỚC 5: Tạo Ví Dụ Guest Controller

### `src/Api/Controllers/GuestController.php`

```php
<?php
namespace Api\Controllers;

use Api\BaseApiController;
use Api\Resources\GuestResource;
use Shared\Models\Guest;

class GuestController extends BaseApiController {
    
    /**
     * GET /api/v1/guests
     * Lấy danh sách guests với pagination
     */
    public function index() {
        try {
            $page = $this->request->query('page', 1);
            $perPage = $this->request->query('per_page', 10);
            $search = $this->request->query('search', '');

            $guestModel = $this->model('Guest');
            $guests = $guestModel
                ->where('name', 'LIKE', "%$search%")
                ->paginate($page, $perPage);

            $total = $guestModel->where('name', 'LIKE', "%$search%")->count();

            return $this->response->paginate(
                GuestResource::collection($guests),
                $total,
                $page,
                $perPage
            );
        } catch (\Exception $e) {
            return $this->response->error($e->getMessage(), 500);
        }
    }

    /**
     * GET /api/v1/guests/:id
     * Lấy thông tin 1 guest
     */
    public function show() {
        try {
            $id = $this->getSegment(3); // /api/v1/guests/123 -> 123
            
            $guestModel = $this->model('Guest');
            $guest = $guestModel->find($id);

            if (!$guest) {
                return $this->response->notFound('Guest not found');
            }

            return $this->response->json([
                'status' => 'success',
                'data' => GuestResource::transform($guest)
            ]);
        } catch (\Exception $e) {
            return $this->response->error($e->getMessage(), 500);
        }
    }

    /**
     * POST /api/v1/guests
     * Thêm guest mới
     */
    public function store() {
        try {
            $data = $this->request->all();

            // Validate
            $this->validate($data, [
                'name' => ['required'],
                'email' => ['required', 'email'],
                'phone' => ['required'],
            ]);

            $guestModel = $this->model('Guest');
            $guest = $guestModel->create($data);

            return $this->response->json([
                'status' => 'success',
                'message' => 'Guest created successfully',
                'data' => GuestResource::transform($guest)
            ], 201);
        } catch (\Exception $e) {
            return $this->response->error($e->getMessage(), 500);
        }
    }

    /**
     * PUT /api/v1/guests/:id
     * Cập nhật guest
     */
    public function update() {
        try {
            $id = $this->getSegment(3);
            $data = $this->request->all();

            $guestModel = $this->model('Guest');
            $guest = $guestModel->find($id);

            if (!$guest) {
                return $this->response->notFound('Guest not found');
            }

            $guestModel->update($id, $data);
            $guest = $guestModel->find($id);

            return $this->response->json([
                'status' => 'success',
                'message' => 'Guest updated successfully',
                'data' => GuestResource::transform($guest)
            ]);
        } catch (\Exception $e) {
            return $this->response->error($e->getMessage(), 500);
        }
    }

    /**
     * DELETE /api/v1/guests/:id
     * Xóa guest
     */
    public function destroy() {
        try {
            $id = $this->getSegment(3);

            $guestModel = $this->model('Guest');
            if (!$guestModel->find($id)) {
                return $this->response->notFound('Guest not found');
            }

            $guestModel->delete($id);

            return $this->response->json([
                'status' => 'success',
                'message' => 'Guest deleted successfully'
            ]);
        } catch (\Exception $e) {
            return $this->response->error($e->getMessage(), 500);
        }
    }

    // ============ Helper Methods ============

    private function getSegment($index) {
        $segments = $this->request->segments();
        return $segments[$index] ?? null;
    }
}
?>
```

---

## BƯỚC 6: Tạo Resource Class

### `src/Api/Resources/GuestResource.php`

```php
<?php
namespace Api\Resources;

class GuestResource {
    
    /**
     * Transform collection
     */
    public static function collection($guests) {
        return array_map([self::class, 'transform'], $guests);
    }

    /**
     * Transform single guest
     * Lọc ra các field cần thiết, ẩn các thông tin nhạy cảm
     */
    public static function transform($guest) {
        return [
            'id' => $guest['id'] ?? $guest->id,
            'name' => $guest['name'] ?? $guest->name,
            'email' => $guest['email'] ?? $guest->email,
            'phone' => $guest['phone'] ?? $guest->phone,
            'address' => $guest['address'] ?? $guest->address,
            'created_at' => $guest['created_at'] ?? $guest->created_at,
            'updated_at' => $guest['updated_at'] ?? $guest->updated_at,
            // Không trả về password, token, v.v.
        ];
    }
}
?>
```

---

## BƯỚC 7: Entry Point Mới

### `api.php` (Mới)

```php
<?php
// Bật error reporting
error_reporting(E_ALL);
ini_set('display_errors', 1);
ini_set('log_errors', 1);

// Load config
require_once __DIR__ . '/config/database.php';

// Load autoloader hoặc require các class
// Nếu không có autoloader, require các namespaces như sau:
// require_once __DIR__ . '/src/Shared/Http/Request.php';
// require_once __DIR__ . '/src/Shared/Http/Response.php';
// require_once __DIR__ . '/src/Shared/Http/Router.php';
// ... (require tất cả files)

// Hoặc tạo simple autoloader:
spl_autoload_register(function($class) {
    $path = __DIR__ . '/src/' . str_replace('\\', '/', $class) . '.php';
    if (file_exists($path)) {
        require_once $path;
    }
});

use Shared\Http\Request;
use Shared\Http\Response;
use Shared\Http\Router;

// Tạo request & response objects
$request = new Request();
$response = new Response();

// Tạo router
$router = new Router($request, $response);

// Load routes
$router->loadRoutes(__DIR__ . '/src/Api/Routes.php');

// Dispatch request
try {
    $router->dispatch();
} catch (\Exception $e) {
    $response->error($e->getMessage(), 500);
}
?>
```

---

## BƯỚC 8: Thêm JWT Authentication (Tùy chọn nhưng Recommended)

### `src/Shared/Auth/JwtToken.php`

```php
<?php
namespace Shared\Auth;

class JwtToken {
    private static $secret = 'your-secret-key'; // Đặt trong .env
    private static $algorithm = 'HS256';

    /**
     * Tạo JWT token
     */
    public static function encode($payload) {
        $header = [
            'alg' => self::$algorithm,
            'typ' => 'JWT'
        ];

        $encodedHeader = self::urlsafeBase64Encode(json_encode($header));
        $encodedPayload = self::urlsafeBase64Encode(json_encode($payload));
        
        $signatureInput = "$encodedHeader.$encodedPayload";
        $signature = self::sign($signatureInput);
        $encodedSignature = self::urlsafeBase64Encode($signature);

        return "$encodedHeader.$encodedPayload.$encodedSignature";
    }

    /**
     * Decode JWT token
     */
    public static function decode($token) {
        $parts = explode('.', $token);
        
        if (count($parts) !== 3) {
            throw new \Exception('Invalid token format');
        }

        list($encodedHeader, $encodedPayload, $encodedSignature) = $parts;

        // Verify signature
        $signatureInput = "$encodedHeader.$encodedPayload";
        $signature = self::urlsafeBase64Decode($encodedSignature);
        $expectedSignature = self::sign($signatureInput);

        if (!hash_equals($signature, $expectedSignature)) {
            throw new \Exception('Invalid token signature');
        }

        // Decode payload
        $payload = json_decode(self::urlsafeBase64Decode($encodedPayload), true);
        
        // Kiểm tra expiry
        if (isset($payload['exp']) && $payload['exp'] < time()) {
            throw new \Exception('Token has expired');
        }

        return $payload;
    }

    private static function sign($data) {
        return hash_hmac('sha256', $data, self::$secret, true);
    }

    private static function urlsafeBase64Encode($data) {
        return rtrim(strtr(base64_encode($data), '+/', '-_'), '=');
    }

    private static function urlsafeBase64Decode($data) {
        $data = strtr($data, '-_', '+/');
        $data = str_pad($data, strlen($data) % 4, '=', STR_PAD_RIGHT);
        return base64_decode($data);
    }
}
?>
```

### `src/Api/Middleware/AuthMiddleware.php`

```php
<?php
namespace Api\Middleware;

use Shared\Auth\JwtToken;
use Shared\Http\Response;

class AuthMiddleware {
    
    public static function handle($request, $response) {
        $authHeader = $request->header('Authorization');

        if (!$authHeader) {
            return $response->unauthorized('Missing authorization header');
        }

        // Format: Bearer <token>
        $parts = explode(' ', $authHeader);
        if (count($parts) !== 2 || $parts[0] !== 'Bearer') {
            return $response->unauthorized('Invalid authorization format');
        }

        $token = $parts[1];

        try {
            $payload = JwtToken::decode($token);
            $request->user = $payload; // Lưu user vào request
            return true;
        } catch (\Exception $e) {
            return $response->unauthorized($e->getMessage());
        }
    }
}
?>
```

---

## 📝 Tóm Tắt Cải Thiện

| Thành phần | Cũ | Mới |
|-----------|----|----|
| Entry Point | api.php (đơn giản) | api.php (có Router) |
| Routing | Switch cases (ApiController) | Router class + Routes.php |
| Request Handling | Superglobals trực tiếp | Request class |
| Response Format | Hàm respond() | Response class |
| Controllers | 1 ApiController lớn | Tách từng resource (GuestController, etc.) |
| Resource Format | Raw data | Resource classes |
| Authentication | Session-based | JWT tokens |
| Validation | Không có | Có (validate method) |
| Error Handling | Cơ bản | Structured error responses |
| Middleware | Không có | Support middleware |

---

## 🚀 Tiếp Theo?

1. **Thêm Unit Tests** - Test các endpoints
2. **Thêm Logging** - Ghi lại toàn bộ requests/responses
3. **API Documentation** - OpenAPI/Swagger
4. **Rate Limiting** - Ngăn abuse
5. **Database Seeding** - Sample data để test

---

**Đó là lộ trình chi tiết để cải thiện REST API của bạn từ ~50% lên 85% tiêu chuẩn!**
