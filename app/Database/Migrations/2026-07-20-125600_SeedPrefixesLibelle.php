<?php

namespace App\Database\Migrations;

use CodeIgniter\Database\Migration;

class SeedPrefixesLibelle extends Migration
{
    public function up()
    {
        $this->db->table('prefixes')
            ->where('prefixe', '034')
            ->update(['libelle' => 'Telma']);

        $this->db->table('prefixes')
            ->where('prefixe', '038')
            ->update(['libelle' => 'Yas']);

        $this->db->table('prefixes')
            ->where('prefixe', '033')
            ->update(['libelle' => 'Orange']);

        $this->db->table('prefixes')
            ->where('prefixe', '032')
            ->update(['libelle' => 'Orange']);

        $this->db->table('prefixes')
            ->where('prefixe', '037')
            ->update(['libelle' => 'Airtel']);

        $this->db->table('prefixes')
            ->where('prefixe', '031')
            ->update(['libelle' => 'Airtel']);
    }

    public function down()
    {
        $this->db->table('prefixes')
            ->where('libelle IS NOT NULL')
            ->update(['libelle' => null]);
    }
}
