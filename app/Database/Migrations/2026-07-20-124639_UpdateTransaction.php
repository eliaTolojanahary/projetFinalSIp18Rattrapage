<?php

namespace App\Database\Migrations;

use CodeIgniter\Database\Migration;

class UpdateTransaction extends Migration
{
    public function up()
    {
        $fields = [
            'prefixe_destination_id' => [
                'type'           => 'INT',
                'constraint'     => 11,
                'unsigned'       => true,
                'after'      => 'date_operation',
            ],
            'inclure_frais_retrait' => [
                'type'       => 'TINYINT',
                'constraint' => 1,
                'null'       => false,
                'default'    => 0,
                'after'      => 'prefixe_destination_id',
            ]
        ];

        $this->forge->addForeignKey('prefixe_destination_id', 'prefixes', 'id', 'CASCADE', 'RESTRICT');
        $this->forge->addColumn('transactions', $fields);
    }

    public function down()
    {
        $this->forge->dropColumn('transactions', 'prefixe_destination_id');
        $this->forge->dropColumn('transactions', 'inclure_frais_retrait');

    }
}
