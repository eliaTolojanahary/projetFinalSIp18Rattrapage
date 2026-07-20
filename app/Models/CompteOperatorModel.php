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

    public function countAllClients(): int
    {
        return $this->countAllResults();
    }

    public function totalMontantDetenu(): float
    {
        return (float) $this->builder()
            ->select('COALESCE(SUM(solde), 0) AS total')
            ->get()
            ->getRow()
            ->total ?? 0.0;
    }

    public function getSituationCompteParIdWithTransactions(int $id): ?array
    {
        $compte = $this->find($id);
        if ($compte === null) {
            return null;
        }

        $builder = $this->db->table('transactions t');
        $compte['situation'] = $builder
            ->select('
                COALESCE(SUM(CASE WHEN t.type_operation_id = 1 THEN t.montant ELSE 0 END), 0) AS total_depots,
                COALESCE(SUM(CASE WHEN t.type_operation_id = 2 THEN t.montant ELSE 0 END), 0) AS total_retraits,
                COALESCE(SUM(CASE WHEN t.type_operation_id = 3 AND t.compte_id = ' . (int) $id . ' THEN t.montant ELSE 0 END), 0) AS total_transferts_sortants,
                COALESCE(SUM(CASE WHEN t.type_operation_id = 3 AND t.compte_destination_id = ' . (int) $id . ' THEN t.montant ELSE 0 END), 0) AS total_transferts_entrants
            ')
            ->where('t.compte_id', $id)
            ->orWhere('t.compte_destination_id', $id)
            ->get()
            ->getRowArray();

        $s = $compte['situation'];
        $compte['solde_calcule'] = $s['total_depots'] - $s['total_retraits'] - $s['total_transferts_sortants'] + $s['total_transferts_entrants'];

        return $compte;
    }
}
