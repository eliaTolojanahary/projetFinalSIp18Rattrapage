<?php

namespace App\Controllers;

use App\Models\ClientModel;
use App\Models\ClientPrefixeModel;

class Client extends BaseController
{
    public function form()
    {
    
        if (session()->get('compte_id')) {
            return redirect()->to('/depot');
        }

        return view('clients/login');
    }

    public function login()
    {
        $numero = trim((string) $this->request->getPost('numero_telephone'));

        if ($numero === '') {
            return redirect()->back()->withInput()->with('error', 'Le numéro est obligatoire.');
        }

        $prefixeModel = new ClientPrefixeModel();
        if (! $prefixeModel->estValide($numero)) {
            return redirect()->back()->withInput()->with('error', 'Ce préfixe n\'est pas pris en charge par l\'opérateur.');
        }

        $compteModel = new ClientModel();
        $compte = $compteModel->trouverOuCreerCompte($numero);

        
        session()->set([
            'compte_id'        => $compte['id'],
            'numero_telephone' => $compte['numero_telephone'],
        ]);

        return redirect()->to('/depot');
    }

    public function logout()
{
    session()->destroy();
    return redirect()->to('/');
}
}