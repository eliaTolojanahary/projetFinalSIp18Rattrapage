<?php

namespace App\Controllers;

use App\Models\TransactionOperatorModel;
use App\Models\CompteOperatorModel;

class DashboardOperator extends BaseController
{
    public function index()
    {
        $transactionModel = new TransactionOperatorModel();
        $compteModel = new CompteOperatorModel();

        $data = [
            'totalFrais'       => $transactionModel->totalFrais(),
            'nbClients'        => $compteModel->countAllClients(),
            'totalMontant'     => $compteModel->totalMontantDetenu(),
        ];

        return view('operator/dashboard', $data);
    }
}
