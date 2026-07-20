<?php

namespace App\Controllers;

use App\Models\CompteOperatorModel;
use CodeIgniter\Exceptions\PageNotFoundException;

class ClientsOperator extends BaseController
{
    public function index()
    {
        $compteModel = new CompteOperatorModel();
        $clients = $compteModel->getSituationCompte();

        return view('operator/clients', [
            'clients' => $clients,
        ]);
    }

    public function show(int $id)
    {
        $compteModel = new CompteOperatorModel();
        $client = $compteModel->getSituationCompteParId($id);

        if ($client === null) {
            throw PageNotFoundException::forPageNotFound('Client introuvable.');
        }

        return view('operator/client_detail', [
            'client' => $client,
        ]);
    }
}
