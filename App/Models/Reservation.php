<?php

namespace App\Models;

use DateTime;

class Reservation
{
    public int $id;
    public DateTime $startDate;
    public DateTime $endDate;
    public User $user;
    public array $materials = [];

    public function __construct(array $data)
    {
        $this->id = $data['id'];
        $this->startDate = new DateTime($data['start_date']);
        $this->endDate = new DateTime($data['end_date']);
        $this->user = $data['user'];
        $this->materials = $data['materials'] ?? [];
    }

    public function getId(): int
    {
        return $this->id;
    }

    public function getStartDate(): DateTime
    {
        return $this->startDate;
    }

    public function getEndDate(): DateTime
    {
        return $this->endDate;
    }

    public function getUser(): User
    {
        return $this->user;
    }

    public function getMaterials(): array
    {
        return $this->materials;
    }

    public function addMaterial(Material $material): void
    {
        $this->materials[] = $material;
    }

    public function setId(int $id): void
    {
        $this->id = $id;
    }

    public function setStartDate(DateTime $startDate): void
    {
        $this->startDate = $startDate;
    }

    public function setEndDate(DateTime $endDate): void
    {
        $this->endDate = $endDate;
    }

    public function setUserId(User $user): void
    {
        $this->user = $user;
    }

    public function setMaterials(array $materials): void
    {
        $this->materials = $materials;
    }
}
