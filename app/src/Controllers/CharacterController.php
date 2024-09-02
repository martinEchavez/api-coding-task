<?php

namespace App\Controllers;

use App\Repositories\CharacterRepository;
use App\Utils\Response;

class CharacterController
{
    private $repository;

    public function __construct(CharacterRepository $repository)
    {
        $this->repository = $repository;
    }

    public function index()
    {
        try {
            $characters = $this->repository->getAll();
            return Response::send($characters);
        } catch (\Exception) {
            return Response::send(['error' => 'Failed to retrieve characters'], 500);
        }
    }

    public function show($id)
    {
        try {
            $character = $this->repository->getById($id);
            if (!$character) {
                return Response::send(['error' => 'Character not found'], 404);
            }
            return Response::send($character);
        } catch (\Exception) {
            return Response::send(['error' => 'Failed to retrieve character'], 500);
        }
    }

    public function store()
    {
        try {
            $data = json_decode(file_get_contents('php://input'), true);

            if (!$this->validateCharacterData($data)) {
                return Response::send(['error' => 'Invalid character data'], 400);
            }

            $character = $this->repository->store($data);

            return Response::send($character, 201);
        } catch (\Exception) {
            return Response::send(['error' => 'Failed to create character'], 500);
        }
    }

    public function update($id)
    {
        try {
            $data = json_decode(file_get_contents('php://input'), true);

            if (!$this->validateCharacterData($data)) {
                return Response::send(['error' => 'Invalid character data'], 400);
            }

            $character = $this->repository->update($id, $data);

            if (!$character) {
                return Response::send(['error' => 'Character not found'], 404);
            }

            return Response::send($character, 200);
        } catch (\Exception) {
            return Response::send(['error' => 'Failed to update character'], 500);
        }
    }

    private function validateCharacterData($data)
    {
        $requiredFields = ['name', 'birth_date', 'kingdom', 'equipment_id', 'faction_id'];
        foreach ($requiredFields as $field) {
            if (!isset($data[$field]) || empty($data[$field])) {
                return false;
            }
        }
        return true;
    }

    public function destroy($id)
    {
        try {
            $result = $this->repository->delete($id);

            if (!$result) {
                return Response::send(['error' => 'Character not found'], 404);
            }

            return Response::send(['message' => 'Character successfully deleted'], 200);
        } catch (\Exception) {
            return Response::send(['error' => 'Failed to delete character'], 500);
        }
    }
}