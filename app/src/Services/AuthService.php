<?php

namespace App\Services;

use App\Repositories\UserRepository;
use Firebase\JWT\JWT;
use Firebase\JWT\Key;

class AuthService
{
    private $userRepository;
    private $secretKey;

    public function __construct(UserRepository $userRepository)
    {
        $this->userRepository = $userRepository;
        $this->secretKey = $_ENV['JWT_SECRET'] ?? 'secret';
    }

    public function register($username, $email, $password)
    {
        if ($this->userRepository->findByUsername($username) || $this->userRepository->findByEmail($email)) {
            return null;
        }

        $userData = [
            'username' => $username,
            'email' => $email,
            'password_hash' => password_hash($password, PASSWORD_DEFAULT)
        ];

        $user = $this->userRepository->create($userData);

        return $user ? $this->generateToken($user) : null;
    }

    public function authenticate($username, $password)
    {
        $user = $this->userRepository->findByUsername($username);
        if ($user && password_verify($password, $user->getPasswordHash())) {
            return $this->generateToken($user);
        }
        return null;
    }

    private function generateToken($user)
    {
        $payload = [
            'user_id' => $user->getId(),
            'username' => $user->getUsername(),
            'exp' => time() + 3600
        ];

        return JWT::encode($payload, $this->secretKey, 'HS256');
    }

    public function validateToken($token)
    {
        try {
            $decoded = JWT::decode($token, new Key($this->secretKey, 'HS256'));
            return $decoded;
        } catch (\Exception $e) {
            return null;
        }
    }
}