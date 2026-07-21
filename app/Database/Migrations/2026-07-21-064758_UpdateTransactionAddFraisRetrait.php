<?php

namespace App\Database\Migrations;

use CodeIgniter\Database\Migration;

class UpdateTransactionAddFraisRetrait extends Migration
{
    public function up()
    {
        $fields = [
            'frais_retrait' => [
                'type'       => 'DECIMAL',
                'constraint' => '15,2',
                'null'       => true,
                'after'      => 'commission',
            ],
        ];

        $this->forge->addColumn('transactions', $fields);
    }

    public function down()
    {
        $this->forge->dropColumn('transactions', 'frais_retrait');
    }
}
