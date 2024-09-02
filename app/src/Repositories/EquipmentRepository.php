<?php

namespace App\Repositories;

use App\Models\Equipment;
use App\Config\Database;
use PDO;
use PDOException;

class EquipmentRepository
{
    private PDO $db;

    public function __construct()
    {
        $this->db = Database::getInstance();
    }

    /**
     * @return array
     * @throws \Exception
     */
    public function getAll(): array
    {
        try {
            $query = "SELECT id, name, type, made_by FROM equipments";
            $stmt = $this->db->prepare($query);
            $stmt->execute();
            $equipmentsData = $stmt->fetchAll(PDO::FETCH_ASSOC);

            $equipments = [];
            foreach ($equipmentsData as $equipmentData) {
                $equipment = new Equipment($equipmentData);
                $equipments[] = $equipment->toArray();
            }
            return $equipments;
        } catch (PDOException $e) {
            $this->logError('getAll', $e);
            throw new \Exception("An error occurred while fetching equipments.");
        }
    }

    /**
     * @param int $id
     * @return array|null
     * @throws \Exception
     */
    public function getById(int $id): ?array
    {
        try {
            $query = "SELECT id, name, type, made_by FROM equipments WHERE id = :id";
            $stmt = $this->db->prepare($query);
            $stmt->bindValue(':id', $id, PDO::PARAM_INT);
            $stmt->execute();

            $equipmentData = $stmt->fetch(PDO::FETCH_ASSOC);

            if (!$equipmentData) {
                return null;
            }

            $equipment = new Equipment($equipmentData);
            return $equipment->toArray();
        } catch (PDOException $e) {
            $this->logError('getById', $e);
            throw new \Exception("An error occurred while fetching the equipment.");
        }
    }

    /**
     * @param array $data
     * @return array
     * @throws \Exception
     */
    public function store(array $data): array
    {
        try {
            $query = "INSERT INTO equipments (name, type, made_by) 
                      VALUES (:name, :type, :made_by)";

            $stmt = $this->db->prepare($query);
            $stmt->execute([
                ':name' => $data['name'],
                ':type' => $data['type'],
                ':made_by' => $data['made_by']
            ]);

            $id = $this->db->lastInsertId();

            return $this->getById($id);
        } catch (PDOException $e) {
            $this->logError('store', $e);
            throw new \Exception("An error occurred while storing the equipment.");
        }
    }

    /**
     * @param int $id
     * @param array $data
     * @return array|null
     * @throws \Exception
     */
    public function update(int $id, array $data): ?array
    {
        try {
            $query = "UPDATE equipments 
                      SET name = :name, type = :type, made_by = :made_by 
                      WHERE id = :id";

            $stmt = $this->db->prepare($query);
            $stmt->execute([
                ':id' => $id,
                ':name' => $data['name'],
                ':type' => $data['type'],
                ':made_by' => $data['made_by']
            ]);

            if ($stmt->rowCount() === 0) {
                return null;
            }

            return $this->getById($id);
        } catch (PDOException $e) {
            $this->logError('update', $e);
            throw new \Exception("An error occurred while updating the equipment.");
        }
    }

    /**
     * @param int $id
     * @return bool
     * @throws \Exception
     */
    public function delete(int $id): bool
    {
        try {
            $query = "DELETE FROM equipments WHERE id = :id";
            $stmt = $this->db->prepare($query);
            $stmt->execute([':id' => $id]);

            return $stmt->rowCount() > 0;
        } catch (PDOException $e) {
            $this->logError('delete', $e);
            throw new \Exception("An error occurred while deleting the equipment.");
        }
    }

    /**
     * @param string $method
     * @param PDOException $e
     * @return void
     */
    private function logError(string $method, PDOException $e): void
    {
        error_log("Database error in EquipmentRepository::$method: " . $e->getMessage());
    }
}