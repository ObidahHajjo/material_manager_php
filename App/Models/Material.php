<?php

namespace App\Models;

use App\Enums\MaterialStatus;

class Material
{
    private int $id, $quantity;
    private string $name, $category;
    private MaterialStatus $status;

    public function __construct(array $data)
    {
        $this->id = $data['id'];
        $this->quantity = $data['quantity'];
        $this->name = $data['name'];
        $this->category = $data['category'];
        $this->status = MaterialStatus::getEnumFromValue($data['status']);
    }

    public function getId(): int
    {
        return $this->id;
    }

    public function setId(int $id)
    {
        $this->id = $id;
    }

    public function getName()
    {
        return $this->name;
    }

    public function setName(string $name)
    {
        $this->name = $name;
    }

    public function getStatus()
    {
        return $this->status;
    }

    public function setStatus(MaterialStatus $status)
    {
        $this->status = $status;
    }

    public function getQuantity()
    {
        return $this->quantity;
    }

    public function setQuantity(int $quantity)
    {
        $this->quantity = $quantity;
    }

    public function getCategory()
    {
        return $this->category;
    }

    public function setCategory(string $category)
    {
        $this->category = $category;
    }
}
