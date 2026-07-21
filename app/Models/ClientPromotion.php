<?php

namespace App\Models;

use CodeIgniter\Model;

class ClientPromotion extends Model
{
    protected $table = 'promotion';
    protected $primaryKey = 'id';
    protected $allowedFields = ['pourcentage'];
    protected $returnType = 'array';
    protected $useTimestamps = false;

  
    
}