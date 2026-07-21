<?php

namespace App\Controllers;

use App\Models\CompteOperatorModel;
use CodeIgniter\Exceptions\PageNotFoundException;

class ClientsOperator extends BaseController
{
    private function checkAuth()
    {
        if (!session()->get('operator_logged_in')) {
            return redirect()->to('/operator/login');
        }
        return null;
    }

    public function index()
    {
        $redirect = $this->checkAuth();
        if ($redirect) return $redirect;

        $compteModel = new CompteOperatorModel();
        $clients = $compteModel->getSituationCompte();

        return view('operator/clients', [
            'clients' => $clients,
        ]);
    }

    public function show(int $id)
    {
        $redirect = $this->checkAuth();
        if ($redirect) return $redirect;

        $compteModel = new CompteOperatorModel();
        $client = $compteModel->getSituationCompteParIdWithTransactions($id);

        if ($client === null) {
            throw PageNotFoundException::forPageNotFound('Client introuvable.');
        }

        return view('operator/client_detail', [
            'client' => $client,
        ]);
    }
}
