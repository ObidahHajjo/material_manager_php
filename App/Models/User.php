<?php

namespace App\Models;

class User
{
    private int $id;
    private string $username;
    private string $email;
    private ?string $password;
    private string $role;

    /**
     * Summary of __construct
     * @param array $data
     */
    public function __construct(array $data)
    {
        $this->id = $data['id'];
        $this->username = $data['username'];
        $this->email = $data['email'];
        $this->role = $data['role'];
    }

    // Getters
    /**
     * Get the id of the User
     * @return int
     */
    public function getId()
    {
        return $this->id;
    }

    /**
     * Get the username of the User
     * @return string
     */
    public function getUserName(): string
    {
        return $this->username;
    }

    /**
     * Get the email of the user
     * @return string
     */
    public function getEmail(): string
    {
        return $this->email;
    }

    /**
     * Get the role of the User
     * @return string
     */
    public function getRole(): string
    {
        return $this->role;
    }

    // Setters

    /**
     * Set the id of the user
     * @param int $id
     */
    public function setId(int $id): void
    {
        $this->id = $id;
    }

    /**
     * Set the username of the user
     * @param string $userName
     */
    public function setUserName(string $userName): void
    {
        $this->username = $userName;
    }

    /**
     * Set the email of the user
     * @param string $email
     */
    public function setEmail(string $email): void
    {
        $this->email = $email;
    }

    /**
     * Set the password of the user
     * @param string $password
     */
    public function setPassword(string $password): void
    {
        $this->password = $password;
    }
}
