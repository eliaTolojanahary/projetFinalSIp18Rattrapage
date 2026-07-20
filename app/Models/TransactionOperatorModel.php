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

    /**
     * Crée une transaction et met à jour les soldes atomiquement.
     *
     * @param array{compte_id: int, type_operation_id: int, montant: float, baremes_frais_id: int, compte_destination_id?: int|null} $data
     * @return bool true si la transaction a réussi, false si solde insuffisant ou erreur
     */
    public function creerTransaction(array $data): bool
    {
        $compteModel = new CompteOperatorModel();

        $compteId         = (int) $data['compte_id'];
        $typeOperationId  = (int) $data['type_operation_id'];
        $montant          = (float) $data['montant'];
        $baremesFraisId   = (int) $data['baremes_frais_id'];
        $destId           = !empty($data['compte_destination_id']) ? (int) $data['compte_destination_id'] : null;

        $compteSource = $compteModel->getSituationCompteParId($compteId);
        if ($compteSource === null) {
            return false;
        }

        $soldeActuel = (float) $compteSource['solde'];

        // Vérification solde suffisant pour retrait (2) et transfert sortant (3)
        if (in_array($typeOperationId, [2, 3], true) && $soldeActuel < $montant) {
            return false;
        }

        // Calcul nouveau solde source
        switch ($typeOperationId) {
            case 1: // Dépôt
                $nouveauSolde = $soldeActuel + $montant;
                break;
            case 2: // Retrait
            case 3: // Transfert sortant
                $nouveauSolde = $soldeActuel - $montant;
                break;
            default:
                return false;
        }

        $this->db->transException(function () use ($compteModel, $compteId, $typeOperationId, $montant, $baremesFraisId, $destId, $nouveauSolde, $soldeActuel) {
            // 1ère transaction : compte source
            $this->insert([
                'compte_id'             => $compteId,
                'type_operation_id'     => $typeOperationId,
                'montant'               => $montant,
                'baremes_frais_id'      => $baremesFraisId,
                'compte_destination_id' => $destId,
                'solde_apres'           => $nouveauSolde,
            ]);
            $compteModel->updateSolde($compteId, $nouveauSolde);

            // 2ème transaction : compte destination (si transfert)
            if ($typeOperationId === 3 && $destId !== null) {
                $compteDest = $compteModel->getSituationCompteParId($destId);
                if ($compteDest === null) {
                    throw \RuntimeException('Compte destination introuvable.');
                }

                $soldeDestActuel    = (float) $compteDest['solde'];
                $nouveauSoldeDest   = $soldeDestActuel + $montant;

                $this->insert([
                    'compte_id'             => $destId,
                    'type_operation_id'     => $typeOperationId,
                    'montant'               => $montant,
                    'baremes_frais_id'      => $baremesFraisId,
                    'compte_destination_id' => $compteId,
                    'solde_apres'           => $nouveauSoldeDest,
                ]);
                $compteModel->updateSolde($destId, $nouveauSoldeDest);
            }
        });

        return true;
    }
}
