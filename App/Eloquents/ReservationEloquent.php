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
                $data['start_time'],
                $data['end_time'],
                $data['user_id']
            ]);

            $reservationId = $this->db->lastInsertId();

            if (!empty($data['materials'])) {
                $stmtEquip = $this->db->prepare("
                    INSERT INTO reservation_equipment (reservation_id, equipment_id)
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
     * Get all reservations with their materials
     * @return Reservation[]
     */
    public function all(): array
    {
        $stmt = $this->db->query("SELECT * FROM reservations");
        $reservations = [];
        $data = $stmt->fetchAll(PDO::FETCH_ASSOC);
        foreach ($data as $row) {
            $row['user'] = $this->userEloquent->findById($row['user_id']);
            $row['materials'] = $this->getMaterialsByReservationId(intval($row['id']));
            $reservations[] = ReservationFactory::create($row);
        }

        return $reservations;
    }

    /**
     * Get one reservation by ID
     */
    public function findById(int $id): ?Reservation
    {
        $stmt = $this->db->prepare("SELECT * FROM reservations WHERE id = ?");
        $stmt->execute([$id]);
        $data = $stmt->fetch(PDO::FETCH_ASSOC);

        if ($data) {
            $data['materials'] = $this->getMaterialsByReservationId($data['id']);
            return ReservationFactory::create($data);
        }

        return null;
    }

    /**
     * Get equipment IDs reserved by a reservation
     */
    private function getMaterialsByReservationId(int $reservationId): array
    {
        $stmt = $this->db->prepare("
            SELECT e.* FROM reservation_equipment re 
            JOIN equipments e ON e.id = re.equipment_id
            WHERE re.reservation_id = ?
        ");
        $stmt->execute([$reservationId]);

        $materials = [];
        while ($row = $stmt->fetch(PDO::FETCH_ASSOC)) {
            $materials[] = MaterialFactory::create($row);
        }
        return $materials;
    }

    /**
     * Check for conflicts (overlapping reservations for same materials)
     */
    public function hasConflict(DateTime $startTime, DateTime $endTime, array $materialIds): bool
    {
        if (empty($materialIds)) {
            return false;
        }

        $placeholders = str_repeat('?,', count($materialIds) - 1) . '?';

        $query = "
            SELECT re.equipment_id
            FROM reservation_equipment re
            JOIN reservations r ON r.id = re.reservation_id
            WHERE re.equipment_id IN ($placeholders)
              AND (
                    (r.start_date < ? AND r.end_date > ?) OR
                    (r.start_date < ? AND r.end_date > ?) OR
                    (r.start_date >= ? AND r.end_date <= ?)
              )
        ";

        $params = array_merge(
            $materialIds,
            [
                $endTime->format('Y-m-d H:i:s'),
                $endTime->format('Y-m-d H:i:s'),
                $startTime->format('Y-m-d H:i:s'),
                $startTime->format('Y-m-d H:i:s'),
                $startTime->format('Y-m-d H:i:s'),
                $endTime->format('Y-m-d H:i:s')
            ]
        );

        $stmt = $this->db->prepare($query);
        $stmt->execute($params);

        return $stmt->fetchColumn() !== false;
    }
}
