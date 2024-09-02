<?php

namespace App\Models;

class Character
{
    private $id;
    private $name;
    private $birth_date;
    private $kingdom;
    private $equipment_id;
    private $faction_id;

    public function __construct(array $data)
    {
        $this->id = $data['id'] ?? null;
        $this->name = $data['name'] ?? '';
        $this->birth_date = $data['birth_date'] ?? '';
        $this->kingdom = $data['kingdom'] ?? '';
        $this->equipment_id = $data['equipment_id'] ?? null;
        $this->faction_id = $data['faction_id'] ?? null;
    }

    public function toArray(): array
    {
        return [
            'id' => $this->id,
            'name' => $this->name,
            'birth_date' => $this->birth_date,
            'kingdom' => $this->kingdom,
            'equipment_id' => $this->equipment_id,
            'faction_id' => $this->faction_id,
        ];
    }
}