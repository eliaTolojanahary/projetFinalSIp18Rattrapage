<?php

namespace App\Database\Migrations;

use CodeIgniter\Database\Migration;

class SeedBaremesFraisAllTypes extends Migration
{
    public function up()
    {
        $this->db->table('baremes_frais')->insertBatch([
            // Dépôt (type_operation_id = 1)
            ['type_operation_id' => 1, 'montant_min' => 100,    'montant_max' => 10000,  'frais' => 50],
            ['type_operation_id' => 1, 'montant_min' => 10001,  'montant_max' => 50000,  'frais' => 150],
            ['type_operation_id' => 1, 'montant_min' => 50001,  'montant_max' => 200000, 'frais' => 400],
            // Transfert (type_operation_id = 3)
            ['type_operation_id' => 3, 'montant_min' => 100,    'montant_max' => 5000,   'frais' => 150],
            ['type_operation_id' => 3, 'montant_min' => 5001,   'montant_max' => 20000,  'frais' => 400],
            ['type_operation_id' => 3, 'montant_min' => 20001,  'montant_max' => 100000, 'frais' => 750],
        ]);
    }

    public function down()
    {
        $this->db->table('baremes_frais')
            ->where('type_operation_id', 1)
            ->orwhere('type_operation_id', 3)
            ->delete();
    }
}
