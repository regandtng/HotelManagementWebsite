<?php
namespace Api\Controllers;

use Api\BaseApiController;
use Api\Resources\ServiceResource;

class ServiceController extends BaseApiController {
    
    public function index() {
        try {
            $page = $this->getPage();
            $perPage = $this->getPerPage();

            $serviceModel = $this->model('Service');
            $services = $serviceModel->paginate($page, $perPage);
            $total = $serviceModel->count();

            return $this->response->paginate(ServiceResource::collection($services), $total, $page, $perPage);
        } catch (\Exception $e) {
            $this->logError('Error in ServiceController::index', $e);
            return $this->response->error($e->getMessage(), 500);
        }
    }

    public function show() {
        try {
            $id = $this->getId();
            if (!$id) return $this->response->error('ID is required', 400);

            $service = $this->model('Service')->find($id);
            if (!$service) return $this->response->notFound('Service not found');

            return $this->response->success(ServiceResource::transform($service));
        } catch (\Exception $e) {
            $this->logError('Error in ServiceController::show', $e);
            return $this->response->error($e->getMessage(), 500);
        }
    }

    public function store() {
        try {
            $data = $this->request->all();

            if (!$this->validate($data, [
                'name' => ['required'],
                'price' => ['required', 'numeric'],
            ])) {
                return;
            }

            $service = $this->model('Service')->create($data);
            return $this->response->created(ServiceResource::transform($service));
        } catch (\Exception $e) {
            $this->logError('Error in ServiceController::store', $e);
            return $this->response->error($e->getMessage(), 500);
        }
    }

    public function update() {
        try {
            $id = $this->getId();
            if (!$id) return $this->response->error('ID is required', 400);

            $serviceModel = $this->model('Service');
            if (!$serviceModel->find($id)) return $this->response->notFound('Service not found');

            $data = $this->request->all();
            $service = $serviceModel->update($id, $data);

            return $this->response->success(ServiceResource::transform($service));
        } catch (\Exception $e) {
            $this->logError('Error in ServiceController::update', $e);
            return $this->response->error($e->getMessage(), 500);
        }
    }

    public function destroy() {
        try {
            $id = $this->getId();
            if (!$id) return $this->response->error('ID is required', 400);

            $serviceModel = $this->model('Service');
            if (!$serviceModel->find($id)) return $this->response->notFound('Service not found');

            $serviceModel->delete($id);
            return $this->response->success(null, 'Service deleted successfully');
        } catch (\Exception $e) {
            $this->logError('Error in ServiceController::destroy', $e);
            return $this->response->error($e->getMessage(), 500);
        }
    }
}
?>
