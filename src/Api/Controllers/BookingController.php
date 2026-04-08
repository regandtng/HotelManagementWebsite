<?php
namespace Api\Controllers;

use Api\BaseApiController;
use Api\Resources\BookingResource;

class BookingController extends BaseApiController {
    
    public function index() {
        try {
            $page = $this->getPage();
            $perPage = $this->getPerPage();
            $status = $this->request->query('status');

            $bookingModel = $this->model('Booking');
            
            if ($status) {
                $bookings = $bookingModel->getByStatus($status, $page, $perPage);
                $total = count($bookingModel->getByStatus($status));
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

            if (!$this->validate($data, [
                'guest_id' => ['required', 'numeric'],
                'room_id' => ['required', 'numeric'],
                'check_in' => ['required'],
                'check_out' => ['required'],
            ])) {
                return;
            }

            $booking = $this->model('Booking')->create($data);
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
            $booking = $bookingModel->update($id, $data);

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
