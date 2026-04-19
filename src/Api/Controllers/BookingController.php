<?php
namespace Api\Controllers;
 
use Api\BaseApiController;
use Api\Resources\BookingResource;
 
class BookingController extends BaseApiController {
    
    public function index() {
        try {
            $status = $this->request->query('status');
            $bookingModel = $this->model('Booking');
            
            // Fix: Kiểm tra query param 'full' hoặc 'paginate'
            $getFull = $this->request->query('full') === 'true' || $this->request->query('full') === '1';
            $disablePaginate = $this->request->query('paginate') === 'false' || $this->request->query('paginate') === '0';
 
            // Nếu yêu cầu full data hoặc disable pagination
            if ($getFull || $disablePaginate) {
                if ($status) {
                    $bookings = $bookingModel->getByStatus($status, 1, 1000000);
                } else {
                    $bookings = $bookingModel->all();
                }
                return $this->response->success(BookingResource::collection($bookings));
            }
 
            // Pagination mặc định
            $page = $this->getPage();
            $perPage = $this->getPerPage();
 
            if ($status) {
                $bookings = $bookingModel->getByStatus($status, $page, $perPage);
                $total = $bookingModel->countByStatus($status); // Fix: Dùng method riêng để count
            } else {
                $bookings = $bookingModel->paginate($page, $perPage);
                $total = $bookingModel->count();
            }
 
            return $this->response->paginate(
                BookingResource::collection($bookings),
                $total,
                $page,
                $perPage
            );
        } catch (\Exception $e) {
            $this->logError('Error in BookingController::index', $e);
            return $this->response->error($e->getMessage(), 500);
        }
    }
 
    public function show() {
        try {
            $id = $this->getId();
            if (!$id) return $this->response->error('ID is required', 400);
 
            $booking = $this->model('Booking')->find($id);
            if (!$booking) return $this->response->notFound('Booking not found');
 
            return $this->response->success(BookingResource::transform($booking));
        } catch (\Exception $e) {
            $this->logError('Error in BookingController::show', $e);
            return $this->response->error($e->getMessage(), 500);
        }
    }
 
    public function store() {
        try {
            $data = $this->request->all();
 
            // Fix: Đổi tên field validation từ check_in/check_out sang check_in_date/check_out_date
            // room_id là string (DELUXE, STD, v.v.), không phải numeric
            if (!$this->validate($data, [
                'guest_id' => ['required', 'numeric'],
                'room_id' => ['required', 'string'],
                'check_in_date' => ['required'],
                'check_out_date' => ['required'],
            ])) {
                return;
            }
 
            // Kiểm tra guest_id có tồn tại không
            $guest = $this->model('Guest')->find($data['guest_id']);
            if (!$guest) {
                return $this->response->error('Guest not found', 404);
            }

            // Note: Không cần kiểm tra room_id vì nó chỉ là string reference (DELUXE, STD, v.v.)
            // Việc validate sẽ được thực hiện ở tầng database nếu có foreign key constraint

            $bookingData = [
                'MaKhachHang' => $data['guest_id'],
                'MaLoaiPhong' => $data['room_id'],
                'NgayNhanPhong' => $data['check_in_date'],  // Fix: Đổi từ check_in sang check_in_date
                'NgayTraPhong' => $data['check_out_date'],  // Fix: Đổi từ check_out sang check_out_date
                'SoTienDatPhong' => $data['total_price'] ?? 0,
                'TrangThai' => $data['status'] ?? 'Pending',
                'GhiChu' => $data['note'] ?? ''
            ];
 
            $booking = $this->model('Booking')->create($bookingData);
            return $this->response->created(BookingResource::transform($booking));
        } catch (\Exception $e) {
            $this->logError('Error in BookingController::store', $e);
            return $this->response->error($e->getMessage(), 500);
        }
    }
 
    public function update() {
        try {
            $id = $this->getId();
            if (!$id) return $this->response->error('ID is required', 400);
 
            $bookingModel = $this->model('Booking');
            if (!$bookingModel->find($id)) return $this->response->notFound('Booking not found');
 
            $data = $this->request->all();
            $bookingData = [];
            
            // Fix: Thêm hỗ trợ cả check_in_date và check_out_date
            if (isset($data['guest_id'])) $bookingData['MaKhachHang'] = $data['guest_id'];
            if (isset($data['room_id'])) $bookingData['MaLoaiPhong'] = $data['room_id'];
            
            // Hỗ trợ cả 2 format: check_in hoặc check_in_date
            if (isset($data['check_in'])) $bookingData['NgayNhanPhong'] = $data['check_in'];
            if (isset($data['check_in_date'])) $bookingData['NgayNhanPhong'] = $data['check_in_date'];
            
            if (isset($data['check_out'])) $bookingData['NgayTraPhong'] = $data['check_out'];
            if (isset($data['check_out_date'])) $bookingData['NgayTraPhong'] = $data['check_out_date'];
            
            if (isset($data['total_price'])) $bookingData['SoTienDatPhong'] = $data['total_price'];
            if (isset($data['status'])) $bookingData['TrangThai'] = $data['status'];
            if (isset($data['note'])) $bookingData['GhiChu'] = $data['note'];
 
            $booking = $bookingModel->update($id, $bookingData);
 
            return $this->response->success(BookingResource::transform($booking));
        } catch (\Exception $e) {
            $this->logError('Error in BookingController::update', $e);
            return $this->response->error($e->getMessage(), 500);
        }
    }
 
    public function destroy() {
        try {
            $id = $this->getId();
            if (!$id) return $this->response->error('ID is required', 400);
 
            $bookingModel = $this->model('Booking');
            if (!$bookingModel->find($id)) return $this->response->notFound('Booking not found');
 
            $bookingModel->delete($id);
            return $this->response->success(null, 'Booking deleted successfully');
        } catch (\Exception $e) {
            $this->logError('Error in BookingController::destroy', $e);
            return $this->response->error($e->getMessage(), 500);
        }
    }
}
?>