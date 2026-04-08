<?php
namespace Api\Controllers;

use Api\BaseApiController;
use Api\Resources\RoomResource;

class RoomController extends BaseApiController {
    
    public function index() {
        try {
            $page = $this->getPage();
            $perPage = $this->getPerPage();

            $roomModel = $this->model('Room');
            $rooms = $roomModel->paginate($page, $perPage);
            $total = $roomModel->count();

            return $this->response->paginate(RoomResource::collection($rooms), $total, $page, $perPage);
        } catch (\Exception $e) {
            $this->logError('Error in RoomController::index', $e);
            return $this->response->error($e->getMessage(), 500);
        }
    }

    public function show() {
        try {
            $id = $this->getId();
            if (!$id) return $this->response->error('ID is required', 400);

            $room = $this->model('Room')->find($id);
            if (!$room) return $this->response->notFound('Room not found');

            return $this->response->success(RoomResource::transform($room));
        } catch (\Exception $e) {
            $this->logError('Error in RoomController::show', $e);
            return $this->response->error($e->getMessage(), 500);
        }
    }

    public function store() {
        try {
            $data = $this->request->all();

            if (!$this->validate($data, [
                'room_number' => ['required'],
                'room_type_id' => ['required', 'numeric'],
                'floor' => ['required', 'numeric'],
            ])) {
                return;
            }

            $room = $this->model('Room')->create($data);
            return $this->response->created(RoomResource::transform($room));
        } catch (\Exception $e) {
            $this->logError('Error in RoomController::store', $e);
            return $this->response->error($e->getMessage(), 500);
        }
    }

    public function update() {
        try {
            $id = $this->getId();
            if (!$id) return $this->response->error('ID is required', 400);

            $roomModel = $this->model('Room');
            if (!$roomModel->find($id)) return $this->response->notFound('Room not found');

            $data = $this->request->all();
            $room = $roomModel->update($id, $data);

            return $this->response->success(RoomResource::transform($room));
        } catch (\Exception $e) {
            $this->logError('Error in RoomController::update', $e);
            return $this->response->error($e->getMessage(), 500);
        }
    }

    public function destroy() {
        try {
            $id = $this->getId();
            if (!$id) return $this->response->error('ID is required', 400);

            $roomModel = $this->model('Room');
            if (!$roomModel->find($id)) return $this->response->notFound('Room not found');

            $roomModel->delete($id);
            return $this->response->success(null, 'Room deleted successfully');
        } catch (\Exception $e) {
            $this->logError('Error in RoomController::destroy', $e);
            return $this->response->error($e->getMessage(), 500);
        }
    }
}
?>
