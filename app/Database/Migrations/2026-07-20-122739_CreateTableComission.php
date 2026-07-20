<?php

namespace App\Database\Migrations;

use CodeIgniter\Database\Migration;

class CreateTableComission extends Migration
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
            'id_prefixe' => [
                'type'           => 'INT',
                'constraint'     => 11,
                'unsigned'       => true,
            ],
            'pourcentage' => [
                'type'       => 'DECIMAL',
                'constraint' => '5,2',
            ],
        ]);

        $this->forge->addKey('id', true);
        $this->forge->addForeignKey('id_prefixe', 'prefixes', 'id', 'CASCADE', 'RESTRICT');
        $this->forge->createTable('commission', true);
    }

    public function down()
    {
        $this->forge->dropTable('commission', true);
    }
}
