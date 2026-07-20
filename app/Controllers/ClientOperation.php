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
        $compteId = session()->get('compte_id');
        $montantTotal = (float) $this->request->getPost('montant_total');
        $destinataires = $this->request->getPost('destinataires');
        $inclureFraisRetrait = $this->request->getPost('inclure_frais_retrait');

        $destinataires = array_filter($destinataires, function($dest) {
            return !empty($dest['numero']);
        });

        if (empty($destinataires)) {
            return redirect()->back()->with('error', 'Sélectionnez au moins un destinataire.');
        }

        if ($montantTotal <= 0) {
            return redirect()->back()->with('error', 'Montant total invalide.');
        }

        $compteModel = new ClientModel();
        $compteOrigine = $compteModel->find($compteId);
        
        if (!$compteOrigine) {
            return redirect()->to('/login')->with('error', 'Session expirée.');
        }

        $numeros = array_column($destinataires, 'numero');
        if (count($numeros) !== count(array_unique($numeros))) {
            return redirect()->back()->with('error', 'Vous ne pouvez pas sélectionner deux fois le même destinataire.');
        }
        $montantDistribue = 0;
        foreach ($destinataires as $dest) {
            $montantDistribue += (float) $dest['montant'];
        }

        if (abs($montantDistribue - $montantTotal) > 1) {
            return redirect()->back()->with('error', 'La répartition ne correspond pas au montant total.');
        }

        $db = db_connect();
        $db->transStart();

        $typeOperationModel = new ClientOperationModel();
        $baremeModel = new ClientBaremeModel();
        $prefixeModel = new ClientPrefixeModel();
        $commissionModel = new ClientCommissionModel();
        $transactionModel = new ClientTransactionModel();
        
        $typeId = $typeOperationModel->idParCode('transfert');

        $bareme = $baremeModel->calculerFrais($typeId, $montantTotal);
        if (!$bareme) {
            $db->transRollback();
            return redirect()->back()->with('error', 'Aucun barème pour ce montant.');
        }

        $fraisRetraitTotal = 0;
        if ($inclureFraisRetrait) {
            $retraitTypeId = $typeOperationModel->idParCode('retrait');
            $baremeRetrait = $baremeModel->calculerFrais($retraitTypeId, $montantTotal);
            if ($baremeRetrait) {
                $fraisRetraitTotal = $baremeRetrait['frais'];
            }
        }
        $commissionTotal = 0;
        foreach ($destinataires as $dest) {
            $numero = trim($dest['numero']);
            $prefixe = substr($numero, 0, 3);
            $prefixeInfo = $prefixeModel->where('prefixe', $prefixe)->first();
            
            if ($prefixeInfo && $prefixeInfo['est_operateur_principal'] == 0) {
                $commission = $commissionModel->where('id_prefixe', $prefixeInfo['id'])->first();
                if ($commission) {
                    $montant = (float) $dest['montant'];
                    $commissionTotal += ($montant * $commission['pourcentage']) / 100;
                }
            }
        }

        $montantTotalAvecFrais = $montantTotal + $bareme['frais'] + $fraisRetraitTotal + $commissionTotal;

        if ($compteOrigine['solde'] < $montantTotalAvecFrais) {
            $db->transRollback();
            return redirect()->back()->with('error', 'Solde insuffisant. Solde : ' . number_format($compteOrigine['solde'], 0, ',', ' ') . ' Ar, besoin : ' . number_format($montantTotalAvecFrais, 0, ',', ' ') . ' Ar');
        }

    
        foreach ($destinataires as $destinataire) {
            $numero = trim($destinataire['numero']);
            $montant = (float) $destinataire['montant'];

    
            if ($numero === $compteOrigine['numero_telephone']) {
                $db->transRollback();
                return redirect()->back()->with('error', 'Transfert vers soi-même impossible.');
            }

            if ($montant <= 0) {
                $db->transRollback();
                return redirect()->back()->with('error', 'Montant invalide pour un destinataire.');
            }
             
            $prefixe = substr($numero, 0, 3);
            $prefixeInfo = $prefixeModel->where('prefixe', $prefixe)->first();
            
            $commission = 0;
            if ($prefixeInfo && $prefixeInfo['est_operateur_principal'] == 0) {
                $commissionData = $commissionModel->where('id_prefixe', $prefixeInfo['id'])->first();
                if ($commissionData) {
                    $commission = ($montant * $commissionData['pourcentage']) / 100;
                }
            }

            $compteDestinataire = $compteModel->trouverOuCreerCompte($numero);

            $compteModel->update($compteDestinataire['id'], [
                'solde' => $compteDestinataire['solde'] + $montant
            ]);

            // Enregistrer la transaction
            $transactionModel->insert([
                'compte_id' => $compteOrigine['id'],
                'type_operation_id' => $typeId,
                'montant' => $montant,
                'baremes_frais_id' => $bareme['id'],
                'solde_apres' => $compteOrigine['solde'] - $montantTotalAvecFrais,
                'compte_destination_id' => $compteDestinataire['id'],
                'prefixe_destination_id' => $prefixeInfo ? $prefixeInfo['id'] : null,
                'inclure_frais_retrait' => $inclureFraisRetrait ? 1 : 0,
                'commission' => $commission
            ]);
        }


        $compteModel->update($compteOrigine['id'], [
            'solde' => $compteOrigine['solde'] - $montantTotalAvecFrais
        ]);

        $db->transComplete();

        if (!$db->transStatus()) {
            return redirect()->back()->with('error', 'Le transfert a échoué.');
        }

        return redirect()->to('/transfert')->with('success', 
            'Transfert de ' . number_format($montantTotal, 0, ',', ' ') . ' Ar effectué vers ' . count($destinataires) . ' destinataire(s).' .
            ($fraisRetraitTotal > 0 ? ' Frais retrait: ' . number_format($fraisRetraitTotal, 0, ',', ' ') . ' Ar' : '') .
            ($commissionTotal > 0 ? ' Commission: ' . number_format($commissionTotal, 0, ',', ' ') . ' Ar' : '')
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