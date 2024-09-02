<?php

namespace App\Repositories;

use App\Models\Character;
use App\Config\Database;
use PDO;
use PDOException;

class CharacterRepository
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
            $query = "SELECT id, name, birth_date, kingdom, equipment_id, faction_id FROM characters";
            $stmt = $this->db->prepare($query);
            $stmt->execute();
            $charactersData = $stmt->fetchAll(PDO::FETCH_ASSOC);

            $characters = [];
            foreach ($charactersData as $characterData) {
                $character = new Character($characterData);
                $characters[] = $character->toArray();
            }
            return $characters;
        } catch (PDOException $e) {
            $this->logError('getAll', $e);
            throw new \Exception("An error occurred while fetching characters.");
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
            $query = "SELECT id, name, birth_date, kingdom, equipment_id, faction_id FROM characters WHERE id = :id";
            $stmt = $this->db->prepare($query);
            $stmt->bindValue(':id', $id, PDO::PARAM_INT);
            $stmt->execute();

            $characterData = $stmt->fetch(PDO::FETCH_ASSOC);

            if (!$characterData) {
                return null;
            }

            $character = new Character($characterData);
            return $character->toArray();
        } catch (PDOException $e) {
            $this->logError('getById', $e);
            throw new \Exception("An error occurred while fetching the character.");
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
            $query = "INSERT INTO characters (name, birth_date, kingdom, equipment_id, faction_id) 
                      VALUES (:name, :birth_date, :kingdom, :equipment_id, :faction_id)";

            $stmt = $this->db->prepare($query);
            $stmt->execute([
                ':name' => $data['name'],
                ':birth_date' => $data['birth_date'],
                ':kingdom' => $data['kingdom'],
                ':equipment_id' => $data['equipment_id'],
                ':faction_id' => $data['faction_id']
            ]);

            $id = $this->db->lastInsertId();

            return $this->getById($id);
        } catch (PDOException $e) {
            $this->logError('store', $e);
            throw new \Exception("An error occurred while storing the character.");
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
            $query = "UPDATE characters 
                      SET name = :name, birth_date = :birth_date, kingdom = :kingdom, 
                          equipment_id = :equipment_id, faction_id = :faction_id 
                      WHERE id = :id";

            $stmt = $this->db->prepare($query);
            $stmt->execute([
                ':id' => $id,
                ':name' => $data['name'],
                ':birth_date' => $data['birth_date'],
                ':kingdom' => $data['kingdom'],
                ':equipment_id' => $data['equipment_id'],
                ':faction_id' => $data['faction_id']
            ]);

            if ($stmt->rowCount() === 0) {
                return null;
            }

            return $this->getById($id);
        } catch (PDOException $e) {
            $this->logError('update', $e);
            throw new \Exception("An error occurred while updating the character.");
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
            $query = "DELETE FROM characters WHERE id = :id";
            $stmt = $this->db->prepare($query);
            $stmt->execute([':id' => $id]);

            return $stmt->rowCount() > 0;
        } catch (PDOException $e) {
            $this->logError('delete', $e);
            throw new \Exception("An error occurred while deleting the character.");
        }
    }

    /**
     * @param string $method
     * @param PDOException $e
     * @return void
     */
    private function logError(string $method, PDOException $e): void
    {
        error_log("Database error in CharacterRepository::$method: " . $e->getMessage());
    }
}