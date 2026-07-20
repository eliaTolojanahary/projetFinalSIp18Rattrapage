<?php

namespace App\Database\Migrations;

use CodeIgniter\Database\Migration;

class CreateTypesOperationsTable extends Migration
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
            'code' => [
                'type'       => 'VARCHAR',
                'constraint' => 20,
            ],
            'libelle' => [
                'type'       => 'VARCHAR',
                'constraint' => 50,
            ],
        ]);

        $this->forge->addKey('id', true);
        $this->forge->addUniqueKey('code');
        $this->forge->createTable('types_operations', true);

        $this->db->table('types_operations')->insertBatch([
            ['code' => 'depot',    'libelle' => 'Dépôt'],
            ['code' => 'retrait',  'libelle' => 'Retrait'],
            ['code' => 'transfert','libelle' => 'Transfert'],
        ]);
    }

    public function down()
    {
        $this->forge->dropTable('types_operations', true);
    }
}
