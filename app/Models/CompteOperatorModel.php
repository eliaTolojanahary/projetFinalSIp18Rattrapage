<?php

namespace App\Models;

use CodeIgniter\Model;

class CompteOperatorModel extends Model
{
    protected $table            = 'comptes';
    protected $primaryKey       = 'id';
    protected $useAutoIncrement = true;
    protected $returnType       = 'array';
    protected $useSoftDeletes   = false;
    protected $protectFields    = true;
    protected $allowedFields    = [
        'numero_telephone',
        'nom',
        'prenom',
        'solde',
    ];

    public function getSituationCompte(): array
    {
        return $this->orderBy('id', 'ASC')->findAll();
    }

    public function getSituationCompteParId(int $id): ?array
    {
        return $this->find($id);
    }
}
