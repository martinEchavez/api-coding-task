<?php

namespace App\Models;

class Equipment
{
    private $id;
    private $name;
    private $type;
    private $made_by;

    public function __construct(array $data)
    {
        $this->id = $data['id'] ?? null;
        $this->name = $data['name'] ?? '';
        $this->type = $data['type'] ?? '';
        $this->made_by = $data['made_by'] ?? '';
    }

    public function toArray(): array
    {
        return [
            'id' => $this->id,
            'name' => $this->name,
            'type' => $this->type,
            'made_by' => $this->made_by,
        ];
    }
}