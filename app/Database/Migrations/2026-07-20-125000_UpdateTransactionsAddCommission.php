<?php

namespace App\Database\Migrations;

use CodeIgniter\Database\Migration;

class UpdateTransactionsAddCommission extends Migration
{
    public function up()
    {
        $fields = [
            'commission' => [
                'type'       => 'DECIMAL',
                'constraint' => '15,2',
                'null'       => true,
                'after'      => 'inclure_frais_retrait',
            ],
        ];

        $this->forge->addColumn('transactions', $fields);
    }

    public function down()
    {
        $this->forge->dropColumn('transactions', 'commission');
    }
}
