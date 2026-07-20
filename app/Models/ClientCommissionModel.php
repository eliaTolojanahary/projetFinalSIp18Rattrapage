<?php

namespace App\Models;

use CodeIgniter\Model;

class ClientCommissionModel extends Model
{
    protected $table = 'commission';
    protected $primaryKey = 'id';
    protected $allowedFields = ['id_prefixe', 'pourcentage'];
    protected $returnType = 'array';
    protected $useTimestamps = false;

  
    public function getCommissionByPrefixe($prefixe)
    {
        return $this->select('commission.*, prefixes.prefixe, prefixes.libelle')
                    ->join('prefixes', 'prefixes.id = commission.id_prefixe')
                    ->where('prefixes.prefixe', $prefixe)
                    ->first();
    }

   
    public function getCommissionByPrefixeId($prefixeId)
    {
        return $this->where('id_prefixe', $prefixeId)->first();
    }


    public function calculerCommission($prefixeId, $montant)
    {
        $commission = $this->where('id_prefixe', $prefixeId)->first();
        if ($commission) {
            return ($montant * $commission['pourcentage']) / 100;
        }
        return 0;
    }

    
    public function hasCommission($prefixeId)
    {
        return $this->where('id_prefixe', $prefixeId)->countAllResults() > 0;
    }

   
    public function getAllCommissionsWithPrefixes()
    {
        return $this->select('commission.*, prefixes.prefixe, prefixes.libelle, prefixes.est_operateur_principal')
                    ->join('prefixes', 'prefixes.id = commission.id_prefixe')
                    ->findAll();
    }
}