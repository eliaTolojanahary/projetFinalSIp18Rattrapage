<?php

namespace App\Database\Seeds;

use CodeIgniter\Database\Seeder;

class DataSeeder extends Seeder
{
    public function run()
    {
        $this->db->table('comptes')->insertBatch([
            [
                'numero_telephone' => '0331000001',
                'nom'              => 'Rakoto',
                'prenom'           => 'Jean',
                'solde'            => 150000.00,
            ],
            [
                'numero_telephone' => '0331000002',
                'nom'              => 'Rabe',
                'prenom'           => 'Soa',
                'solde'            => 85000.00,
            ],
            [
                'numero_telephone' => '0372000001',
                'nom'              => 'Randria',
                'prenom'           => 'Miary',
                'solde'            => 250000.00,
            ],
            [
                'numero_telephone' => '0372000002',
                'nom'              => 'Rajaonah',
                'prenom'           => 'Faly',
                'solde'            => 45000.00,
            ],
            [
                'numero_telephone' => '0331000003',
                'nom'              => 'Andriana',
                'prenom'           => 'Tiana',
                'solde'            => 52000.00,
            ],
        ]);

        $this->db->table('transactions')->insertBatch([
            [
                'compte_id'            => 1,
                'type_operation_id'    => 1,
                'montant'              => 200000.00,
                'baremes_frais_id'     => 1,
                'compte_destination_id'=> null,
                'solde_apres'          => 200000.00,
                'date_operation'       => '2026-06-01 10:00:00',
            ],
            [
                'compte_id'            => 1,
                'type_operation_id'    => 2,
                'montant'              => 50000.00,
                'baremes_frais_id'     => 3,
                'compte_destination_id'=> null,
                'solde_apres'          => 150000.00,
                'date_operation'       => '2026-06-15 14:30:00',
            ],
            [
                'compte_id'            => 2,
                'type_operation_id'    => 1,
                'montant'              => 100000.00,
                'baremes_frais_id'     => 1,
                'compte_destination_id'=> null,
                'solde_apres'          => 100000.00,
                'date_operation'       => '2026-06-10 09:00:00',
            ],
            [
                'compte_id'            => 2,
                'type_operation_id'    => 2,
                'montant'              => 15000.00,
                'baremes_frais_id'     => 1,
                'compte_destination_id'=> null,
                'solde_apres'          => 85000.00,
                'date_operation'       => '2026-06-20 11:00:00',
            ],
            [
                'compte_id'            => 3,
                'type_operation_id'    => 1,
                'montant'              => 300000.00,
                'baremes_frais_id'     => 1,
                'compte_destination_id'=> null,
                'solde_apres'          => 300000.00,
                'date_operation'       => '2026-06-05 08:00:00',
            ],
            [
                'compte_id'            => 3,
                'type_operation_id'    => 3,
                'montant'              => 50000.00,
                'baremes_frais_id'     => 3,
                'compte_destination_id'=> 4,
                'solde_apres'          => 250000.00,
                'date_operation'       => '2026-06-25 16:00:00',
            ],
            [
                'compte_id'            => 4,
                'type_operation_id'    => 3,
                'montant'              => 50000.00,
                'baremes_frais_id'     => 1,
                'compte_destination_id'=> null,
                'solde_apres'          => 50000.00,
                'date_operation'       => '2026-06-25 16:00:00',
            ],
            [
                'compte_id'            => 5,
                'type_operation_id'    => 1,
                'montant'              => 100000.00,
                'baremes_frais_id'     => 1,
                'compte_destination_id'=> null,
                'solde_apres'          => 100000.00,
                'date_operation'       => '2026-06-28 12:00:00',
            ],
            [
                'compte_id'            => 5,
                'type_operation_id'    => 2,
                'montant'              => 48000.00,
                'baremes_frais_id'     => 2,
                'compte_destination_id'=> null,
                'solde_apres'          => 52000.00,
                'date_operation'       => '2026-07-01 09:00:00',
            ],
        ]);
    }
}
