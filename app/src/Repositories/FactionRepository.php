<?php

namespace App\Repositories;

use App\Models\Faction;
use App\Config\Database;
use PDO;
use PDOException;

class FactionRepository
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
            $query = "SELECT id, faction_name, description FROM factions";
            $stmt = $this->db->prepare($query);
            $stmt->execute();
            $factionsData = $stmt->fetchAll(PDO::FETCH_ASSOC);

            $factions = [];
            foreach ($factionsData as $factionData) {
                $faction = new Faction($factionData);
                $factions[] = $faction->toArray();
            }
            return $factions;
        } catch (PDOException $e) {
            $this->logError('getAll', $e);
            throw new \Exception("An error occurred while fetching factions.");
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
            $query = "SELECT id, faction_name, description FROM factions WHERE id = :id";
            $stmt = $this->db->prepare($query);
            $stmt->bindValue(':id', $id, PDO::PARAM_INT);
            $stmt->execute();

            $factionData = $stmt->fetch(PDO::FETCH_ASSOC);

            if (!$factionData) {
                return null;
            }

            $faction = new Faction($factionData);
            return $faction->toArray();
        } catch (PDOException $e) {
            $this->logError('getById', $e);
            throw new \Exception("An error occurred while fetching the faction.");
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
            $query = "INSERT INTO factions (faction_name, description) 
                      VALUES (:faction_name, :description)";

            $stmt = $this->db->prepare($query);
            $stmt->execute([
                ':faction_name' => $data['faction_name'],
                ':description' => $data['description']
            ]);

            $id = $this->db->lastInsertId();

            return $this->getById($id);
        } catch (PDOException $e) {
            $this->logError('store', $e);
            throw new \Exception("An error occurred while storing the faction.");
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
            $query = "UPDATE factions 
                      SET faction_name = :faction_name, description = :description 
                      WHERE id = :id";

            $stmt = $this->db->prepare($query);
            $stmt->execute([
                ':id' => $id,
                ':faction_name' => $data['faction_name'],
                ':description' => $data['description']
            ]);

            if ($stmt->rowCount() === 0) {
                return null;
            }

            return $this->getById($id);
        } catch (PDOException $e) {
            $this->logError('update', $e);
            throw new \Exception("An error occurred while updating the faction.");
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
            $query = "DELETE FROM factions WHERE id = :id";
            $stmt = $this->db->prepare($query);
            $stmt->execute([':id' => $id]);

            return $stmt->rowCount() > 0;
        } catch (PDOException $e) {
            $this->logError('delete', $e);
            throw new \Exception("An error occurred while deleting the faction.");
        }
    }

    /**
     * @param string $method
     * @param PDOException $e
     * @return void
     */
    private function logError(string $method, PDOException $e): void
    {
        error_log("Database error in FactionRepository::$method: " . $e->getMessage());
    }
}