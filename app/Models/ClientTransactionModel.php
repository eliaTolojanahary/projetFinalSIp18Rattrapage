<?php

namespace App\Models;

use CodeIgniter\Model;

class ClientTransactionModel extends Model
{
    protected $table         = 'transactions';
    protected $primaryKey    = 'id';
    protected $returnType    = 'array';
    protected $useTimestamps = false; 

    protected $allowedFields = [
        'compte_id',
        'type_operation_id',
        'montant',
        'baremes_frais_id',
        'compte_destination_id',
        'solde_apres',
        'date_operation',
        'commission',
        'inclure_frais_retrait',
        'prefixe_destination_id',
        'frais_retrait',
        'promotion'
    ];


    public function historiquePourCompte(int $compteId): array
{
    return $this->select('
            transactions.id,
            transactions.date_operation,
            transactions.montant AS montant,
            transactions.commission AS commission,
            transactions.inclure_frais_retrait,
             transactions.promotion as promotion,
            transactions.frais_retrait as frais_retrait,
            baremes_frais.frais AS montant_frais,
            types_operations.code AS type_code,
            types_operations.libelle AS type_libelle,
            emetteur.numero_telephone AS compte_emetteur,
            destinataire.numero_telephone AS compte_destinataire
        ')
        ->join('types_operations', 'types_operations.id = transactions.type_operation_id')
        ->join('baremes_frais', 'baremes_frais.id = transactions.baremes_frais_id')
        ->join('comptes AS emetteur', 'emetteur.id = transactions.compte_id')
        ->join('comptes AS destinataire', 'destinataire.id = transactions.compte_destination_id', 'left')
        ->where('transactions.compte_id', $compteId)
        ->orWhere('transactions.compte_destination_id', $compteId)
        ->orderBy('transactions.date_operation', 'DESC')
        ->findAll();
}

    public function situationParCompte(int $compteId): array
    {
        return $this->select('
                COALESCE(SUM(CASE WHEN transactions.type_operation_id = 1 THEN transactions.montant ELSE 0 END), 0) AS total_depots,
                COALESCE(SUM(CASE WHEN transactions.type_operation_id = 2 THEN transactions.montant ELSE 0 END), 0) AS total_retraits,
                COALESCE(SUM(CASE WHEN transactions.type_operation_id = 3 THEN transactions.montant ELSE 0 END), 0) AS total_transferts
            ')
            ->where('transactions.compte_id', $compteId)
            ->orWhere('transactions.compte_destination_id', $compteId)
            ->first();
    }
}