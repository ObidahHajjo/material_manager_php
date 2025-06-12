<?php

namespace App\Eloquents;

use App\Eloquents\UserEloquent;
use App\Factory\UserFactory;
use App\Models\User;

class Auth
{

    private UserEloquent $userEloquent;

    /**
     * Summary of __construct
     */
    public function __construct()
    {
        $this->userEloquent = new UserEloquent();
    }

    /**
     * login user
     * @param string $email
     * @param string $password
     * @return User|null
     */
    public function loginVerify(string $email, string $password): ?User
    {
        $user = $this->userEloquent->findByEmail($email);
        if ($user && password_verify($password, $user['password'])) {

            if (password_needs_rehash($user['password'], PASSWORD_DEFAULT)) {
                $newHashedPassword = password_hash($password, PASSWORD_DEFAULT);
                $this->userEloquent->updatePasswordAlgorythme($newHashedPassword, $user['id']);
            }
            return UserFactory::create($user);
        }
        return null;
    }
}
