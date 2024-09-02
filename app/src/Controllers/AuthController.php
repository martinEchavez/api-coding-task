<?php

namespace App\Controllers;

use App\Services\AuthService;
use App\Utils\Response;

class AuthController
{
    private $authService;

    public function __construct(AuthService $authService)
    {
        $this->authService = $authService;
    }

    public function register()
    {
        $data = json_decode(file_get_contents('php://input'), true);

        if (!isset($data['username']) || !isset($data['email']) || !isset($data['password'])) {
            return Response::json(['error' => 'Username, email and password are required'], 400);
        }

        $token = $this->authService->register($data['username'], $data['email'], $data['password']);

        if ($token) {
            return Response::json(['token' => $token], 201);
        } else {
            return Response::json(['error' => 'Username or email already exists'], 409);
        }
    }

    public function login()
    {
        $data = json_decode(file_get_contents('php://input'), true);

        if (!isset($data['username']) || !isset($data['password'])) {
            return Response::json(['error' => 'Username and password are required'], 400);
        }

        $token = $this->authService->authenticate($data['username'], $data['password']);

        if ($token) {
            return Response::json(['token' => $token]);
        } else {
            return Response::json(['error' => 'Invalid credentials'], 401);
        }
    }
}