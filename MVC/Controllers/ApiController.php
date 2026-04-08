<?php
class ApiController extends controller {
    private $method;
    private $segments;

    public function __construct() {
        $this->method = $_SERVER['REQUEST_METHOD'];
        $this->segments = $this->parsePath();
    }

    public function handle() {
        $this->setJsonHeaders();

        if (empty($this->segments)) {
            return $this->respond(200, [
                'message' => 'Hotel Management REST API',
                'resources' => [
                    'bookings',
                    'guests',
                    'rooms',
                    'room-types',
                    'services'
                ]
            ]);
        }

        $resource = $this->segments[0] ?? null;
        $id = $this->segments[1] ?? null;

        switch ($resource) {
            case 'bookings':
                $this->handleBookings($id);
                break;
            case 'guests':
                $this->handleGuests($id);
                break;
            case 'rooms':
                $this->handleRooms($id);
                break;
            case 'room-types':
                $this->handleRoomTypes($id);
                break;
            case 'services':
                $this->handleServices($id);
                break;
            default:
                $this->respond(404, ['error' => 'Resource not found']);
        }
    }

    private function parsePath() {
        $path = parse_url($_SERVER['REQUEST_URI'], PHP_URL_PATH);
        $script = $_SERVER['SCRIPT_NAME'];

        if (strpos($path, $script) === 0) {
            $path = substr($path, strlen($script));
        } elseif (!empty($_SERVER['PATH_INFO'])) {
            $path = $_SERVER['PATH_INFO'];
        }

        $path = trim($path, '/');
        return $path === '' ? [] : array_values(array_filter(explode('/', $path)));
    }

    private function setJsonHeaders() {
        header('Content-Type: application/json; charset=utf-8');
        header('Access-Control-Allow-Origin: *');
        header('Access-Control-Allow-Methods: GET, POST, PUT, PATCH, DELETE, OPTIONS');
        header('Access-Control-Allow-Headers: Content-Type, Authorization');

        if ($this->method === 'OPTIONS') {
            http_response_code(204);
            exit();
        }
    }

    private function respond($status, $data) {
        http_response_code($status);
        echo json_encode($data, JSON_UNESCAPED_UNICODE);
        exit();
    }

    private function getRequestBody() {
        $raw = file_get_contents('php://input');
        $data = json_decode($raw, true);
        return is_array($data) ? $data : [];
    }

    private function handleBookings($id) {
        $model = $this->model('BookingModel');

        if ($this->method === 'GET') {
            if ($id) {
                $booking = $model->getById($id);
                if (!$booking) {
                    return $this->respond(404, ['error' => 'Booking not found']);
                }
                return $this->respond(200, $booking);
            }
            return $this->respond(200, $model->getAll());
        }

        if ($this->method === 'POST') {
            $data = $this->getRequestBody();
            $required = ['NgayNhanPhong', 'NgayTraPhong', 'MaKhachHang', 'MaLoaiPhong'];
            foreach ($required as $field) {
                if (empty($data[$field])) {
                    return $this->respond(422, ['error' => "Thiếu trường: $field"]);
                }
            }

            $roomTypeModel = $this->model('RoomTypeModel');
            $available = $roomTypeModel->countAvailableRooms($data['MaLoaiPhong']);
            if ($available <= 0) {
                return $this->respond(409, ['error' => 'Không còn phòng trống cho loại phòng này']);
            }

            $date1 = new DateTime($data['NgayNhanPhong']);
            $date2 = new DateTime($data['NgayTraPhong']);
            $days = max(1, $date1->diff($date2)->days);
            $roomType = $roomTypeModel->getById($data['MaLoaiPhong']);
            if (!$roomType) {
                return $this->respond(404, ['error' => 'Loại phòng không tồn tại']);
            }

            $total = $days * ($roomType['GiaPhong'] ?? 0);

            $payload = [
                'NgayDatPhong' => date('Y-m-d'),
                'NgayNhanPhong' => $data['NgayNhanPhong'],
                'NgayTraPhong' => $data['NgayTraPhong'],
                'MaKhachHang' => $data['MaKhachHang'],
                'MaLoaiPhong' => $data['MaLoaiPhong'],
                'GhiChu' => $data['GhiChu'] ?? '',
                'ThoiGianLuuTru' => $days,
                'SoTienDatPhong' => $total
            ];

            if ($model->createBooking($payload)) {
                return $this->respond(201, [
                    'message' => 'Booking created',
                    'total_amount' => $total,
                    'booking' => $payload
                ]);
            }

            return $this->respond(500, ['error' => 'Không thể tạo booking']);
        }

        if (in_array($this->method, ['PUT', 'PATCH'])) {
            if (!$id) {
                return $this->respond(400, ['error' => 'Booking ID is required']);
            }
            $data = $this->getRequestBody();
            if (empty($data['TrangThai'])) {
                return $this->respond(422, ['error' => 'Thiếu trường: TrangThai']);
            }
            if ($model->updateStatus($id, $data['TrangThai'])) {
                return $this->respond(200, ['message' => 'Booking status updated']);
            }
            return $this->respond(500, ['error' => 'Không thể cập nhật trạng thái']);
        }

        if ($this->method === 'DELETE') {
            if (!$id) {
                return $this->respond(400, ['error' => 'Booking ID is required']);
            }
            $roomModel = $this->model('RoomModel');
            $booking = $model->getById($id);
            if (!$booking) {
                return $this->respond(404, ['error' => 'Booking not found']);
            }
            $assignedRooms = $model->getAssignedRooms($id);
            if (!$model->updateStatus($id, 'Cancelled')) {
                return $this->respond(500, ['error' => 'Không thể hủy booking']);
            }
            foreach ($assignedRooms as $room) {
                $roomModel->updateAvailability($room['MaPhong'], 'Yes');
            }
            return $this->respond(200, ['message' => 'Booking cancelled']);
        }

        $this->respond(405, ['error' => 'Method not allowed']);
    }

    private function handleGuests($id) {
        $model = $this->model('GuestModel');

        if ($this->method === 'GET') {
            if ($id) {
                $guest = $model->getGuestById($id);
                if (!$guest) {
                    return $this->respond(404, ['error' => 'Guest not found']);
                }
                return $this->respond(200, $guest);
            }
            return $this->respond(200, $model->getAll());
        }

        if ($this->method === 'POST') {
            $data = $this->getRequestBody();
            $required = ['TenKhachHang', 'HoKhachHang', 'EmailKhachHang', 'SoDienThoaiKhachHang', 'CMND_CCCDKhachHang', 'DiaChi', 'MatKhau'];
            foreach ($required as $field) {
                if (empty($data[$field])) {
                    return $this->respond(422, ['error' => "Thiếu trường: $field"]);
                }
            }

            if ($model->createGuest($data)) {
                return $this->respond(201, ['message' => 'Guest created']);
            }
            return $this->respond(500, ['error' => 'Không thể tạo khách hàng']);
        }

        if (in_array($this->method, ['PUT', 'PATCH'])) {
            if (!$id) {
                return $this->respond(400, ['error' => 'Guest ID is required']);
            }
            $data = $this->getRequestBody();
            if ($model->update($id, $data)) {
                return $this->respond(200, ['message' => 'Guest updated']);
            }
            return $this->respond(500, ['error' => 'Không thể cập nhật khách hàng']);
        }

        if ($this->method === 'DELETE') {
            if (!$id) {
                return $this->respond(400, ['error' => 'Guest ID is required']);
            }
            if ($model->delete($id)) {
                return $this->respond(200, ['message' => 'Guest deleted']);
            }
            return $this->respond(500, ['error' => 'Không thể xóa khách hàng']);
        }

        $this->respond(405, ['error' => 'Method not allowed']);
    }

    private function handleRooms($id) {
        $model = $this->model('RoomModel');

        if ($this->method === 'GET') {
            if ($id) {
                $room = $model->getById($id);
                if (!$room) {
                    return $this->respond(404, ['error' => 'Room not found']);
                }
                return $this->respond(200, $room);
            }
            return $this->respond(200, $model->getAllArray());
        }

        if ($this->method === 'POST') {
            $data = $this->getRequestBody();
            $required = ['MaPhong', 'SoPhong', 'MaLoaiPhong', 'KhaDung'];
            foreach ($required as $field) {
                if (empty($data[$field])) {
                    return $this->respond(422, ['error' => "Thiếu trường: $field"]);
                }
            }
            if ($model->insert($data['MaPhong'], $data['SoPhong'], $data['MaLoaiPhong'], $data['KhaDung'])) {
                return $this->respond(201, ['message' => 'Room created']);
            }
            return $this->respond(500, ['error' => 'Không thể tạo phòng']);
        }

        if (in_array($this->method, ['PUT', 'PATCH'])) {
            if (!$id) {
                return $this->respond(400, ['error' => 'Room ID is required']);
            }
            $data = $this->getRequestBody();
            if ($model->updateRoomInfo($id, $data['SoPhong'] ?? '', $data['MaLoaiPhong'] ?? '', $data['KhaDung'] ?? '')) {
                return $this->respond(200, ['message' => 'Room updated']);
            }
            return $this->respond(500, ['error' => 'Không thể cập nhật phòng']);
        }

        if ($this->method === 'DELETE') {
            if (!$id) {
                return $this->respond(400, ['error' => 'Room ID is required']);
            }
            if ($model->delete($id)) {
                return $this->respond(200, ['message' => 'Room deleted']);
            }
            return $this->respond(500, ['error' => 'Không thể xóa phòng']);
        }

        $this->respond(405, ['error' => 'Method not allowed']);
    }

    private function handleRoomTypes($id) {
        $model = $this->model('RoomTypeModel');

        if ($this->method === 'GET') {
            if ($id) {
                $roomType = $model->getById($id);
                if (!$roomType) {
                    return $this->respond(404, ['error' => 'Room type not found']);
                }
                return $this->respond(200, $roomType);
            }
            return $this->respond(200, $model->getAllArray());
        }

        $this->respond(405, ['error' => 'Method not allowed']);
    }

    private function handleServices($id) {
        $model = $this->model('ServiceModel');

        if ($this->method === 'GET') {
            if ($id) {
                $service = $model->getById($id);
                if (!$service) {
                    return $this->respond(404, ['error' => 'Service not found']);
                }
                return $this->respond(200, $service);
            }
            return $this->respond(200, $model->getAll());
        }

        if ($this->method === 'POST') {
            $data = $this->getRequestBody();
            $required = ['TenDichVu', 'MoTaDichVu', 'ChiPhiDichVu'];
            foreach ($required as $field) {
                if (!isset($data[$field])) {
                    return $this->respond(422, ['error' => "Thiếu trường: $field"]);
                }
            }
            if ($model->insert($data['TenDichVu'], $data['MoTaDichVu'], $data['ChiPhiDichVu'])) {
                return $this->respond(201, ['message' => 'Service created']);
            }
            return $this->respond(500, ['error' => 'Không thể tạo dịch vụ']);
        }

        if ($this->method === 'DELETE') {
            if (!$id) {
                return $this->respond(400, ['error' => 'Service ID is required']);
            }
            if ($model->delete($id)) {
                return $this->respond(200, ['message' => 'Service deleted']);
            }
            return $this->respond(500, ['error' => 'Không thể xóa dịch vụ']);
        }

        $this->respond(405, ['error' => 'Method not allowed']);
    }
}
