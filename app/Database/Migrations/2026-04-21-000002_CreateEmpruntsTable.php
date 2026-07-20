<?php

namespace App\Database\Migrations;

use CodeIgniter\Database\Migration;

class CreateEmpruntsTable extends Migration
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
            'livre_id' => [
                'type'       => 'INT',
                'constraint' => 11,
                'unsigned'   => true,
            ],
            'nom_emprunteur' => [
                'type'       => 'VARCHAR',
                'constraint' => 255,
            ],
            'date_emprunt' => [
                'type' => 'DATE',
            ],
            'date_retour' => [
                'type' => 'DATE',
                'null' => true,
            ],
        ]);

        $this->forge->addKey('id', true);
        $this->forge->addKey('livre_id');
        $this->forge->addForeignKey('livre_id', 'livres', 'id', 'CASCADE', 'RESTRICT');
        $this->forge->createTable('emprunts', true, [
            'ENGINE'  => 'InnoDB',
            'CHARSET' => 'utf8mb4',
            'COLLATE' => 'utf8mb4_unicode_ci',
        ]);
    }

    public function down()
    {
        $this->forge->dropTable('emprunts', true);
    }
}
