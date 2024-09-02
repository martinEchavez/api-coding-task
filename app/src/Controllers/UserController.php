<?php

namespace App\Controllers;

use App\Models\User;
use App\Repositories\UserRepository;
use App\Utils\Response;

class UserController
{
    private $repository;

    public function __construct(UserRepository $repository)
    {
        $this->repository = $repository;
    }

    public function index()
    {
        $users = $this->repository->getAll();
        return Response::json(array_map(fn($user) => $user->toArray(), $users));
    }

    public function show($id)
    {
        $user = $this->repository->getById($id);
        if (!$user) {
            return Response::json(['error' => 'User not found'], 404);
        }
        return Response::json($user->toArray());
    }

    public function store()
    {
        $data = json_decode(file_get_contents('php://input'), true);

        if (!isset($data['username']) || !isset($data['email']) || !isset($data['password'])) {
            return Response::json(['error' => 'Username, email and password are required'], 400);
        }

        $user = new User($data);
        $user->setPasswordHash($data['password']);

        $id = $this->repository->create($user);
        return Response::json(['id' => $id, 'message' => 'User created successfully'], 201);
    }

    public function update($id)
    {
        $user = $this->repository->getById($id);
        if (!$user) {
            return Response::json(['error' => 'User not found'], 404);
        }

        $data = json_decode(file_get_contents('php://input'), true);

        if (isset($data['username'])) {
            $user->setUsername($data['username']);
        }
        if (isset($data['email'])) {
            $user->setEmail($data['email']);
        }
        if (isset($data['password'])) {
            $user->setPasswordHash($data['password']);
        }

        $success = $this->repository->update($user);
        return Response::json(['success' => $success, 'message' => 'User updated successfully']);
    }

    public function destroy($id)
    {
        $result = $this->repository->delete($id);
        if ($result === false) {
            return Response::json(['error' => 'User not found'], 404);
        }
        return Response::json(['message' => 'User deleted successfully'], 200);
    }
}