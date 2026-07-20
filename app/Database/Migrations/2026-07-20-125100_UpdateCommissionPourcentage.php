<?php

namespace App\Database\Migrations;

use CodeIgniter\Database\Migration;

class UpdateCommissionPourcentage extends Migration
{
    public function up()
    {
        $this->forge->modifyColumn('commission', [
            'pourcentage' => [
                'type'       => 'DECIMAL',
                'constraint' => '15,2',
                'null'       => false,
            ],
        ]);
    }

    public function down()
    {
        $this->forge->modifyColumn('commission', [
            'pourcentage' => [
                'type'       => 'DECIMAL',
                'constraint' => '5,2',
                'null'       => false,
            ],
        ]);
    }
}
