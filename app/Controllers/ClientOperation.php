<?php

namespace App\Controllers;

use App\Models\ClientModel;
use App\Models\ClientPrefixeModel;
use App\Models\ClientOperationModel;
use App\Models\ClientBaremeModel;
use App\Models\ClientTransactionModel;

class ClientOperation extends BaseController
{
    public function depotForm()
    {
        $compteId = session()->get('compte_id');
    $compteModel = new ClientModel();
    $compte = $compteModel->find($compteId);

    return view('clients/depot', ['compte' => $compte]);
    }

public function depotStore()
{
$compteId = session()->get('compte_id');
    $montant  = (float) $this->request->getPost('montant');

    if ($montant <= 0) {
        return redirect()->back()->withInput()->with('error', 'Le montant doit être supérieur à 0.');
    }

    $compteModel = new ClientModel();
    $compte = $compteModel->find($compteId);

    $typeOperationModel = new ClientOperationModel();
    $typeId = $typeOperationModel->idParCode('depot');

    $baremeModel = new ClientBaremeModel();
    $bareme = 0;

    if ($bareme === null) {
        return redirect()->back()->withInput()->with('error', 'Aucun barème trouvé pour ce montant.');
    }

    $nouveauSolde = $compte['solde'] + $montant - $bareme;

    $db = db_connect();
    $db->transStart();

    $compteModel->update($compte['id'], ['solde' => $nouveauSolde]);

    $transactionModel = new ClientTransactionModel();
    $transactionModel->insert([
        'compte_id'             => $compte['id'],
        'type_operation_id'     => $typeId,
        'montant'               => $montant,
        'baremes_frais_id'      => 0,
        'compte_destination_id' => null,
        'solde_apres'           => $nouveauSolde,
        'date_operation'        => date('Y-m-d H:i:s'),
    ]);

    $db->transComplete();

    if (! $db->transStatus()) {
        return redirect()->back()->with('error', 'Le dépôt a échoué.');
    }

    return redirect()->to('/depot')->with('success', 'Dépôt de ' . $montant . ' Ar effectué.');
}

public function calculerFraisAjax()
{
    $montant = (float) $this->request->getGet('montant');
    $type    = $this->request->getGet('type') ?: 'depot';

    $typeOperationModel = new ClientOperationModel();
    $typeId = $typeOperationModel->idParCode($type);

    $baremeModel = new ClientBaremeModel();
    $bareme = $baremeModel->calculerFrais($typeId, $montant);

    return $this->response->setJSON([
        'debug_type'    => $type,
        'debug_typeId'  => $typeId,
        'debug_montant' => $montant,
        'debug_bareme'  => $bareme,
        'frais'         => $bareme['frais'] ?? 0,
    ]);
}
    public function retraitForm()
{
    $compteId = session()->get('compte_id');
    $compteModel = new ClientModel();
    $compte = $compteModel->find($compteId);

    return view('clients/retrait', ['compte' => $compte]);
}

public function retraitStore()
{
    $compteId = session()->get('compte_id');
    $montant  = (float) $this->request->getPost('montant');

    if ($montant <= 0) {
        return redirect()->back()->withInput()->with('error', 'Le montant doit être supérieur à 0.');
    }

    $compteModel = new ClientModel();
    $compte = $compteModel->find($compteId);

    $typeOperationModel = new ClientOperationModel();
    $typeId = $typeOperationModel->idParCode('retrait');

    $baremeModel = new ClientBaremeModel();
    $bareme = $baremeModel->calculerFrais($typeId, $montant);

    if ($bareme === null) {
        return redirect()->back()->withInput()->with('error', 'Aucun barème trouvé pour ce montant.');
    }

    $montantTotal = $montant + $bareme['frais'];

    if ($compte['solde'] < $montantTotal) {
        return redirect()->back()->withInput()->with('error', 'Solde insuffisant pour ce retrait (montant + frais).');
    }

    $nouveauSolde = $compte['solde'] - $montantTotal;

    $db = db_connect();
    $db->transStart();

    $compteModel->update($compte['id'], ['solde' => $nouveauSolde]);

    $transactionModel = new ClientTransactionModel();
    $transactionModel->insert([
        'compte_id'             => $compte['id'],
        'type_operation_id'     => $typeId,
        'montant'               => $montant,
        'baremes_frais_id'      => $bareme['id'],
        'compte_destination_id' => null,
        'solde_apres'           => $nouveauSolde,
        'date_operation'        => date('Y-m-d H:i:s'),
    ]);

    $db->transComplete();

    if (! $db->transStatus()) {
        return redirect()->back()->with('error', 'Le retrait a échoué.');
    }

    return redirect()->to('/retrait')->with('success', 'Retrait de ' . $montant . ' Ar effectué.');
}

public function transfertForm()
{
    $compteId = session()->get('compte_id');
    $compteModel = new ClientModel();
    $compte = $compteModel->find($compteId);

    return view('clients/transfert', [
        'compte'  => $compte,
        'comptes' => $compteModel->findAll(), 
    ]);
}

public function transfertStore()
{
    $compteId       = session()->get('compte_id');
    $numeroDestinataire = trim((string) $this->request->getPost('numero_destinataire'));
    $montant        = (float) $this->request->getPost('montant');
    $dateTransfert  = $this->request->getPost('date_transfert') ?: date('Y-m-d H:i:s');

    if ($montant <= 0) {
        return redirect()->back()->withInput()->with('error', 'Le montant doit être supérieur à 0.');
    }

    $compteModel = new ClientModel();
    $compteOrigine = $compteModel->find($compteId);

    if ($numeroDestinataire === $compteOrigine['numero_telephone']) {
        return redirect()->back()->withInput()->with('error', 'Impossible de transférer vers son propre compte.');
    }

    $prefixeModel = new ClientPrefixeModel();
    if (! $prefixeModel->estValide($numeroDestinataire)) {
        return redirect()->back()->withInput()->with('error', 'Préfixe opérateur non valable pour le destinataire.');
    }

    $compteDestinataire = $compteModel->trouverOuCreerCompte($numeroDestinataire);

    $typeOperationModel = new ClientOperationModel();
    $typeId = $typeOperationModel->idParCode('transfert');

    $baremeModel = new ClientBaremeModel ();
    $bareme = $baremeModel->calculerFrais($typeId, $montant);

    if ($bareme === null) {
        
        return redirect()->back()->withInput()->with('error', 'Aucun barème trouvé pour ce montant.');
    }

    $montantTotal = $montant + $bareme['frais'];

    if ($compteOrigine['solde'] < $montantTotal) {
        return redirect()->back()->withInput()->with('error', 'Solde insuffisant pour ce transfert (montant + frais).');
    }

    $nouveauSoldeOrigine     = $compteOrigine['solde'] - $montantTotal;
    $nouveauSoldeDestinataire = $compteDestinataire['solde'] + $montant;

    $db = db_connect();
    $db->transStart();

    $compteModel->update($compteOrigine['id'], ['solde' => $nouveauSoldeOrigine]);
    $compteModel->update($compteDestinataire['id'], ['solde' => $nouveauSoldeDestinataire]);

    $transactionModel = new ClientTransactionModel();
    $transactionModel->insert([
        'compte_id'             => $compteOrigine['id'],
        'type_operation_id'     => $typeId,
        'montant'               => $montant,
        'baremes_frais_id'      => $bareme['id'],
        'compte_destination_id' => $compteDestinataire['id'],
        'solde_apres'           => $nouveauSoldeOrigine,
        'date_operation'        => $dateTransfert,
    ]);

    $db->transComplete();

    if (! $db->transStatus()) {
        return redirect()->back()->with('error', 'Le transfert a échoué.');
    }

    return redirect()->to('/transfert')->with('success', 'Transfert de ' . $montant . ' Ar vers ' . $numeroDestinataire . ' effectué.');
}
public function historique()
{
    $compteId = session()->get('compte_id');

    $transactionModel = new ClientTransactionModel();
    $historique = $transactionModel->historiquePourCompte($compteId);

    return view('clients/historique', ['historique' => $historique]);
}
}