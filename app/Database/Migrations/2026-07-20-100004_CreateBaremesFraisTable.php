<?php

namespace App\Database\Migrations;

use CodeIgniter\Database\Migration;

class CreateBaremesFraisTable extends Migration
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
            'type_operation_id' => [
                'type'       => 'INT',
                'constraint' => 11,
                'unsigned'   => true,
            ],
            'montant_min' => [
                'type'       => 'DECIMAL',
                'constraint' => '15,2',
            ],
            'montant_max' => [
                'type'       => 'DECIMAL',
                'constraint' => '15,2',
            ],
            'frais' => [
                'type'       => 'DECIMAL',
                'constraint' => '15,2',
            ],
        ]);

        $this->forge->addKey('id', true);
        $this->forge->addForeignKey('type_operation_id', 'types_operations', 'id', 'CASCADE', 'RESTRICT');
        $this->forge->createTable('baremes_frais', true);

        $this->db->table('baremes_frais')->insertBatch([
            ['type_operation_id' => 2, 'montant_min' => 100,     'montant_max' => 5000,   'frais' => 100],
            ['type_operation_id' => 2, 'montant_min' => 5001,    'montant_max' => 20000,  'frais' => 300],
            ['type_operation_id' => 2, 'montant_min' => 20001,   'montant_max' => 50000,  'frais' => 500],
        ]);
    }

    public function down()
    {
        $this->forge->dropTable('baremes_frais', true);
    }
}
