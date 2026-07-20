<?php

namespace App\Controllers;

use App\Models\ConfigurationOperatorModel;

class ConfigurationOperator extends BaseController
{
    public function index()
    {
        $model = new ConfigurationOperatorModel();

        $data = [
            'prefixes'         => $model->getPrefixes(),
            'types_operations' => $model->getTypesOperations(),
            'baremes'          => $model->getBaremes(),
        ];

        return view('operator/configuration', $data);
    }

    // --- Prefixes ---
    public function storePrefix()
    {
        $model = new ConfigurationOperatorModel();
        $prefixe = trim($this->request->getPost('prefixe'));
        $libelle = trim($this->request->getPost('libelle'));
        $estPrincipal = $this->request->getPost('est_operateur_principal') ? 1 : 0;

        if ($prefixe === '') {
            return redirect()->back()->withInput()->with('error', 'Préfixe et libellé requis.');
        }

        $model->insertPrefix([
            'prefixe'                  => $prefixe,
            'libelle'                  => $libelle,
            'est_operateur_principal'  => $estPrincipal,
        ]);

        return redirect()->to('configuration')->with('success', 'Préfixe ajouté.');
    }

    public function deletePrefix(int $id)
    {
        $model = new ConfigurationOperatorModel();
        $model->deletePrefix($id);

        return redirect()->to('configuration')->with('success', 'Préfixe supprimé.');
    }

    public function togglePrefix(int $id)
    {
        $model = new ConfigurationOperatorModel();
        $model->togglePrefix($id);

        return redirect()->to('configuration')->with('success', 'Statut du préfixe mis à jour.');
    }

    public function togglePrincipal(int $id)
    {
        $model = new ConfigurationOperatorModel();
        $model->togglePrincipal($id);

        return redirect()->to('configuration')->with('success', 'Statut principal mis à jour.');
    }

    // --- Barèmes ---
    public function storeBareme()
    {
        $model = new ConfigurationOperatorModel();

        $type_operation_id = (int) $this->request->getPost('type_operation_id');
        $montant_min       = (float) $this->request->getPost('montant_min');
        $montant_max       = (float) $this->request->getPost('montant_max');
        $frais             = (float) $this->request->getPost('frais');

        if ($type_operation_id <= 0 || $montant_min <= 0 || $montant_max <= 0 || $frais <= 0) {
            return redirect()->back()->withInput()->with('error', 'Tous les champs sont requis et doivent être positifs.');
        }

        $model->insertBareme([
            'type_operation_id' => $type_operation_id,
            'montant_min'       => $montant_min,
            'montant_max'       => $montant_max,
            'frais'             => $frais,
        ]);

        return redirect()->to('configuration')->with('success', 'Barème ajouté.');
    }

    public function deleteBareme(int $id)
    {
        $model = new ConfigurationOperatorModel();
        $model->deleteBareme($id);

        return redirect()->to('configuration')->with('success', 'Barème supprimé.');
    }
}
