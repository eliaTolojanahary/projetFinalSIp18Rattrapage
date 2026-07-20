<?php

namespace App\Models;

use CodeIgniter\Model;
use InvalidArgumentException;

class LivreModel extends Model
{
    protected $table         = 'livres';
    protected $primaryKey     = 'id';
    protected $useAutoIncrement = true;
    protected $returnType     = 'array';
    protected $useSoftDeletes = false;
    protected $protectFields  = true;
    protected $allowedFields  = [
        'titre',
        'auteur',
        'isbn',
        'annee_publication',
        'categorie',
        'resume',
        'couverture_fichier',
        'statut',
    ];
    protected $useTimestamps = true;
    protected $dateFormat    = 'datetime';
    protected $createdField   = 'created_at';
    protected $updatedField   = 'updated_at';

    protected $validationRules = [
        'titre' => 'required|min_length[3]',
        'auteur' => 'required',
        'isbn' => 'required|is_unique[livres.isbn,id,{id}]',
        'annee_publication' => 'required',
    ];

    protected $validationMessages = [
        'titre' => [
            'required'   => 'Le titre est obligatoire.',
            'min_length' => 'Le titre doit contenir au moins 3 caractères.',
        ],
        'auteur' => [
            'required' => 'L\'auteur est obligatoire.',
        ],
        'isbn' => [
            'required'  => 'L\'ISBN est obligatoire.',
            'is_unique' => 'Cet ISBN existe déjà en base de données.',
        ],
        'annee_publication' => [
            'required' => 'L\'année de publication est obligatoire.',
        ],
    ];

    protected $beforeInsert = ['ensurePublicationYearIsNotFuture'];
    protected $beforeUpdate = ['ensurePublicationYearIsNotFuture'];

    protected function ensurePublicationYearIsNotFuture(array $data): array
    {
        if (! isset($data['data']['annee_publication'])) {
            return $data;
        }

        $annee = (int) $data['data']['annee_publication'];
        $anneeCourante = (int) date('Y');

        if ($annee > $anneeCourante) {
            throw new InvalidArgumentException('L\'année de publication ne peut pas être dans le futur.');
        }

        return $data;
    }

    public function rechercherLivres(?string $motCle = null, ?string $categorie = null)
    {
        $builder = $this->builder();

        if ($motCle !== null && $motCle !== '') {
            $builder->like('titre', $motCle);
        }

        if ($categorie !== null && $categorie !== '') {
            $builder->where('categorie', $categorie);
        }

        return $builder->orderBy('titre', 'ASC')->get()->getResultArray();
    }

    public function livresPagine(): array
    {
        return $this->orderBy('titre', 'ASC')->paginate(10);
    }
}
