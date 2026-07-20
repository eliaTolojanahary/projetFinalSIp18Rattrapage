<?php

namespace App\Models;

use CodeIgniter\Model;

class ClientPrefixeModel extends Model
{
    protected $table      = 'prefixes';
    protected $primaryKey = 'id';
    protected $returnType = 'array';

    public function estValide(string $numero): bool
    {
        $prefixe = substr($numero, 0, 3); // les 3 premiers chiffres

        return $this->where('prefixe', $prefixe)
                     ->where('actif', 1)
                     ->first() !== null;
    }
}