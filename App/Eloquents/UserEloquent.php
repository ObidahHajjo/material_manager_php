<?php

namespace App\Eloquents;

use App\Factory\UserFactory;
use PDO;
use Config\Database;
use App\Models\Praticien;
use App\Models\User;
use PDOException;

class UserEloquent
{
    private ?PDO $db;

    /**
     * Summary of __construct
     */
    public function __construct()
    {
        $this->db = Database::getInstance();
    }

    // SQL Queries
    const UPDATE_PASSWORD_ALGORYTHME_SQL = "UPDATE users SET password = :password WHERE id = :id";
    const FIND_BY_EMAIL_SQL = "SELECT * FROM users WHERE email = :email LIMIT 1";
    const FIND_BY_ID_SQL = "SELECT * FROM users WHERE id = :id LIMIT 1";
    const CREATE_SQL = "INSERT INTO users(username,email,password,role) VALUES(:username, :email, :password, :role)";
    const UPDATE_SQL = "UPDATE users SET username = :username, email = :email, role = :role, last_login = :last_login ";

    /**
     * Get a User by his email from the db
     * @param string $email
     * @return array|null
     */
    public function findByEmail(string $email): ?array
    {
        $stmt = $this->db->prepare(self::FIND_BY_EMAIL_SQL);

        $stmt->execute([':email' => $email]);

        $praticien = $stmt->fetch(PDO::FETCH_ASSOC);

        if (!$praticien) return null;
        return $praticien;
    }

    /**
     * Get a user by his id
     * @param int $id
     * @return User|null
     */
    public function findById(int $id): ?User
    {
        $stmt = $this->db->prepare(self::FIND_BY_ID_SQL);
        $stmt->execute([
            ':id' => $id,
        ]);
        $user = $stmt->fetch(PDO::FETCH_ASSOC);
        if (!$user) return null;
        return UserFactory::create($user);
    }

    /**
     * Create a new user
     * @param array $data
     * @return \App\Models\User
     */
    public function create(array $data): ?User
    {
        $password = $data['password'];
        $hashedPassword = password_hash($password, PASSWORD_DEFAULT);
        $data['password'] = $hashedPassword;
        $stmt = $this->db->prepare(self::CREATE_SQL);
        $stmt->execute($data);
        $id = $this->db->lastInsertId();
        $data['id'] = $id;
        return UserFactory::create($data);
    }

    /**
     * Update practitioner password algorythme
     * @param string $newHashedPassword
     * @param int $idPraticien
     * @return bool
     */
    public function updatePasswordAlgorythme(string $newHashedPassword, int $idUser): bool
    {
        $stmt = $this->db->prepare(self::UPDATE_PASSWORD_ALGORYTHME_SQL);

        return $stmt->execute([
            ':password' => $newHashedPassword,
            ':id' => $idUser,
        ]);
    }

    /**
     * Update a practitioner fields
     * @param array $data
     * @return User|null
     */
    public function update(array $data): ?User
    {
        try {
            $sql = self::UPDATE_SQL;
            if (isset($data['password'])) $sql .= ", password = :password ";
            $sql .= "WHERE id = :id";
            $stmt = $this->db->prepare($sql);
            $data['last_login'] = $data['last_login'] ? $data['last_login']->format('Y-m-d H:i:s') : null;
            $stmt->execute($data);
            $isValid = $stmt->rowCount() > 0;
            return UserFactory::create($data);
        } catch (\Exception $e) {
            throw new \Exception($e->getMessage());
        }
    }


    public function createReset(array $data): bool
    {
        $sql = "INSERT INTO password_resets(email, token) VALUES(:email, :token);";
        try {
            $stmt = $this->db->prepare($sql);
            $stmt->execute($data);
            return $stmt->rowCount() > 0;
        } catch (PDOException $e) {
            throw new PDOException($e->getMessage());
        }
    }

    public function getResetByToken(string $token): ?array
    {
        $sql = "SELECT * FROM password_resets WHERE token = :token AND expire_at > NOW() AND created_at > NOW() - INTERVAL 1 HOUR LIMIT 1;";
        try {
            $stmt = $this->db->prepare($sql);
            $expireAt =  date('Y-m-d H:i:s');
            $stmt->bindParam('token', $token, PDO::PARAM_STR);
            $stmt->execute();
            $data = $stmt->fetch(PDO::FETCH_ASSOC);
            if (!$data || empty($data)) return null;
            return $data;
        } catch (PDOException $e) {
            file_put_contents(__DIR__ . "/../../logs/app.log", data: $e->getMessage());
            throw new PDOException($e->getMessage());
        }
    }

    public function deleteReset(string $email): bool
    {
        $sql = "DELETE FROM password_resets WHERE email = :email;";
        try {
            $stmt = $this->db->prepare($sql);
            $stmt->bindParam('email', $email);
            $stmt->execute();
            return $stmt->rowCount() > 0;
        } catch (PDOException $e) {
            throw new PDOException($e->getMessage());
        }
    }
}
