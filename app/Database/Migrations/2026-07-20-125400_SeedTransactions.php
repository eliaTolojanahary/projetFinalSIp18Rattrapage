<?php

namespace App\Database\Migrations;

use CodeIgniter\Database\Migration;

class SeedTransactions extends Migration
{
    public function up()
    {
        $this->db->table('transactions')->insertBatch([
            [
                'compte_id'                => 1,
                'type_operation_id'        => 1,
                'montant'                  => 50000,
                'baremes_frais_id'         => 2,
                'solde_apres'              => 150000,
                'compte_destination_id'    => null,
                'prefixe_destination_id'   => null,
                'inclure_frais_retrait'    => 0,
                'commission'               => null,
            ],
            [
                'compte_id'                => 1,
                'type_operation_id'        => 3,
                'montant'                  => 10000,
                'baremes_frais_id'         => 8,
                'solde_apres'              => 139600,
                'compte_destination_id'    => 3,
                'prefixe_destination_id'   => 3,
                'inclure_frais_retrait'    => 0,
                'commission'               => null,
            ],
            [
                'compte_id'                => 2,
                'type_operation_id'        => 2,
                'montant'                  => 5000,
                'baremes_frais_id'         => 4,
                'solde_apres'              => 114900,
                'compte_destination_id'    => null,
                'prefixe_destination_id'   => null,
                'inclure_frais_retrait'    => 0,
                'commission'               => null,
            ],
            [
                'compte_id'                => 2,
                'type_operation_id'        => 3,
                'montant'                  => 20000,
                'baremes_frais_id'         => 9,
                'solde_apres'              => 94250,
                'compte_destination_id'    => 5,
                'prefixe_destination_id'   => 5,
                'inclure_frais_retrait'    => 1,
                'commission'               => null,
            ],
        ]);
    }

    public function down()
    {
        $this->db->table('transactions')->delete();
    }
}
