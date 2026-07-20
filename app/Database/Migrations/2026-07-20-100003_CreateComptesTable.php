<?php

namespace App\Database\Migrations;

use CodeIgniter\Database\Migration;

class CreateComptesTable extends Migration
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
            'numero_telephone' => [
                'type'       => 'VARCHAR',
                'constraint' => 15,
            ],
            'solde' => [
                'type'       => 'DECIMAL',
                'constraint' => '15,2',
                'default'    => 0,
            ],
            'created_at' => [
                'type' => 'DATETIME',
                'null' => true,
            ],
        ]);

        $this->forge->addKey('id', true);
        $this->forge->addUniqueKey('numero_telephone');
        $this->forge->createTable('comptes', true);
    }

    public function down()
    {
        $this->forge->dropTable('comptes', true);
    }
}
