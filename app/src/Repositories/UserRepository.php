<?php

namespace App\Repositories;

use App\Models\User;
use App\Config\Database;
use PDO;

class UserRepository
{
    private $db;

    public function __construct()
    {
        $this->db = Database::getInstance();
    }

    public function getAll()
    {
        $stmt = $this->db->query("SELECT * FROM users");
        $users = [];
        while ($row = $stmt->fetch(PDO::FETCH_ASSOC)) {
            $users[] = new User($row);
        }
        return $users;
    }

    public function getById($id)
    {
        $stmt = $this->db->prepare("SELECT * FROM users WHERE id = :id");
        $stmt->execute(['id' => $id]);
        $userData = $stmt->fetch(PDO::FETCH_ASSOC);
        return $userData ? new User($userData) : null;
    }

    public function findByUsername($username)
    {
        $stmt = $this->db->prepare("SELECT * FROM users WHERE username = :username");
        $stmt->execute(['username' => $username]);
        $userData = $stmt->fetch(PDO::FETCH_ASSOC);
        return $userData ? new User($userData) : null;
    }

    public function findByEmail($email)
    {
        $stmt = $this->db->prepare("SELECT * FROM users WHERE email = :email");
        $stmt->execute(['email' => $email]);
        $userData = $stmt->fetch(PDO::FETCH_ASSOC);
        return $userData ? new User($userData) : null;
    }

    public function create($data)
    {
        if ($data instanceof User) {
            $userData = [
                'username' => $data->getUsername(),
                'email' => $data->getEmail(),
                'password_hash' => $data->getPasswordHash()
            ];
        } elseif (is_array($data)) {
            $userData = $data;
        } else {
            throw new \InvalidArgumentException("Invalid data type for user creation");
        }

        $stmt = $this->db->prepare("INSERT INTO users (username, email, password_hash) VALUES (:username, :email, :password_hash)");
        $stmt->execute([
            'username' => $userData['username'],
            'email' => $userData['email'],
            'password_hash' => $userData['password_hash'] ?? $userData['password']
        ]);
        $id = $this->db->lastInsertId();
        return $this->getById($id);
    }

    public function update(User $user)
    {
        $stmt = $this->db->prepare("UPDATE users SET username = :username, email = :email, password_hash = :password_hash WHERE id = :id");
        return $stmt->execute([
            'id' => $user->getId(),
            'username' => $user->getUsername(),
            'email' => $user->getEmail(),
            'password_hash' => $user->getPasswordHash()
        ]);
    }

    public function delete($id)
    {
        $user = $this->getById($id);
        if (!$user) {
            return false;
        }

        $stmt = $this->db->prepare("DELETE FROM users WHERE id = :id");
        $stmt->execute(['id' => $id]);
        return $stmt->rowCount() > 0;
    }
}