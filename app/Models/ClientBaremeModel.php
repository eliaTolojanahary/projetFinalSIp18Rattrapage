<?php

namespace App\Models;

use CodeIgniter\Model;

class ClientBaremeModel extends Model
{
    protected $table         = 'baremes_frais';
    protected $returnType    = 'array';
    protected $allowedFields = ['type_operation_id', 'montant_min', 'montant_max', 'frais'];

   
    public function calculerFrais(int $typeOperationId, float $montant): ?array
{
    return $this->where('type_operation_id', $typeOperationId)
                ->where('montant_min <=', $montant)
                ->where('montant_max >=', $montant)
                ->first();
}
}