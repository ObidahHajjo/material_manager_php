<?php

namespace App\Eloquents;

use App\Models\Material;
use App\Factory\MaterialFactory;
use PDO;
use Config\Database;
use PDOException;

class MaterialEloquent
{
    private ?PDO $db;

    /**
     * Summary of __construct
     */
    public function __construct()
    {
        $this->db = Database::getInstance();
    }

    // Queries
    const FIND_BY_ID_SQL = "SELECT * FROM materials WHERE id = $1 LIMIT 1";
    const CREATE_SQL = "INSERT INTO materials (name, category, quantity, status) VALUES ($1, $2, $3, $4)";
    const UPDATE_SQL = "UPDATE materials SET name = $1, category = $2, quantity = $3, status = $4 WHERE id = $5";
    const DELETE_SQL = "DELETE FROM materials WHERE id = $1";
    const GET_ALL_SQL = "SELECT * FROM materials";


    /**
     * Get all materials
     * @return array|null
     * @throws PDOException
     */
    public function all(): ?array
    {
        $materials = [];
        try {
            $stmt = $this->db->query(self::GET_ALL_SQL);
            while ($row = $stmt->fetch(PDO::FETCH_ASSOC)) {
                $materials[] = MaterialFactory::create($row);
            }
            return $materials;
        } catch (PDOException $e) {
            throw new PDOException($e->getMessage());
        }
    }

    /**
     * Find a material by ID
     * @param int $id
     * @return Material|null
     * @throws PDOException
     */
    public function findById(int $id): ?Material
    {
        try {
            $stmt = $this->db->prepare(self::FIND_BY_ID_SQL);
            $stmt->execute([$id]);
            $data = $stmt->fetch(PDO::FETCH_ASSOC);
            return $data ? MaterialFactory::create($data) : null;
        } catch (PDOException $e) {
            throw new PDOException($e->getMessage());
        }
    }

    /**
     * Create a new material
     * @param array $data
     * @return bool
     * @throws PDOException
     */
    public function create(array $data): bool
    {
        try {
            $stmt = $this->db->prepare(self::CREATE_SQL);
            return $stmt->execute([
                $data['name'],
                $data['category'],
                $data['quantity'],
                $data['status']
            ]);
        } catch (PDOException $e) {
            throw new PDOException($e->getMessage());
        }
    }

    /**
     * Update an existing material
     * @param array $data
     * @return bool
     * @throws PDOException
     */
    public function update(array $data): bool
    {
        try {
            $stmt = $this->db->prepare(self::UPDATE_SQL);
            return $stmt->execute([
                $data['name'],
                $data['category'],
                $data['quantity'],
                $data['status'],
                $data['id']
            ]);
        } catch (PDOException $e) {
            throw new PDOException($e->getMessage());
        }
    }

    /**
     * Delete a material by ID
     * @param int $id
     * @return bool
     * @throws PDOException
     */
    public function delete(int $id): bool
    {
        try {
            $stmt = $this->db->prepare(self::DELETE_SQL);
            return $stmt->execute([$id]);
        } catch (PDOException $e) {
            throw new PDOException($e->getMessage());
        }
    }
}
