<?php

namespace App\Controllers;

use App\Repositories\FactionRepository;
use App\Utils\Response;

class FactionController
{
    private $repository;

    public function __construct(FactionRepository $repository)
    {
        $this->repository = $repository;
    }

    public function index()
    {
        try {
            $factions = $this->repository->getAll();
            return Response::send($factions);
        } catch (\Exception) {
            return Response::send(['error' => 'Failed to retrieve factions'], 500);
        }
    }

    public function show($id)
    {
        try {
            $faction = $this->repository->getById($id);
            if (!$faction) {
                return Response::send(['error' => 'Faction not found'], 404);
            }
            return Response::send($faction);
        } catch (\Exception) {
            return Response::send(['error' => 'Failed to retrieve faction'], 500);
        }
    }

    public function store()
    {
        try {
            $data = json_decode(file_get_contents('php://input'), true);

            if (!$this->validateFactionData($data)) {
                return Response::send(['error' => 'Invalid faction data'], 400);
            }

            $faction = $this->repository->store($data);

            return Response::send($faction, 201);
        } catch (\Exception) {
            return Response::send(['error' => 'Failed to create faction'], 500);
        }
    }

    public function update($id)
    {
        try {
            $data = json_decode(file_get_contents('php://input'), true);

            if (!$this->validateFactionData($data)) {
                return Response::send(['error' => 'Invalid faction data'], 400);
            }

            $faction = $this->repository->update($id, $data);

            if (!$faction) {
                return Response::send(['error' => 'Faction not found'], 404);
            }

            return Response::send($faction, 200);
        } catch (\Exception) {
            return Response::send(['error' => 'Failed to update faction'], 500);
        }
    }

    public function destroy($id)
    {
        try {
            $result = $this->repository->delete($id);

            if (!$result) {
                return Response::send(['error' => 'Faction not found'], 404);
            }

            return Response::send(['message' => 'Faction successfully deleted'], 200);
        } catch (\Exception) {
            return Response::send(['error' => 'Failed to delete faction'], 500);
        }
    }

    private function validateFactionData($data)
    {
        $requiredFields = ['faction_name', 'description'];
        foreach ($requiredFields as $field) {
            if (!isset($data[$field]) || empty($data[$field])) {
                return false;
            }
        }
        return true;
    }
}