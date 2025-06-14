<?php

namespace App\Eloquents;

use App\Factory\MaterialFactory;
use App\Models\Reservation;
use App\Factory\ReservationFactory;
use DateTime;
use PDO;
use Config\Database;
use Exception;
use PDOException;

class ReservationEloquent
{
    private ?PDO $db;
    private UserEloquent $userEloquent;

    /**
     * Summary of __construct
     */
    public function __construct()
    {
        $this->db = Database::getInstance();
        $this->userEloquent = new UserEloquent();
    }

    /**
     * Create a new reservation and assign materials
     */
    public function create(array $data): bool
    {
        $this->db->beginTransaction();

        try {
            $stmt = $this->db->prepare("
                INSERT INTO reservations (start_date, end_date, user_id)
                VALUES (?, ?, ?)
            ");
            $stmt->execute([
                $data['start_date'],
                $data['end_date'],
                $data['user_id']
            ]);

            $reservationId = $this->db->lastInsertId();

            if (!empty($data['materials'])) {
                $stmtEquip = $this->db->prepare("
                    INSERT INTO reservation_material (reservation_id, material_id)
                    VALUES (?, ?)
                ");

                foreach ($data['materials'] as $equipmentId) {
                    $stmtEquip->execute([$reservationId, $equipmentId]);
                }
            }

            $this->db->commit();
            return true;
        } catch (Exception $e) {
            $this->db->rollBack();
            throw $e;
        }
    }

    /**
     * Update an exsting reservation
     * @param array $data
     * @return bool
     * @throws Exception
     */
    public function update(array $data): bool
    {
        $this->db->beginTransaction();

        try {
            $stmt = $this->db->prepare("
            UPDATE reservations
            SET start_date = ?, end_date = ?
            WHERE id = ?
        ");
            $stmt->execute([
                $data['start_date'],
                $data['end_date'],
                $data['id']
            ]);

            $deleteStmt = $this->db->prepare("DELETE FROM reservation_material WHERE reservation_id = ?");
            $deleteStmt->execute([$data['id']]);

            if (!empty($data['materials'])) {
                $insertStmt = $this->db->prepare("
                INSERT INTO reservation_material (reservation_id, material_id)
                VALUES (?, ?)
            ");
                foreach ($data['materials'] as $materialId) {
                    $insertStmt->execute([$data['id'], $materialId]);
                }
            }
            $this->db->commit();
            return true;
        } catch (Exception $e) {
            $this->db->rollBack();
            throw $e;
        }
    }

    /**
     * Delete a reservation by ID
     * @param int $id
     * @return bool
     * @throws PDOException
     */
    public function delete(int $id): bool
    {
        try {
            $stmt = $this->db->prepare("DELETE FROM reservations WHERE id = ?");
            return $stmt->execute([$id]);
        } catch (PDOException $e) {
            throw new PDOException($e->getMessage());
        }
    }

    /**
     * Get all reservations with their materials
     * @return array
     */
    public function all(): array
    {
        try {
            $stmt = $this->db->query("SELECT * FROM reservations");
            $reservations = [];
            $data = $stmt->fetchAll(PDO::FETCH_ASSOC);
            foreach ($data as $row) {
                $row['user'] = $this->userEloquent->findById($row['user_id']);
                $row['materials'] = $this->getMaterialsByReservationId(intval($row['id']));
                $reservations[] = ReservationFactory::create($row);
            }
            return $reservations;
        } catch (PDOException $e) {
            throw new PDOException($e->getMessage());
        }
    }

    /**
     * Get all reservations by user id with their materials
     * @return array
     */
    public function allByUserId(int $userId): array
    {
        try {
            $stmt = $this->db->prepare("SELECT * FROM reservations WHERE user_id = ?");
            $stmt->execute([$userId]);
            $reservations = [];
            $data = $stmt->fetchAll(PDO::FETCH_ASSOC);
            if (empty($data)) return [];
            $user = $this->userEloquent->findById($data[0]['user_id']);
            foreach ($data as $row) {
                $row['user'] = $user;
                $row['materials'] = $this->getMaterialsByReservationId(intval($row['id']));
                $reservations[] = ReservationFactory::create($row);
            }

            return $reservations;
        } catch (PDOException $e) {
            throw new PDOException($e->getMessage());
        }
    }

    /**
     * Get one reservation by ID
     */
    public function findById(int $id): ?Reservation
    {
        try {
            $stmt = $this->db->prepare("SELECT * FROM reservations WHERE id = ?");
            $stmt->execute([$id]);
            $data = $stmt->fetch(PDO::FETCH_ASSOC);

            if (!$data) return null;
            $data['materials'] = $this->getMaterialsByReservationId($data['id']);
            return ReservationFactory::create($data);
        } catch (PDOException $e) {
            throw new PDOException($e->getMessage());
        }
    }

    /**
     * Get equipment IDs reserved by a reservation
     */
    private function getMaterialsByReservationId(int $reservationId): array
    {
        try {
            $stmt = $this->db->prepare("
            SELECT m.* FROM reservation_material rm 
            JOIN materials m ON m.id = rm.material_id
            WHERE rm.reservation_id = ?
        ");
            $stmt->execute([$reservationId]);

            $materials = [];
            while ($row = $stmt->fetch(PDO::FETCH_ASSOC)) {
                $materials[] = MaterialFactory::create($row);
            }
            return $materials;
        } catch (PDOException $e) {
            throw new PDOException($e->getMessage());
        }
    }

    /**
     * Check for conflicts (overlapping reservations for same materials)
     */
    public function hasConflict(?int $reservationId, string $startTime, string $endTime, array $materialIds): bool
    {
        if (empty($materialIds)) {
            return false;
        }
        try {
            $placeholders = str_repeat('?,', count($materialIds) - 1) . '?';

            $query = "
            SELECT rm.material_id
            FROM reservation_material rm
            JOIN reservations r ON r.id = rm.reservation_id
            WHERE rm.material_id IN ($placeholders)
              AND (
                    (r.start_date < ? AND r.end_date > ?) OR
                    (r.start_date < ? AND r.end_date > ?) OR
                    (r.start_date >= ? AND r.end_date <= ?)
              )
        ";

            $params = array_merge(
                $materialIds,
                [
                    $endTime,
                    $endTime,
                    $startTime,
                    $startTime,
                    $startTime,
                    $endTime
                ],
            );
            if ($reservationId && $reservationId > 0 && $reservationId !== null) {
                $query .= " AND r.id != ?";
                $params = array_merge($params, [$reservationId]);
            }

            $stmt = $this->db->prepare($query);
            $stmt->execute($params);

            return $stmt->fetchColumn() !== false;
        } catch (PDOException $e) {
            throw new PDOException($e->getMessage());
        }
    }
}
