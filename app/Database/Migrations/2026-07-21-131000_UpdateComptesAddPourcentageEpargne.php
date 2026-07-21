<?php

namespace App\Database\Migrations;

use CodeIgniter\Database\Migration;

class UpdateComptesAddPourcentageEpargne extends Migration
{
    public function up()
    {
        $this->forge->addColumn('comptes', [
            'pourcentage_epargne' => [
                'type'       => 'DECIMAL',
                'constraint' => '15,2',
                'null'       => false,
                'default'    => 0,
                'after'      => 'solde',
            ],
        ]);
    }

    public function down()
    {
        $this->forge->dropColumn('comptes', 'pourcentage_epargne');
    }
}
