<?php

namespace App\Database\Migrations;

use CodeIgniter\Database\Migration;

class CreateTransactionsTable extends Migration
{
    public function up()
    {
        $this->forge->addField([
            'id' => [
                'type'           => 'INT',
                'constraint'     => 11,
                'unsigned'       => true,
                'auto_increment' => true,
            ],
            'compte_id' => [
                'type'       => 'INT',
                'constraint' => 11,
                'unsigned'   => true,
            ],
            'type_operation_id' => [
                'type'       => 'INT',
                'constraint' => 11,
                'unsigned'   => true,
            ],
            'montant' => [
                'type'       => 'DECIMAL',
                'constraint' => '15,2',
            ],
            'baremes_frais_id' => [
                'type'       => 'INT',
                'constraint' => 11,
                'unsigned'   => true,
            ],
            'compte_destination_id' => [
                'type'       => 'INT',
                'constraint' => 11,
                'unsigned'   => true,
                'null'       => true,
            ],
            'solde_apres' => [
                'type'       => 'DECIMAL',
                'constraint' => '15,2',
            ],
            'date_operation' => [
                'type' => 'DATETIME',
                'null' => true,
            ],
        ]);

        $this->forge->addKey('id', true);
        $this->forge->addForeignKey('compte_id', 'comptes', 'id', 'CASCADE', 'RESTRICT');
        $this->forge->addForeignKey('type_operation_id', 'types_operations', 'id', 'CASCADE', 'RESTRICT');
        $this->forge->addForeignKey('baremes_frais_id', 'baremes_frais', 'id', 'CASCADE', 'RESTRICT');
        $this->forge->addForeignKey('compte_destination_id', 'comptes', 'id', 'CASCADE', 'RESTRICT');
        $this->forge->createTable('transactions', true);
    }

    public function down()
    {
        $this->forge->dropTable('transactions', true);
    }
}
