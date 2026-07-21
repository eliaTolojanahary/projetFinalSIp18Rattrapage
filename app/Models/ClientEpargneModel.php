<?php

namespace App\Models;

use CodeIgniter\Model;

class ClientEpargneModel extends Model
{
    protected $table         = 'epargnes';
    protected $primaryKey    = 'id';
    protected $returnType    = 'array';
    protected $allowedFields = ['id_compte', 'solde', 'created_at'];
    protected $useTimestamps = false;

    public function parCompte(int $compteId): ?array
    {
        return $this->where('id_compte', $compteId)->first();
    }

    public function creerSiInexistant(int $compteId): array
    {
        $epargne = $this->parCompte($compteId);

        if ($epargne === null) {
            $this->insert([
                'id_compte'  => $compteId,
                'solde'      => 0,
                'created_at' => date('Y-m-d H:i:s'),
            ]);
            $epargne = $this->parCompte($compteId);
        }

        return $epargne;
    }

    public function crediter(int $compteId, float $montant): void
    {
        $epargne = $this->parCompte($compteId);

        if ($epargne !== null) {
            $nouveauSolde = $epargne['solde'] + $montant;
            $this->update($epargne['id'], ['solde' => $nouveauSolde]);
        }
    }
}
