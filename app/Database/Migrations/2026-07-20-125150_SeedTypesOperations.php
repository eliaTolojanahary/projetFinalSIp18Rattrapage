<?php

namespace App\Database\Migrations;

use CodeIgniter\Database\Migration;

class SeedTypesOperations extends Migration
{
    public function up()
    {
        $this->db->table('types_operations')->insertBatch([
            ['id' => 1, 'code' => 'DEPOT',    'libelle' => 'Dépôt'],
            ['id' => 2, 'code' => 'RETRAIT',  'libelle' => 'Retrait'],
            ['id' => 3, 'code' => 'TRANSFERT', 'libelle' => 'Transfert'],
        ]);
    }

    public function down()
    {
        $this->db->table('types_operations')->delete();
    }
}
