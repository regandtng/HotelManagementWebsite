<?php
namespace Api\Controllers;

use Api\BaseApiController;
use Shared\Auth\JWT;
use Shared\Models\Account;

class AuthController extends BaseApiController {
    /**
     * Đăng nhập và trả về JWT token
     */
    public function login() {
        try {
            $data = $this->request->all();

            if (!$this->validate($data, [
                'username' => ['required'],
                'password' => ['required']
            ])) {
                return;
            }

            $accountModel = new Account();
            $account = $accountModel->findByUsername($data['username']);

            if (!$account) {
                $this->response->unauthorized('Tên đăng nhập hoặc mật khẩu không đúng');
                return;
            }

            $isValidPassword = password_verify($data['password'], $account['MatKhau']) || $data['password'] === $account['MatKhau'];
            if (!$isValidPassword) {
                $this->response->unauthorized('Tên đăng nhập hoặc mật khẩu không đúng');
                return;
            }

            // Tạo payload cho JWT
            $payload = [
                'id' => $account['MaAdmin'],
                'username' => $account['TenDangNhap'],
                'role' => 'admin'
            ];

            $token = JWT::encode($payload);

            $this->response->success([
                'token' => $token,
                'user' => [
                    'id' => $account['MaTaiKhoan'],
                    'username' => $account['TenTaiKhoan'],
                    'role' => $account['VaiTro']
                ]
            ], 'Đăng nhập thành công');

        } catch (\Exception $e) {
            $this->logError('Login error', $e);
            $this->response->error('Lỗi đăng nhập: ' . $e->getMessage());
        }
    }

    /**
     * Lấy thông tin user hiện tại
     */
    public function me() {
        try {
            $user = JWT::validateToken();
            if (!$user) {
                $this->response->unauthorized('Token không hợp lệ');
                return;
            }

            $accountModel = new Account();
            $account = $accountModel->find($user['id']);

            if (!$account) {
                $this->response->notFound('Không tìm thấy tài khoản');
                return;
            }

            $this->response->success([
                'user' => [
                    'id' => $account['MaAdmin'],
                    'username' => $account['TenDangNhap'],
                    'role' => 'admin'
                ]
            ]);

        } catch (\Exception $e) {
            $this->logError('Get user info error', $e);
            $this->response->error('Lỗi lấy thông tin user: ' . $e->getMessage());
        }
    }

    /**
     * Đăng xuất (client-side chỉ cần xóa token)
     */
    public function logout() {
        $this->response->success([], 'Đăng xuất thành công');
    }
}