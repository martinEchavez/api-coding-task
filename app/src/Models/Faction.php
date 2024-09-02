<?php

namespace App\Models;

class Faction
{
    private $id;
    private $faction_name;
    private $description;

    public function __construct(array $data)
    {
        $this->id = $data['id'] ?? null;
        $this->faction_name = $data['faction_name'] ?? '';
        $this->description = $data['description'] ?? '';
    }

    public function toArray(): array
    {
        return [
            'id' => $this->id,
            'faction_name' => $this->faction_name,
            'description' => $this->description,
        ];
    }
}