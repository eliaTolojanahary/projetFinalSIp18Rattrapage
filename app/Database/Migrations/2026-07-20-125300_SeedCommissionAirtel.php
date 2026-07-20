<?php

namespace App\Database\Migrations;

use CodeIgniter\Database\Migration;

class SeedCommissionAirtel extends Migration
{
    public function up()
    {
        $this->db->table('commission')->insertBatch([
            ['id_prefixe' => 5, 'pourcentage' => 15],  // Airtel 037
            ['id_prefixe' => 6, 'pourcentage' => 15],  // Airtel 031
        ]);
    }

    public function down()
    {
        $this->db->table('commission')
            ->where('id_prefixe', 5)
            ->orwhere('id_prefixe', 6)
            ->delete();
    }
}
