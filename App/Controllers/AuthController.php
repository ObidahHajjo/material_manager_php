<?php

namespace App\Controllers;

use App\Controllers\Controller;
use App\Eloquents\Auth;
use App\Eloquents\UserEloquent;
use App\Exceptions\NotFoundException;
use App\Exceptions\ValidationException;
use App\Helpers\Mailer;
use App\Requests\Auth\ForgetPasswordRequest;
use App\Requests\Auth\LoginRequest;
use App\Requests\Auth\ResetPasswordRequest;
use Config\Database;
use Exception;

class AuthController extends Controller
{
    private Auth $auth;
    private UserEloquent $userEloquent;

    public function __construct()
    {
        parent::__construct();
        $this->auth = new Auth();
        $this->userEloquent = new UserEloquent();
    }


    public function display()
    {
        if (Database::checkConnection()) {
            $this->view('ErrorDatabaseView');
            exit;
        }

        $this->session->delete('isLoggedIn');
        $this->session->delete('user');
        $this->session->delete('isPro');
        $this->view('Auth/login', [
            'title' => 'Connection',
        ]);
    }

    public function login()
    {
        $request = new LoginRequest();
        $data = $request->validated();
        if (empty($data['email']) || empty($data['password'])) {
            throw new ValidationException("email or password is empty!", null, 403, "/login");
        }

        $email = trim($data["email"]);
        $password = trim($data['password']);

        $user = $this->auth->loginVerify($email, $password);
        if (!$user) {
            http_response_code(404);
            echo json_encode(["error" => "username or password are incorrect !"]);
            exit();
        }


        $this->session->set('user', $user);
        $this->session->set('isPro', true);
        $this->session->set('isLoggedIn', true);
        http_response_code(200);
        echo json_encode(["success" => "Login successful!"]);
        exit();
    }

    public function logOut()
    {
        $_SESSION = [];
        $this->session->destroy();

        $this->redirectTo('/');
    }

    public function forgetPasswordFrom()
    {
        $this->view('Auth/resetPassword', [
            'title' => "Reset Password"
        ]);
    }

    public function forgotPassword(): void
    {
        header('Content-Type: application/json');
        $request = new ForgetPasswordRequest();
        $data = $request->validated();

        $email = $data['email'];

        if (!$email || !filter_var($email, FILTER_VALIDATE_EMAIL)) {
            http_response_code(400);
            echo json_encode(['success' => false, 'message' => 'Invalid Email.']);
            return;
        }
        $user = $this->userEloquent->findByEmail($email);

        if (!$user || empty($user)) {
            http_response_code(404);
            echo json_encode(['success' => false, 'message' => 'No user found with this email']);
            return;
        }

        $token = bin2hex(random_bytes(32));
        $expiresAt = date('Y-m-d H:i:s', time() + 3600);

        $isCreated = $this->userEloquent->createReset(data: [
            'email' => $email,
            'token' => $token,
            'expire_at' => $expiresAt
        ]);
        if (!$isCreated) {
            http_response_code(404);
            echo json_encode(['success' => false, 'message' => 'Failed to create password reset token']);
            return;
        }

        $resetLink = "localhost:8080/reset-password/{$token}";
        $mailer = new Mailer();
        $isSend = $mailer->send($email, "Reset your password", "Click on this link to reset your password: ", $resetLink);
        if (!$isSend) {
            http_response_code(404);
            echo json_encode(['success' => false, 'message' => 'Failed to send the mail']);
            return;
        }

        echo json_encode([
            'success' => true,
            'message' => 'A reset link has been sent to your email address.'
        ]);
    }

    public function showResetForm(string $token): void
    {

        $reset = $this->userEloquent->getResetByToken($token);
        if (!$reset)  throw new NotFoundException();

        $this->view('Auth/newPassword', [
            'token' => $token,
            'title' => 'Reset password'
        ]);
    }

    public function resetPassword(): void
    {
        $request = new ResetPasswordRequest();
        $data = $request->validated();
        $token = $data['token'] ?? null;
        $password = $data['password'] ?? null;
        $confirm = $data['password_confirmation'] ?? null;

        if (!$token) {
            http_response_code(400);
            echo json_encode(['success' => false, 'message' => 'Invalid Toekn.']);
            return;
        } else if (!$password || !$confirm) {
            http_response_code(400);
            echo json_encode(['success' => false, 'message' => 'Invalid Password.']);
            return;
        } else if (!$password == $confirm) {
            http_response_code(400);
            echo json_encode(['success' => false, 'message' => 'The passwords dosent match!']);
            return;
        }


        $reset = $this->userEloquent->getResetByToken($token);

        if (!$reset) {
            http_response_code(404);
            echo json_encode(['success' => false, 'message' => 'Reset not found!']);
            return;
        };

        $email = $reset['email'];
        $user = $this->userEloquent->findByEmail($email);
        if (!$user) {
            http_response_code(404);
            echo json_encode(['success' => false, 'message' => 'User not found!']);
            return;
        }

        $password =  password_hash($password, PASSWORD_BCRYPT);
        $updatedUser = $this->userEloquent->update([
            "email" => $email,
            "username" => $user['username'],
            "password" => $password,
            "role" => $user['role'],
            "id" => $user['id']
        ]);

        if (!$updatedUser) {
            http_response_code(500);
            echo json_encode(['success' => false, 'message' => 'Error while updating the password!']);
            return;
        }

        $isDeleted = $this->userEloquent->deleteReset($email);
        if (!$isDeleted) {
            http_response_code(500);
            echo json_encode(['success' => false, 'message' => 'Error while deleting the reset']);
            return;
        }

        http_response_code(200);
        echo json_encode(['success' => true, 'message' => 'Password reset successfully. Now, You can continue.']);
        return;
    }
}
