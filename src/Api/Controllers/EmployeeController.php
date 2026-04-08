<?php
namespace Api\Controllers;

use Api\BaseApiController;
use Api\Resources\EmployeeResource;

class EmployeeController extends BaseApiController {
    public function index() {
        try {
            $page = $this->getPage();
            $perPage = $this->getPerPage();
            $empModel = $this->model('Employee');
            $emps = $empModel->paginate($page, $perPage);
            $total = $empModel->count();
            return $this->response->paginate(EmployeeResource::collection($emps), $total, $page, $perPage);
        } catch (\Exception $e) {
            $this->logError('Error in EmployeeController::index', $e);
            return $this->response->error($e->getMessage(), 500);
        }
    }
    public function show() {
        try {
            $id = $this->getId();
            if (!$id) return $this->response->error('ID is required', 400);
            $emp = $this->model('Employee')->find($id);
            if (!$emp) return $this->response->notFound('Employee not found');
            return $this->response->success(EmployeeResource::transform($emp));
        } catch (\Exception $e) {
            $this->logError('Error in EmployeeController::show', $e);
            return $this->response->error($e->getMessage(), 500);
        }
    }
    public function store() {
        try {
            $data = $this->request->all();
            if (!$this->validate($data, ['name' => ['required'], 'email' => ['email']])) return;
            $emp = $this->model('Employee')->create($data);
            return $this->response->created(EmployeeResource::transform($emp));
        } catch (\Exception $e) {
            $this->logError('Error in EmployeeController::store', $e);
            return $this->response->error($e->getMessage(), 500);
        }
    }
    public function update() {
        try {
            $id = $this->getId();
            if (!$id) return $this->response->error('ID is required', 400);
            $empModel = $this->model('Employee');
            if (!$empModel->find($id)) return $this->response->notFound('Employee not found');
            $data = $this->request->all();
            $emp = $empModel->update($id, $data);
            return $this->response->success(EmployeeResource::transform($emp));
        } catch (\Exception $e) {
            $this->logError('Error in EmployeeController::update', $e);
            return $this->response->error($e->getMessage(), 500);
        }
    }
    public function destroy() {
        try {
            $id = $this->getId();
            if (!$id) return $this->response->error('ID is required', 400);
            $empModel = $this->model('Employee');
            if (!$empModel->find($id)) return $this->response->notFound('Employee not found');
            $empModel->delete($id);
            return $this->response->success(null, 'Employee deleted successfully');
        } catch (\Exception $e) {
            $this->logError('Error in EmployeeController::destroy', $e);
            return $this->response->error($e->getMessage(), 500);
        }
    }
}
?>
