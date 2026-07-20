<?php

namespace App\Database\Migrations;

use CodeIgniter\Database\Migration;

class AddLibelleToPrefixes extends Migration
{
    public function up()
    {
        if (!$this->db->fieldExists('libelle', 'prefixes')) {
            $this->forge->addColumn('prefixes', [
                'libelle' => [
                    'type'       => 'VARCHAR',
                    'constraint' => 50,
                    'null'       => true,
                    'after'      => 'prefixe',
                ],
            ]);
        }
    }

    public function down()
    {
        $this->forge->dropColumn('prefixes', 'libelle');
    }
}
