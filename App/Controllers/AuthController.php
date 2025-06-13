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
use DateTime;
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
        ], [
            "login"
        ]);
    }

    public function login()
    {
        try {
            $request = new LoginRequest();
            $data = $request->validated();
            if (empty($data['email']) || empty($data['password'])) {
                throw new ValidationException("email or password is empty!", null, 403, "/login");
            }

            $email = trim($data["email"]);
            $password = trim($data['password']);

            $user = $this->auth->loginVerify($email, $password);
            if (!$user) throw new Exception("username or password are incorrect !", 404);

            $this->session->set('user', $user);
            $this->session->set('isPro', true);
            $this->session->set('isLoggedIn', true);
            $user = $this->userEloquent->update([
                "email" => $user->getEmail(),
                "username" => $user->getUserName(),
                "id" => $user->getId(),
                "role" => $user->getRole(),
                "last_login" => new DateTime(),
                "avatar" => $user->getAvatar()
            ]);
            if (!$user) throw new Exception("Error while updating the user", 500);
            http_response_code(200);
            echo json_encode(["success" => true, "message" => "Login successful!"]);
        } catch (Exception $e) {
            $this->jsonError($e);
        }
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
        ], [
            "login"
        ]);
    }

    public function forgotPassword(): void
    {
        try {
            header('Content-Type: application/json');
            $request = new ForgetPasswordRequest();
            $data = $request->validated();

            $email = $data['email'];

            if (!$email || !filter_var($email, FILTER_VALIDATE_EMAIL)) throw new Exception('Invalid Email.', 400);

            $user = $this->userEloquent->findByEmail($email);

            if (!$user || empty($user)) throw new Exception('No user found with this email', 404);

            $token = bin2hex(random_bytes(32));

            $isCreated = $this->userEloquent->createReset(data: [
                'email' => $email,
                'token' => $token,
            ]);

            if (!$isCreated) throw new Exception('Failed to create password reset token', 500);


            $resetLink = "localhost:8080/reset-password/{$token}";
            $mailer = new Mailer();
            $isSend = $mailer->send($email, "Reset your password", "Click on this link to reset your password: ", $resetLink);
            if (!$isSend) throw new Exception('Failed to send the mail', 500);

            echo json_encode([
                'success' => true,
                'message' => 'A reset link has been sent to your email address.'
            ]);
        } catch (Exception $e) {
            $this->jsonError($e);
        }
        exit();
    }

    public function showResetForm(string $token): void
    {

        $reset = $this->userEloquent->getResetByToken($token);
        if (!$reset)  throw new NotFoundException("The password reset link is invalid or has expired. Please request a new link to continue.", 404, "/login");

        $this->view('Auth/newPassword', [
            'token' => $token,
            'title' => 'Reset password'
        ], [
            "login"
        ]);
    }

    public function resetPassword(): void
    {
        try {
            $request = new ResetPasswordRequest();
            $data = $request->validated();
            $token = $data['token'] ?? null;
            $password = $data['password'] ?? null;
            $confirm = $data['password_confirmation'] ?? null;

            if (empty($token)) throw new Exception('Invalid Toekn.', 400);
            else if (!$password || !$confirm) throw new Exception('Invalid Password.', 400);
            else if ($password !== $confirm) throw new Exception('The passwords dosent match!', 400);

            $reset = $this->userEloquent->getResetByToken($token);

            if (!$reset) throw new Exception('Reset not found!', 400);

            $email = $reset['email'];
            $user = $this->userEloquent->findByEmail($email);
            if (!$user) throw new Exception('User not found!', 404);

            $password =  password_hash($password, PASSWORD_BCRYPT);
            $updatedUser = $this->userEloquent->update([
                "email" => $email,
                "username" => $user['username'],
                "password" => $password,
                "role" => $user['role'],
                "id" => $user['id'],
                "last_login" => $user['last_login']
            ]);

            if (!$updatedUser) throw new Exception('Error while updating the password!', 500);

            $isDeleted = $this->userEloquent->deleteReset($email);
            if (!$isDeleted) throw new Exception('Error while deleting the reset', 500);

            http_response_code(200);
            echo json_encode(['success' => true, 'message' => 'Password reset successful. You will be redirect to login page.']);
        } catch (Exception $e) {
            $this->jsonError($e);
        }
        exit();
    }

    private function jsonError(Exception $e): void
    {
        http_response_code($e->getCode() ?: 500);
        echo json_encode(["success" => false, "message" => $e->getMessage() ?: "Internal Server Error"]);
        exit;
    }
}
