<?php

namespace App\Models;

use CodeIgniter\Model;

class TransactionOperatorModel extends Model
{
    protected $table            = 'transactions';
    protected $primaryKey       = 'id';
    protected $useAutoIncrement = true;
    protected $returnType       = 'array';
    protected $useSoftDeletes   = false;
    protected $protectFields    = true;
    protected $allowedFields    = [
        'compte_id',
        'type_operation_id',
        'montant',
        'baremes_frais_id',
        'compte_destination_id',
        'solde_apres',
    ];

    public function totalFrais(): float
    {
        return (float) $this->builder()
            ->select('COALESCE(SUM(bf.frais), 0) AS total')
            ->join('baremes_frais bf', 'transactions.baremes_frais_id = bf.id')
            ->whereIn('transactions.type_operation_id', [2, 3])
            ->get()
            ->getRow()
            ->total ?? 0.0;
    }
}
