<?php

namespace App\Database\Migrations;

use CodeIgniter\Database\Migration;

class UpdateTransactionsAddEpargneFields extends Migration
{
    public function up()
    {
        $fields = [
            'promotion' => [
                'type'       => 'DECIMAL',
                'constraint' => '15,2',
                'null'       => true,
                'after'      => 'commission',
            ],
            'frais_retrait' => [
                'type'       => 'DECIMAL',
                'constraint' => '15,2',
                'null'       => true,
                'after'      => 'promotion',
            ],
            'epargnes' => [
                'type'       => 'DECIMAL',
                'constraint' => '15,2',
                'null'       => true,
                'after'      => 'frais_retrait',
            ],
        ];

        $this->forge->addColumn('transactions', $fields);
    }

    public function down()
    {
        $this->forge->dropColumn('transactions', 'promotion');
        $this->forge->dropColumn('transactions', 'frais_retrait');
        $this->forge->dropColumn('transactions', 'epargnes');
    }
}
