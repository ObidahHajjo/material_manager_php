<?php

namespace App\Controllers;

use App\Eloquents\UserEloquent;
use App\Requests\Users\CreateUserRequest;
use App\Exceptions\UnauthorizedException;
use Exception;
use App\Helpers\Mailer;

class UserController extends Controller
{
    private UserEloquent $userEloquent;
    private Mailer $mailer;

    public function __construct()
    {
        parent::__construct();
        $this->userEloquent = new UserEloquent();
        $this->mailer = new Mailer();
    }

    public function show()
    {
        $users = $this->userEloquent->all();
        $this->view('App/userManagmentView', [
            'title' => 'User Manager',
            'users' => $users,
            'active' => 'users'
        ]);
    }

    public function create()
    {
        if (!$this->session->isset('user') || $this->session->get('user')->getRole() !== 'admin') throw new UnauthorizedException();
        try {
            $request = new CreateUserRequest();
            $data = $request->validated();
            if (!$data) throw new Exception("Fields are required !", 400);
            $password = $this->generateRandomPassword();
            $sended = $this->mailer->send($data['email'], "Your password", "Your password is: " . $password);
            if (!$sended) throw new Exception("Error while sending the mail", 500);
            $data['password'] = $password;

            if (!$this->userEloquent->create($data))  throw new Exception("Error while creating a new user", 500);
            http_response_code(200);
            echo json_encode(['status' => 'success', 'message' => 'User created']);
        } catch (Exception $e) {
            $this->jsonError($e);
        }
        exit();
    }

    public function update(int $id)
    {
        if (!$this->session->isset('user') || $this->session->get('user')->getRole() !== 'admin') throw new UnauthorizedException();
        try {
            $request = new CreateUserRequest();
            $data = $request->validated();
            if (!$data) throw new Exception("Fields are required !", 400);
            if (!$this->userEloquent->update([...$data, "id" => $id]))  throw new Exception("Error while updating a material", 500);
            http_response_code(200);
            echo json_encode(['status' => 'success', 'message' => 'Material updated']);
        } catch (Exception $e) {
            $this->jsonError($e);
        }
        exit();
    }

    public function delete(int $id)
    {
        if (!$this->session->isset('user') || $this->session->get('user')->getRole() !== 'admin') throw new UnauthorizedException();
        try {
            if (!$this->userEloquent->delete($id)) throw new Exception("Error while deleting a material", 500);
            http_response_code(200);
            echo json_encode(['status' => 'success', 'message' => 'User deleted']);
        } catch (Exception $e) {
            $this->jsonError($e);
        }
        exit();
    }

    private function jsonError(Exception $e): void
    {
        http_response_code($e->getCode() ?: 500);
        echo json_encode(['status' => 'error', 'message' => $e->getMessage()]);
        exit;
    }

    private function generateRandomPassword($length = 12)
    {
        $chars = 'abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ0123456789!@#$%^&*()-_=+[]{}<>?';
        $password = '';
        $maxIndex = strlen($chars) - 1;

        for ($i = 0; $i < $length; $i++) {
            $password .= $chars[random_int(0, $maxIndex)];
        }

        return $password;
    }
}
