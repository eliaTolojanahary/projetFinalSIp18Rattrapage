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
            ->join('comptes c', 'transactions.compte_id = c.id')
            ->join('prefixes p', 'SUBSTR(c.numero_telephone, 1, 3) = p.prefixe')
            ->where('p.est_operateur_principal', 1)
            ->whereIn('transactions.type_operation_id', [2, 3])
            ->get()
            ->getRow()
            ->total ?? 0.0;
    }

    public function totalFraisAutre(): float
    {
        return (float) $this->builder()
            ->select('COALESCE(SUM(bf.frais), 0) AS total')
            ->join('baremes_frais bf', 'transactions.baremes_frais_id = bf.id')
            ->join('comptes c', 'transactions.compte_id = c.id')
            ->join('prefixes p', 'SUBSTR(c.numero_telephone, 1, 3) = p.prefixe')
            ->where('p.est_operateur_principal', 0)
            ->whereIn('transactions.type_operation_id', [2, 3])
            ->get()
            ->getRow()
            ->total ?? 0.0;
    }

    public function montantsParOperateur(): array
    {
        return $this->builder()
            ->select('p.id AS id_operateur, p.libelle AS nom_operateur, p.prefixe, COALESCE(SUM(bf.frais), 0) AS montant_total, COUNT(transactions.id) AS nombre_transactions')
            ->join('baremes_frais bf', 'transactions.baremes_frais_id = bf.id')
            ->join('comptes c', 'transactions.compte_id = c.id')
            ->join('prefixes p', 'SUBSTR(c.numero_telephone, 1, 3) = p.prefixe')
            ->whereIn('transactions.type_operation_id', [2, 3])
            ->groupBy('p.id, p.libelle, p.prefixe')
            ->get()
            ->getResultArray();
    }
}
