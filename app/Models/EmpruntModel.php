<?php

namespace App\Models;

use CodeIgniter\Model;

class EmpruntModel extends Model
{
    protected $table            = 'emprunts';
    protected $primaryKey       = 'id';
    protected $useAutoIncrement = true;
    protected $returnType       = 'array';
    protected $useSoftDeletes   = false;
    protected $protectFields    = true;
    protected $allowedFields    = [
        'livre_id',
        'nom_emprunteur',
        'date_emprunt',
        'date_retour',
    ];

    public function dernierEmpruntPourLivre(int $livreId): ?array
    {
        return $this->where('livre_id', $livreId)
            ->orderBy('date_emprunt', 'DESC')
            ->orderBy('id', 'DESC')
            ->first();
    }
}
