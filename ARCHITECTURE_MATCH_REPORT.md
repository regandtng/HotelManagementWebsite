# ARCHITECTURE MATCH REPORT — ĐÃ SỬA CHO KHỚP

## 1. Mục tiêu
Tài liệu này đã được chỉnh sửa để phản ánh chính xác cấu trúc thực tế của repository `Hotel_Management_Website`.

## 2. So sánh từng tầng

| Tầng | Trách nhiệm ý tưởng | Thực tế trong repo | Khớp hay không |
|------|----------------------|--------------------|----------------|
| Model | Tương tác CSDL, business entities, data validation | `src/Shared/Models/*` chứa các model như `Guest.php`, `Booking.php`, `Room.php` | ✅ Khớp |
| View | Render HTML templates cho UI | `MVC/Views/Pages/*` chứa HTML views cho web | ✅ Khớp với phần web |
| Controller | Nhận request, gọi Model, trả View/JSON | Có `MVC/Controllers/*` cho web và `src/Api/Controllers/*` cho API | ✅ Khớp |
| Router | Ánh xạ URL → Controller::method() | `src/Shared/Http/Router.php` + `src/Api/Routes.php`; `api.php` dispatch API | ✅ Khớp, nhưng không phải `ApiRouter` |
| Middleware | Auth, RBAC, CORS, Rate Limiting | Hiện tại repo không có middleware API chuyên biệt | ❌ Không khớp |

## 3. Những điểm cần sửa lại

### 3.1. Router
- Ý tưởng: `.htaccess + ApiRouter: /api/v1/rooms → RoomController`
- Thực tế: `.htaccess` có thể route về `api.php` cho API, nhưng tracker chính xác là `src/Shared/Http/Router.php`.
- Repo không có class `ApiRouter`; route list nằm trong `src/Api/Routes.php`.

### 3.2. Middleware / Bridge
- Ý tưởng: `bridge.php: Bridge::authenticate(), requireRole()`
- Thực tế: file `bridge.php` trong repo hiện chỉ load core và thư viện; nó không làm middleware auth cho API.
- Không tồn tại `Bridge::authenticate()` trong repo hiện tại.
- API hiện tại chưa có `Auth` hoặc `RBAC` trong layer `src/Api/`.

### 3.3. Auth/RBAC
- Ý tưởng mô tả API-first với auth/Bearer token/RBAC.
- Thực tế: repository chỉ có auth session-based cho web MVC (`MVC/Core/controller.php` `requireRole()`), không có auth API.

### 3.4. View cho API
- Ý tưởng: Controller trả View/JSON
- Thực tế: `src/Api/Controllers/*` trả JSON, `MVC/Controllers/*` trả HTML. Điều này khớp nếu phân biệt web vs API.

## 4. Kết luận

### Những gì đúng với repo
- `Model` chịu trách nhiệm dữ liệu và DB.
- `Controller` là đúng, có web controller và API controller.
- `View` đúng cho phần web.
- `Router` đúng về chức năng, nhưng tên thực tế khác.

### Những gì không đúng với repo
- Không có API middleware `bridge.php` / `Bridge::authenticate()`.
- Không có `ApiRouter` class.
- Không có auth API, JWT / Bearer token, RBAC ở `src/Api`.

## 5. Gợi ý sửa lại kiến trúc

Để mô tả đúng hiện trạng repo, bạn nên viết:

- `Router` thực tế là `src/Shared/Http/Router.php` dùng route list `src/Api/Routes.php`.
- `bridge.php` hiện tại là file load core, không phải middleware auth.
- Phần auth/RBAC chỉ tồn tại ở web MVC, chưa áp dụng cho API.
- API hiện tại chỉ triển khai CRUD cơ bản cho các resource trong `src/Api`, chưa có advanced endpoint, auth API hoặc middleware bảo mật.
