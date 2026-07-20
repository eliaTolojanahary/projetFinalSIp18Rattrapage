<?php

namespace App\Database\Migrations;

use CodeIgniter\Database\Migration;

class CreatePrefixesTable extends Migration
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
            'prefixe' => [
                'type'       => 'VARCHAR',
                'constraint' => 3,
            ],
            'actif' => [
                'type'    => 'INT',
                'constraint' => 1,
                'default' => 1,
            ],
        ]);

        $this->forge->addKey('id', true);
        $this->forge->addUniqueKey('prefixe');
        $this->forge->createTable('prefixes', true);

        $this->db->table('prefixes')->insertBatch([
            ['prefixe' => '033', 'actif' => 1],
            ['prefixe' => '037', 'actif' => 1],
        ]);
    }

    public function down()
    {
        $this->forge->dropTable('prefixes', true);
    }
}
