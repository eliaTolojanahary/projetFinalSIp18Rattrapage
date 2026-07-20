<?php

namespace App\Models;

use CodeIgniter\Model;

class ConfigurationOperatorModel extends Model
{
    // --- Prefixes ---
    public function getPrefixes(): array
    {
        return $this->db->table('prefixes')
            ->orderBy('id', 'ASC')
            ->get()
            ->getResultArray();
    }

    public function insertPrefix(array $data): bool
    {
        return $this->db->table('prefixes')->insert($data);
    }

    public function deletePrefix(int $id): bool
    {
        return $this->db->table('prefixes')->where('id', $id)->delete();
    }

    public function togglePrefix(int $id): bool
    {
        $row = $this->db->table('prefixes')->where('id', $id)->get()->getRowArray();
        if (! $row) {
            return false;
        }
        return $this->db->table('prefixes')
            ->where('id', $id)
            ->update(['actif' => $row['actif'] ? 0 : 1]);
    }

    // --- Types d'opérations ---
    public function getTypesOperations(): array
    {
        return $this->db->table('types_operations')
            ->orderBy('id', 'ASC')
            ->get()
            ->getResultArray();
    }

    // --- Barèmes de frais ---
    public function getBaremes(): array
    {
        return $this->db->table('baremes_frais bf')
            ->select('bf.*, to2.libelle AS type_libelle, to2.code AS type_code')
            ->join('types_operations to2', 'bf.type_operation_id = to2.id')
            ->orderBy('bf.type_operation_id', 'ASC')
            ->orderBy('bf.montant_min', 'ASC')
            ->get()
            ->getResultArray();
    }

    public function insertBareme(array $data): bool
    {
        return $this->db->table('baremes_frais')->insert($data);
    }

    public function deleteBareme(int $id): bool
    {
        return $this->db->table('baremes_frais')->where('id', $id)->delete();
    }

    public function getFraisForMontant(int $typeOperationId, float $montant): ?array
    {
        return $this->db->table('baremes_frais')
            ->where('type_operation_id', $typeOperationId)
            ->where('montant_min <=', $montant)
            ->where('montant_max >=', $montant)
            ->get()
            ->getRowArray();
    }

    public function getPrefixesActifs(): array
    {
        return $this->db->table('prefixes')
            ->where('actif', 1)
            ->orderBy('prefixe', 'ASC')
            ->get()
            ->getResultArray();
    }
}
