<?php

namespace App\Database\Migrations;

use CodeIgniter\Database\Migration;

class SeedAllData extends Migration
{
    public function up()
    {
        $this->db->table('prefixes')->insertBatch([
            ['prefixe' => '034', 'libelle' => 'Yas',  'actif' => 1, 'est_operateur_principal' => 1],
            ['prefixe' => '038', 'libelle' => 'Yas',    'actif' => 1, 'est_operateur_principal' => 1],
            ['prefixe' => '033', 'libelle' => 'Orange', 'actif' => 1, 'est_operateur_principal' => 0],
            ['prefixe' => '032', 'libelle' => 'Orange', 'actif' => 1, 'est_operateur_principal' => 0],
            ['prefixe' => '037', 'libelle' => 'Airtel', 'actif' => 1, 'est_operateur_principal' => 0],
            ['prefixe' => '031', 'libelle' => 'Airtel', 'actif' => 1, 'est_operateur_principal' => 0],
        ]);

        $this->db->table('types_operations')->insertBatch([
            ['code' => 'depot',     'libelle' => 'Dépôt'],
            ['code' => 'retrait',   'libelle' => 'Retrait'],
            ['code' => 'transfert', 'libelle' => 'Transfert'],
        ]);

        $this->db->table('baremes_frais')->insertBatch([
            ['type_operation_id' => 1, 'montant_min' => 100,    'montant_max' => 10000,  'frais' => 50],
            ['type_operation_id' => 1, 'montant_min' => 10001,  'montant_max' => 50000,  'frais' => 150],
            ['type_operation_id' => 1, 'montant_min' => 50001,  'montant_max' => 200000, 'frais' => 400],
            ['type_operation_id' => 2, 'montant_min' => 100,    'montant_max' => 5000,   'frais' => 100],
            ['type_operation_id' => 2, 'montant_min' => 5001,   'montant_max' => 20000,  'frais' => 300],
            ['type_operation_id' => 2, 'montant_min' => 20001,  'montant_max' => 50000,  'frais' => 500],
            ['type_operation_id' => 3, 'montant_min' => 100,    'montant_max' => 5000,   'frais' => 150],
            ['type_operation_id' => 3, 'montant_min' => 5001,   'montant_max' => 20000,  'frais' => 400],
            ['type_operation_id' => 3, 'montant_min' => 20001,  'montant_max' => 100000, 'frais' => 750],
        ]);

        $this->db->table('commission')->insertBatch([
            ['id_prefixe' => 3, 'pourcentage' => 10],
            ['id_prefixe' => 4, 'pourcentage' => 10],
            ['id_prefixe' => 5, 'pourcentage' => 15],
            ['id_prefixe' => 6, 'pourcentage' => 15],
        ]);

        $this->db->table('comptes')->insertBatch([
            ['numero_telephone' => '034123456', 'nom' => 'Rakoto', 'prenom' => 'Jean',   'solde' => 150000],
            ['numero_telephone' => '038654321', 'nom' => 'Rasoa',  'prenom' => 'Marie',  'solde' => 120000],
            ['numero_telephone' => '033111222', 'nom' => 'Andry',  'prenom' => 'Paul',   'solde' => 80000],
            ['numero_telephone' => '032333444', 'nom' => 'Rabe',   'prenom' => 'Daniel', 'solde' => 60000],
            ['numero_telephone' => '037555666', 'nom' => 'Hery',   'prenom' => 'Claire', 'solde' => 90000],
            ['numero_telephone' => '031777888', 'nom' => 'Soa',    'prenom' => 'Luc',    'solde' => 40000],
        ]);

        $this->db->table('transactions')->insertBatch([
            [
                'compte_id'                => 1,
                'type_operation_id'        => 1,
                'montant'                  => 50000,
                'baremes_frais_id'         => 2,
                'solde_apres'              => 150000,
                'compte_destination_id'    => null,
                'prefixe_destination_id'   => null,
                'inclure_frais_retrait'    => 0,
            ],
            [
                'compte_id'                => 1,
                'type_operation_id'        => 3,
                'montant'                  => 10000,
                'baremes_frais_id'         => 8,
                'solde_apres'              => 139600,
                'compte_destination_id'    => 3,
                'prefixe_destination_id'   => 3,
                'inclure_frais_retrait'    => 0,
            ],
            [
                'compte_id'                => 2,
                'type_operation_id'        => 2,
                'montant'                  => 5000,
                'baremes_frais_id'         => 4,
                'solde_apres'              => 114900,
                'compte_destination_id'    => null,
                'prefixe_destination_id'   => null,
                'inclure_frais_retrait'    => 0,
            ],
            [
                'compte_id'                => 2,
                'type_operation_id'        => 3,
                'montant'                  => 20000,
                'baremes_frais_id'         => 9,
                'solde_apres'              => 94250,
                'compte_destination_id'    => 5,
                'prefixe_destination_id'   => 5,
                'inclure_frais_retrait'    => 1,
            ],
        ]);
    }

    public function down()
    {
        $this->db->table('transactions')->delete();
        $this->db->table('comptes')->delete();
        $this->db->table('commission')->delete();
        $this->db->table('baremes_frais')->delete();
        $this->db->table('types_operations')->delete();
        $this->db->table('prefixes')->delete();
    }
}
