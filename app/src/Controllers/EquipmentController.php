<?php

namespace App\Controllers;

use App\Repositories\EquipmentRepository;
use App\Utils\Response;

class EquipmentController
{
    private $repository;

    public function __construct(EquipmentRepository $repository)
    {
        $this->repository = $repository;
    }

    public function index()
    {
        try {
            $equipments = $this->repository->getAll();
            return Response::send($equipments);
        } catch (\Exception) {
            return Response::send(['error' => 'Failed to retrieve equipments'], 500);
        }
    }

    public function show($id)
    {
        try {
            $equipment = $this->repository->getById($id);
            if (!$equipment) {
                return Response::send(['error' => 'Equipment not found'], 404);
            }
            return Response::send($equipment);
        } catch (\Exception) {
            return Response::send(['error' => 'Failed to retrieve equipment'], 500);
        }
    }

    public function store()
    {
        try {
            $data = json_decode(file_get_contents('php://input'), true);

            if (!$this->validateEquipmentData($data)) {
                return Response::send(['error' => 'Invalid equipment data'], 400);
            }

            $equipment = $this->repository->store($data);

            return Response::send($equipment, 201);
        } catch (\Exception) {
            return Response::send(['error' => 'Failed to create equipment'], 500);
        }
    }

    public function update($id)
    {
        try {
            $data = json_decode(file_get_contents('php://input'), true);

            if (!$this->validateEquipmentData($data)) {
                return Response::send(['error' => 'Invalid equipment data'], 400);
            }

            $equipment = $this->repository->update($id, $data);

            if (!$equipment) {
                return Response::send(['error' => 'Equipment not found'], 404);
            }

            return Response::send($equipment, 200);
        } catch (\Exception) {
            return Response::send(['error' => 'Failed to update equipment'], 500);
        }
    }

    public function destroy($id)
    {
        try {
            $result = $this->repository->delete($id);

            if (!$result) {
                return Response::send(['error' => 'Equipment not found'], 404);
            }

            return Response::send(['message' => 'Equipment successfully deleted'], 200);
        } catch (\Exception) {
            return Response::send(['error' => 'Failed to delete equipment'], 500);
        }
    }

    private function validateEquipmentData($data)
    {
        $requiredFields = ['name', 'type', 'made_by'];
        foreach ($requiredFields as $field) {
            if (!isset($data[$field]) || empty($data[$field])) {
                return false;
            }
        }
        return true;
    }
}