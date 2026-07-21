<?php

namespace App\Controllers;

use App\Models\TransactionOperatorModel;
use App\Models\CompteOperatorModel;

class DashboardOperator extends BaseController
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

        $transactionModel = new TransactionOperatorModel();
        $compteModel = new CompteOperatorModel();

        $data = [
            'totalFrais'         => $transactionModel->totalFrais(),
            'totalFraisAutre'    => $transactionModel->totalFraisAutre(),
            'montantsParOperateur' => $transactionModel->montantsParOperateur(),
            'nbClients'          => $compteModel->countAllClients(),
            'totalMontant'       => $compteModel->totalMontantDetenu(),
        ];

        return view('operator/dashboard', $data);
    }
}
