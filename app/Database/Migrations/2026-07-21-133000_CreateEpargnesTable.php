<?php

namespace App\Database\Migrations;

use CodeIgniter\Database\Migration;

class CreateEpargnesTable extends Migration
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
            'id_compte' => [
                'type'       => 'INT',
                'constraint' => 11,
                'unsigned'   => true,
                'unique'     => true,
            ],
            'solde' => [
                'type'       => 'DECIMAL',
                'constraint' => '15,2',
                'null'       => false,
                'default'    => 0,
            ],
            'created_at' => [
                'type' => 'DATETIME',
                'null' => true,
            ],
        ]);

        $this->forge->addKey('id', true);
        $this->forge->addForeignKey('id_compte', 'comptes', 'id', 'CASCADE', 'RESTRICT');
        $this->forge->createTable('epargnes', true);
    }

    public function down()
    {
        $this->forge->dropTable('epargnes', true);
    }
}
