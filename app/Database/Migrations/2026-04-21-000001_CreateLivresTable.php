<?php

namespace App\Database\Migrations;

use CodeIgniter\Database\Migration;

class CreateLivresTable extends Migration
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
            'titre' => [
                'type'       => 'VARCHAR',
                'constraint' => 255,
            ],
            'auteur' => [
                'type'       => 'VARCHAR',
                'constraint' => 255,
            ],
            'isbn' => [
                'type'       => 'VARCHAR',
                'constraint' => 20,
            ],
            'annee_publication' => [
                'type' => 'INT',
                'null' => true,
            ],
            'categorie' => [
                'type'       => 'VARCHAR',
                'constraint' => 100,
                'null'       => true,
            ],
            'resume' => [
                'type' => 'TEXT',
                'null' => true,
            ],
            'couverture_fichier' => [
                'type'       => 'VARCHAR',
                'constraint' => 255,
                'null'       => true,
            ],
            'statut' => [
                'type'       => 'VARCHAR',
                'constraint' => 20,
                'default'    => 'disponible',
            ],
            'created_at' => [
                'type' => 'DATETIME',
                'null' => true,
            ],
            'updated_at' => [
                'type' => 'DATETIME',
                'null' => true,
            ],
        ]);

        $this->forge->addKey('id', true);
        $this->forge->addUniqueKey('isbn');
        $this->forge->createTable('livres', true);
    }

    public function down()
    {
        $this->forge->dropTable('livres', true);
    }
}