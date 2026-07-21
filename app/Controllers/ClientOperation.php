<?php

namespace App\Controllers;

use App\Models\ClientModel;
use App\Models\ClientPrefixeModel;
use App\Models\ClientOperationModel;
use App\Models\ClientBaremeModel;
use App\Models\ClientTransactionModel;
use App\Models\ClientCommissionModel;

class ClientOperation extends BaseController
{
    public function depotForm()
    {
        $compteId = session()->get('compte_id');
        if (!$compteId) {
            return redirect()->to('/');
        }
        $compteModel = new ClientModel();
        $compte = $compteModel->find($compteId);
        if (!$compte) {
            return redirect()->to('/')->with('error', 'Compte introuvable.');
        }

        return view('clients/depot', ['compte' => $compte]);
    }

public function depotStore()
{
    $compteId = session()->get('compte_id');
    if (!$compteId) {
        return redirect()->to('/');
    }
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
    if (!$compteId) {
        return redirect()->to('/');
    }
    $compteModel = new ClientModel();
    $compte = $compteModel->find($compteId);

    return view('clients/retrait', ['compte' => $compte]);
}

public function retraitStore()
{
    $compteId = session()->get('compte_id');
    if (!$compteId) {
        return redirect()->to('/');
    }
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

        $prefixeModel = new ClientPrefixeModel();
        $commissionModel = new ClientCommissionModel();
        
        $prefixes = $prefixeModel->findAll();
        $prefixesInfos = [];
        
        foreach ($prefixes as $prefixe) {
            $commission = $commissionModel->where('id_prefixe', $prefixe['id'])->first();
            $prefixesInfos[$prefixe['prefixe']] = [
                'id' => $prefixe['id'],
                'libelle' => $prefixe['libelle'],
                'est_operateur_principal' => $prefixe['est_operateur_principal'],
                'commission' => $commission ? $commission['pourcentage'] : 0
            ];
        }

        return view('clients/transfert', [
            'compte'        => $compte,
            'comptes'       => $compteModel->findAll(),
            'prefixesInfos' => $prefixesInfos,
        ]);
    }
public function transfertStore()
{
    $compteId              = session()->get('compte_id');
    $montantTotal          = (float) $this->request->getPost('montant');
    $numeros               = $this->request->getPost('numero_destinataire');
    $inclureFraisRetrait   = (bool) $this->request->getPost('inclure_frais_retrait');
    $dateTransfert         = $this->request->getPost('date_transfert') ?: date('Y-m-d H:i:s');

    $numeros = is_array($numeros) ? array_filter(array_map('trim', $numeros)) : [];
    $numeros = array_values($numeros);

    if (empty($numeros)) {
        return redirect()->back()->withInput()->with('error', 'Sélectionnez au moins un destinataire.');
    }

    if (count($numeros) !== count(array_unique($numeros))) {
        return redirect()->back()->withInput()->with('error', 'Vous ne pouvez pas sélectionner deux fois le même destinataire.');
    }

    if ($montantTotal <= 0) {
        return redirect()->back()->withInput()->with('error', 'Montant total invalide.');
    }

    $compteModel = new ClientModel();
    $compteOrigine = $compteModel->find($compteId);

    if (! $compteOrigine) {
        return redirect()->to('/')->with('error', 'Session expirée.');
    }

    $prefixeModel        = new ClientPrefixeModel();
    $typeOperationModel  = new ClientOperationModel();
    $baremeModel         = new ClientBaremeModel();
    $commissionModel     = new ClientCommissionModel();
    $transactionModel    = new ClientTransactionModel();

    $typeTransfertId = $typeOperationModel->idParCode('transfert');
    $typeRetraitId   = $typeOperationModel->idParCode('retrait');

    $nombreDestinataires    = count($numeros);
    $montantParDestinataire = round($montantTotal / $nombreDestinataires, 2);

    $operations = [];
    $totalAPrelevier = 0;

    foreach ($numeros as $numero) {
        if ($numero === $compteOrigine['numero_telephone']) {
            return redirect()->back()->withInput()->with('error', 'Transfert vers soi-même impossible.');
        }

        $prefixe = $prefixeModel->trouverPrefixe($numero);
        if ($prefixe === null) {
            return redirect()->back()->withInput()->with('error', "Préfixe non valable pour le numéro $numero.");
        }

        $estPrincipal = $prefixe['est_operateur_principal'];
   
        $fraisRetrait = 0;
        $montantATransferer = $montantParDestinataire;

        if ($inclureFraisRetrait && $estPrincipal ==1) {
            $baremeRetrait = $baremeModel->calculerFrais($typeRetraitId, $montantParDestinataire);
            $fraisRetrait = $baremeRetrait['frais'] ?? 0;
            $montantATransferer = $montantParDestinataire + $fraisRetrait;
        }

        $baremeTransfert = $baremeModel->calculerFrais($typeTransfertId, $montantATransferer);
        if ($baremeTransfert === null) {
            return redirect()->back()->withInput()->with('error', "Aucun barème de transfert pour le montant destiné à $numero.");
        }
        $fraisTransfert = $baremeTransfert['frais'];

        $commission = 0;
        if ($estPrincipal == 0) {
          
            $commissionData = $commissionModel->where('id_prefixe', $prefixe['id'])->first();
                      
            if ($commissionData) {
                $commission = round($montantParDestinataire * $commissionData['pourcentage'] / 100, 2);
            }
        }

        $totalPourCeDestinataire = $montantATransferer + $fraisTransfert + $commission;
        $totalAPrelevier += $totalPourCeDestinataire;

        $operations[] = [
            'numero'                => $numero,
            'prefixe_id'            => $prefixe['id'],
            'montant_recu'          => $montantParDestinataire,
            'montant_a_transferer'  => $montantATransferer,
            'inclure_frais_retrait' => $inclureFraisRetrait ? 1 : 0,
            'bareme_transfert_id'   => $baremeTransfert['id'],
            'frais_transfert'       => $fraisTransfert,
            'commission'            => $commission,
        ];
    }
    if ($compteOrigine['solde'] < $totalAPrelevier) {
        return redirect()->back()->withInput()->with(
            'error',
            'Solde insuffisant. Solde : ' . number_format($compteOrigine['solde'], 0, ',', ' ') .
            ' Ar, besoin : ' . number_format($totalAPrelevier, 0, ',', ' ') . ' Ar'
        );
    }

    $db = db_connect();
    $db->transStart();

    $soldeCourant = $compteOrigine['solde'];
    $fraisRetraitTotal = 0;
    $commissionTotal   = 0;

    foreach ($operations as $op) {
        $compteDestinataire = $compteModel->trouverOuCreerCompte($op['numero']);

        $soldeCourant -= ($op['montant_a_transferer'] + $op['frais_transfert'] + $op['commission']);
        $compteModel->update($compteOrigine['id'], ['solde' => $soldeCourant]);

        $nouveauSoldeDestinataire = $compteDestinataire['solde'] + $op['montant_recu'];
        $compteModel->update($compteDestinataire['id'], ['solde' => $nouveauSoldeDestinataire]);

        $transactionModel->insert([
            'compte_id'              => $compteOrigine['id'],
            'type_operation_id'      => $typeTransfertId,
            'montant'                => $op['montant_a_transferer'],
            'baremes_frais_id'       => $op['bareme_transfert_id'],
            'compte_destination_id'  => $compteDestinataire['id'],
            'prefixe_destination_id' => $op['prefixe_id'],
            'inclure_frais_retrait'  => $op['inclure_frais_retrait'],
            'commission'             => $op['commission'],
            'solde_apres'            => $soldeCourant,
            'date_operation'         => $dateTransfert,
        ]);

        if ($op['inclure_frais_retrait']) {
            $fraisRetraitTotal += ($op['montant_a_transferer'] - $op['montant_recu']);
        }
        $commissionTotal += $op['commission'];
    }

    $db->transComplete();

    if (! $db->transStatus()) {
        return redirect()->back()->with('error', 'Le transfert a échoué.');
    }

    return redirect()->to('/transfert')->with(
        'success',
        'Transfert de ' . number_format($montantTotal, 0, ',', ' ') . ' Ar effectué vers ' . $nombreDestinataires . ' destinataire(s).' .
        ($fraisRetraitTotal > 0 ? ' Frais retrait inclus : ' . number_format($fraisRetraitTotal, 0, ',', ' ') . ' Ar' : '') .
        ($commissionTotal > 0 ? ' Commission : ' . number_format($commissionTotal, 0, ',', ' ') . ' Ar' : '')
    );
}
    public function historique()
{
    $compteId = session()->get('compte_id');
    if (!$compteId) {
        return redirect()->to('/');
    }

    $transactionModel = new ClientTransactionModel();
    $historique = $transactionModel->historiquePourCompte($compteId);

    return view('clients/historique', ['historique' => $historique]);
}
}