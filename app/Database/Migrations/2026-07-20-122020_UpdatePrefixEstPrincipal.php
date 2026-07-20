<?php

namespace App\Database\Migrations;

use CodeIgniter\Database\Migration;

class UpdatePrefixEstPrincipal extends Migration
{
    public function up()
    {
        $fields = [
            'est_operateur_principal' => [
                'type'       => 'TINYINT',
                'constraint' => 1,
                'null'       => false,
                'default'    => 0,
                'after'      => 'libelle',
            ],
        ];

        $this->forge->addColumn('prefixes', $fields);
    }

    public function down()
    {
        $this->forge->dropColumn('prefixes', 'est_operateur_principal');
    }
}