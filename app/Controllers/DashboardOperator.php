<?php

namespace App\Controllers;

use App\Models\TransactionOperatorModel;

class DashboardOperator extends BaseController
{
    public function index()
    {
        $transactionModel = new TransactionOperatorModel();
        $totalFrais = $transactionModel->totalFrais();

        return view('operator/dashboard', [
            'totalFrais' => $totalFrais,
        ]);
    }
}
