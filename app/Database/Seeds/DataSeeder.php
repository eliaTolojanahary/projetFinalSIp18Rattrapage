<?php

namespace App\Database\Seeds;

use CodeIgniter\Database\Seeder;

class DataSeeder extends Seeder
{
    public function run()
    {
        // Vider les tables dans l'ordre (FK)
        $this->db->table('transactions')->truncate();
        $this->db->table('baremes_frais')->truncate();
        $this->db->table('comptes')->truncate();
        $this->db->table('types_operations')->truncate();
        $this->db->table('prefixes')->truncate();

        // Reset SQLite autoincrement sequences
        if ($this->db->DBDriver === 'SQLite3') {
            $this->db->query("DELETE FROM sqlite_sequence");
        }

        // --- Prefixes ---
        $this->db->table('prefixes')->insertBatch([
            ['prefixe' => '033', 'libelle' => 'Orange', 'actif' => 1],
            ['prefixe' => '037', 'libelle' => 'Airtel', 'actif' => 1],
        ]);

        // --- Types d'opérations ---
        $this->db->table('types_operations')->insertBatch([
            ['code' => 'depot',     'libelle' => 'Dépôt'],
            ['code' => 'retrait',   'libelle' => 'Retrait'],
            ['code' => 'transfert', 'libelle' => 'Transfert'],
        ]);

        // --- Barèmes de frais ---
        // type_operation_id: 1=depot, 2=retrait, 3=transfert
        $this->db->table('baremes_frais')->insertBatch([
            // Dépôt
            ['type_operation_id' => 1, 'montant_min' => 100,    'montant_max' => 10000,  'frais' => 50],
            ['type_operation_id' => 1, 'montant_min' => 10001,  'montant_max' => 50000,  'frais' => 150],
            ['type_operation_id' => 1, 'montant_min' => 50001,  'montant_max' => 200000, 'frais' => 400],
            // Retrait
            ['type_operation_id' => 2, 'montant_min' => 100,    'montant_max' => 5000,   'frais' => 100],
            ['type_operation_id' => 2, 'montant_min' => 5001,   'montant_max' => 20000,  'frais' => 300],
            ['type_operation_id' => 2, 'montant_min' => 20001,  'montant_max' => 50000,  'frais' => 500],
            // Transfert
            ['type_operation_id' => 3, 'montant_min' => 100,    'montant_max' => 5000,   'frais' => 150],
            ['type_operation_id' => 3, 'montant_min' => 5001,   'montant_max' => 20000,  'frais' => 400],
            ['type_operation_id' => 3, 'montant_min' => 20001,  'montant_max' => 100000, 'frais' => 750],
        ]);

        // --- Comptes clients (solde initialisé à 0, mis à jour après les transactions) ---
        $this->db->table('comptes')->insertBatch([
            ['numero_telephone' => '033123456', 'nom' => 'Rakoto', 'prenom' => 'Jean',   'solde' => 0],
            ['numero_telephone' => '037456789', 'nom' => 'Rasoa',  'prenom' => 'Marie',  'solde' => 0],
            ['numero_telephone' => '033987654', 'nom' => 'Andry',  'prenom' => 'Paul',   'solde' => 0],
            ['numero_telephone' => '037111222', 'nom' => 'Hery',   'prenom' => 'Claire', 'solde' => 0],
            ['numero_telephone' => '033333444', 'nom' => 'Rabe',   'prenom' => 'Daniel', 'solde' => 0],
        ]);

        // --- Transactions ---
        // 2 lignes par transfert (source + destination)
        // solde_apres calculé pour chaque ligne
        // baremes_frais_id: 1=depot(100-10000), 2=depot(10001-50000), 3=depot(50001-200000)
        //                   4=retrait(100-5000), 5=retrait(5001-20000), 6=retrait(20001-50000)
        //                   7=transfert(100-5000), 8=transfert(5001-20000), 9=transfert(20001-100000)
        $this->db->table('transactions')->insertBatch([
            // Rakoto: depot 15000
            [
                'compte_id'             => 1,
                'type_operation_id'     => 1,
                'montant'               => 15000,
                'baremes_frais_id'      => 2,
                'compte_destination_id' => null,
                'solde_apres'           => 15000,
                'date_operation'        => '2026-06-01 10:00:00',
            ],
            // Rasoa: depot 10000
            [
                'compte_id'             => 2,
                'type_operation_id'     => 1,
                'montant'               => 10000,
                'baremes_frais_id'      => 1,
                'compte_destination_id' => null,
                'solde_apres'           => 10000,
                'date_operation'        => '2026-06-02 09:00:00',
            ],
            // Rasoa: retrait 3000
            [
                'compte_id'             => 2,
                'type_operation_id'     => 2,
                'montant'               => 3000,
                'baremes_frais_id'      => 4,
                'compte_destination_id' => null,
                'solde_apres'           => 7000,
                'date_operation'        => '2026-06-03 09:00:00',
            ],
            // Andry: depot 20000
            [
                'compte_id'             => 3,
                'type_operation_id'     => 1,
                'montant'               => 20000,
                'baremes_frais_id'      => 2,
                'compte_destination_id' => null,
                'solde_apres'           => 20000,
                'date_operation'        => '2026-06-04 10:00:00',
            ],
            // Andry → Hery: transfert 5000 (source)
            [
                'compte_id'             => 3,
                'type_operation_id'     => 3,
                'montant'               => 5000,
                'baremes_frais_id'      => 7,
                'compte_destination_id' => 4,
                'solde_apres'           => 15000,
                'date_operation'        => '2026-06-05 14:30:00',
            ],
            // Hery ← Andry: transfert 5000 (destination)
            [
                'compte_id'             => 4,
                'type_operation_id'     => 3,
                'montant'               => 5000,
                'baremes_frais_id'      => 7,
                'compte_destination_id' => 3,
                'solde_apres'           => 5000,
                'date_operation'        => '2026-06-05 14:30:00',
            ],
            // Hery: depot 20000
            [
                'compte_id'             => 4,
                'type_operation_id'     => 1,
                'montant'               => 20000,
                'baremes_frais_id'      => 2,
                'compte_destination_id' => null,
                'solde_apres'           => 25000,
                'date_operation'        => '2026-06-06 11:00:00',
            ],
            // Rabe: depot 5000
            [
                'compte_id'             => 5,
                'type_operation_id'     => 1,
                'montant'               => 5000,
                'baremes_frais_id'      => 1,
                'compte_destination_id' => null,
                'solde_apres'           => 5000,
                'date_operation'        => '2026-06-07 08:00:00',
            ],
            // Rabe: retrait 2000
            [
                'compte_id'             => 5,
                'type_operation_id'     => 2,
                'montant'               => 2000,
                'baremes_frais_id'      => 4,
                'compte_destination_id' => null,
                'solde_apres'           => 3000,
                'date_operation'        => '2026-06-08 08:00:00',
            ],
            // Rakoto → Andry: transfert 3000 (source)
            [
                'compte_id'             => 1,
                'type_operation_id'     => 3,
                'montant'               => 3000,
                'baremes_frais_id'      => 7,
                'compte_destination_id' => 3,
                'solde_apres'           => 12000,
                'date_operation'        => '2026-06-09 16:00:00',
            ],
            // Andry ← Rakoto: transfert 3000 (destination)
            [
                'compte_id'             => 3,
                'type_operation_id'     => 3,
                'montant'               => 3000,
                'baremes_frais_id'      => 7,
                'compte_destination_id' => 1,
                'solde_apres'           => 18000,
                'date_operation'        => '2026-06-09 16:00:00',
            ],
        ]);

        // --- Mise à jour des soldes des comptes ---
        // Le solde de chaque compte = solde_apres de sa dernière transaction
        $this->db->query('
            UPDATE comptes SET solde = (
                SELECT t.solde_apres FROM transactions t
                WHERE t.compte_id = comptes.id
                ORDER BY t.id DESC LIMIT 1
            )
        ');
    }
}
