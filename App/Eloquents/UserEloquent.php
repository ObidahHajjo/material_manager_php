<?php

namespace App\Eloquents;

use App\Factory\UserFactory;
use PDO;
use Config\Database;
use App\Models\User;
use PDOException;

class UserEloquent
{

    private ?PDO $db;

    public function __construct()
    {
        $this->db = Database::getInstance();
    }

    // PostgreSQL-Compatible SQL Queries
    const FIND_ALL_SQL = "SELECT * FROM users";
    const UPDATE_PASSWORD_ALGORYTHME_SQL = "UPDATE users SET password = :password WHERE id = :id";
    const FIND_BY_EMAIL_SQL = "SELECT * FROM users WHERE email = :email LIMIT 1";
    const FIND_BY_ID_SQL = "SELECT * FROM users WHERE id = :id LIMIT 1";
    const CREATE_SQL = "INSERT INTO users(username, email, password, role) VALUES(:username, :email, :password, :role) RETURNING id";
    const UPDATE_SQL = "UPDATE users SET username = :username, email = :email, role = :role, last_login = :last_login, avatar = :avatar";
    const DELETE_SQL = "DELETE FROM users WHERE id = $1";
    const CREATE_RESET_SQL = "INSERT INTO password_resets(email, token) VALUES(:email, :token)";
    const GET_RESET_BY_TOKEN_SQL = "SELECT * FROM password_resets WHERE token = :token AND expire_at > NOW() AND created_at > NOW() - INTERVAL '1 hour' LIMIT 1";
    const DELETE_RESET_SQL = "DELETE FROM password_resets WHERE email = :email";

    /**
     * Retrieve all users from the database.
     * @return array An array of User objects
     * @throws PDOException
     */
    public function all(): array
    {
        try {
            $stmt = $this->db->query(self::FIND_ALL_SQL);
            $users = $stmt->fetchAll(PDO::FETCH_ASSOC);
            $data = [];
            foreach ($users as $user) {
                $data[] = UserFactory::create($user);
            }
            return $data;
        } catch (PDOException $e) {
            throw new PDOException($e->getMessage());
        }
    }

    /**
     * Find a user by email.
     * @param string $email
     * @return array|null Associative array of user data or null
     * @throws PDOException
     */
    public function findByEmail(string $email): ?array
    {
        try {
            $stmt = $this->db->prepare(self::FIND_BY_EMAIL_SQL);
            $stmt->execute([':email' => $email]);
            $user = $stmt->fetch(PDO::FETCH_ASSOC);
            return $user ?: null;
        } catch (PDOException $e) {
            throw new PDOException($e->getMessage());
        }
    }

    /**
     * Find a user by ID.
     * @param int $id
     * @return User|null User object or null
     * @throws PDOException
     */
    public function findById(int $id): ?User
    {
        try {
            $stmt = $this->db->prepare(self::FIND_BY_ID_SQL);
            $stmt->execute([':id' => $id]);
            $user = $stmt->fetch(PDO::FETCH_ASSOC);
            return $user ? UserFactory::create($user) : null;
        } catch (PDOException $e) {
            throw new PDOException($e->getMessage());
        }
    }

    /**
     * Create a new user.
     * @param array $data User data
     * @return User|null Created User object
     * @throws PDOException
     */
    public function create(array $data): ?User
    {
        try {
            $data['password'] = password_hash($data['password'], PASSWORD_DEFAULT);
            $stmt = $this->db->prepare(self::CREATE_SQL);
            $stmt->execute($data);
            $id = $stmt->fetchColumn();
            $data['id'] = $id;
            $data['last_login'] = null;
            $data['avatar'] = null;
            return UserFactory::create($data);
        } catch (PDOException $e) {
            throw new PDOException($e->getMessage());
        }
    }

    /**
     * Update the password hash of a user.
     * @param string $newHashedPassword
     * @param int $idUser
     * @return bool True if update was successful
     * @throws PDOException
     */
    public function updatePasswordAlgorythme(string $newHashedPassword, int $idUser): bool
    {
        try {
            $stmt = $this->db->prepare(self::UPDATE_PASSWORD_ALGORYTHME_SQL);
            return $stmt->execute([
                ':password' => $newHashedPassword,
                ':id' => $idUser,
            ]);
        } catch (PDOException $e) {
            throw new PDOException($e->getMessage());
        }
    }

    /**
     * Update user data.
     * @param array $data User data
     * @return User|null Updated User object
     * @throws PDOException
     */
    public function update(array $data): ?User
    {
        try {
            $sql = self::UPDATE_SQL;
            if (isset($data['password'])) $sql .= ", password = :password";
            $sql .= " WHERE id = :id";
            $stmt = $this->db->prepare($sql);
            $data['last_login'] = isset($data['last_login']) ? $data['last_login']->format('Y-m-d H:i:s') : null;
            $data['avatar'] = $data['avatar'] ?? null;
            $isValid = $stmt->execute($data);
            return $isValid ? UserFactory::create($data) : null;
        } catch (PDOException $e) {
            throw new PDOException($e->getMessage());
        }
    }

    /**
     * Delete a user by ID.
     * @param int $id
     * @return bool True if deletion was successful
     * @throws PDOException
     */
    public function delete(int $id): bool
    {
        try {
            $stmt = $this->db->prepare(self::DELETE_SQL);
            $stmt->execute([$id]);
            return $stmt->rowCount() > 0;
        } catch (PDOException $e) {
            throw new PDOException($e->getMessage());
        }
    }

    /**
     * Create a new password reset token.
     * @param array $data Reset data
     * @return bool True if creation was successful
     * @throws PDOException
     */
    public function createReset(array $data): bool
    {
        try {
            $stmt = $this->db->prepare(self::CREATE_RESET_SQL);
            $stmt->execute($data);
            return $stmt->rowCount() > 0;
        } catch (PDOException $e) {
            throw new PDOException($e->getMessage());
        }
    }

    /**
     * Retrieve a password reset record by token.
     * @param string $token
     * @return array|null Reset record or null
     * @throws PDOException
     */
    public function getResetByToken(string $token): ?array
    {
        try {
            $stmt = $this->db->prepare(self::GET_RESET_BY_TOKEN_SQL);
            $stmt->execute([':token' => $token]);
            $data = $stmt->fetch(PDO::FETCH_ASSOC);
            return $data ?: null;
        } catch (PDOException $e) {
            throw new PDOException($e->getMessage());
        }
    }

    /**
     * Delete a password reset record by email.
     * @param string $email
     * @return bool True if deletion was successful
     * @throws PDOException
     */
    public function deleteReset(string $email): bool
    {
        try {
            $stmt = $this->db->prepare(self::DELETE_RESET_SQL);
            $stmt->execute([':email' => $email]);
            return $stmt->rowCount() > 0;
        } catch (PDOException $e) {
            throw new PDOException($e->getMessage());
        }
    }
}
