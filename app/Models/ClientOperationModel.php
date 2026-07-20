<?php

namespace App\Models;

use CodeIgniter\Model;

class ClientOperationModel extends Model
{
    protected $table      = 'types_operations';
    protected $returnType = 'array';

    public function idParCode(string $code): ?int
    {
        $type = $this->where('code', $code)->first();
        return $type['id'] ?? null;
    }
}