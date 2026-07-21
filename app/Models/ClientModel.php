<?php

namespace App\Models;

use CodeIgniter\Model;

class ClientModel extends Model
{
    protected $table         = 'comptes';
    protected $primaryKey    = 'id';
    protected $returnType    = 'array';
    protected $allowedFields = ['numero_telephone', 'nom', 'prenom', 'solde', 'pourcentage_epargne'];

    protected $useTimestamps = false;

    protected $validationRules = [
        'numero_telephone' => 'required|min_length[10]|max_length[15]',
    ];

    protected $validationMessages = [
        'numero_telephone' => [
            'required'   => 'Le numéro de téléphone est obligatoire.',
            'min_length' => 'Numéro de téléphone invalide.',
        ],
    ];

    public function trouverOuCreerCompte(string $numero): array
    {
        $compte = $this->where('numero_telephone', $numero)->first();

        if ($compte !== null) {
            return $compte;
        }

        $id = $this->insert([
            'numero_telephone' => $numero,
            'solde'            => 0,
        ]);

        return $this->find($id);
    }
    public function rechercher(string $terme): array
    {
        return $this->like('numero_telephone', $terme)->findAll(20);
    }
}
