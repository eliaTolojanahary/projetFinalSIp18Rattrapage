<?php

namespace App\Controllers;

use App\Models\EmpruntModel;
use App\Models\LivreModel;
use CodeIgniter\Exceptions\PageNotFoundException;
use InvalidArgumentException;

class Livres extends BaseController
{
    public function index()
    {
        $livreModel = new LivreModel();

        $motCle = trim((string) $this->request->getGet('mot_cle'));
        $categorie = trim((string) $this->request->getGet('categorie'));

        $isRecherche = ($motCle !== '' || $categorie !== '');

        if ($isRecherche) {
            $livres = $livreModel->rechercherLivres($motCle, $categorie);
            $pager = null;
        } else {
            $livres = $livreModel->livresPagine();
            $pager = $livreModel->pager;
        }

        return view('livres/index', [
            'livres' => $livres,
            'pager' => $pager,
            'mot_cle' => $motCle,
            'categorie' => $categorie,
            'isRecherche' => $isRecherche,
        ]);
    }

    public function show(int $id)
    {
        $livreModel = new LivreModel();
        $empruntModel = new EmpruntModel();

        $livre = $livreModel->find($id);

        if ($livre === null) {
            throw PageNotFoundException::forPageNotFound('Livre introuvable.');
        }

        $dernierEmprunt = $empruntModel->dernierEmpruntPourLivre($id);

        return view('livres/detail', [
            'livre' => $livre,
            'dernierEmprunt' => $dernierEmprunt,
        ]);
    }

    public function create()
    {
        return view('livres/create');
    }

    public function store()
    {
        $livreModel = new LivreModel();

        $donnees = [
            'titre' => trim((string) $this->request->getPost('titre')),
            'auteur' => trim((string) $this->request->getPost('auteur')),
            'isbn' => trim((string) $this->request->getPost('isbn')),
            'annee_publication' => trim((string) $this->request->getPost('annee_publication')),
            'categorie' => trim((string) $this->request->getPost('categorie')),
            'resume' => trim((string) $this->request->getPost('resume')),
            'statut' => trim((string) $this->request->getPost('statut')) ?: 'disponible',
        ];

        $anneeCourante = (int) date('Y');
        $annee = (int) $donnees['annee_publication'];

        if ($annee > $anneeCourante) {
            return redirect()->back()->withInput()->with('error', 'L\'année de publication ne peut pas être dans le futur.');
        }

        $fichierCouverture = $this->request->getFile('couverture');
        if ($fichierCouverture !== null && $fichierCouverture->isValid() && ! $fichierCouverture->hasMoved()) {
            $tailleMax = 2 * 1024 * 1024;
            $mimesAutorises = ['image/jpeg', 'image/png', 'image/webp'];

            if ($fichierCouverture->getSize() > $tailleMax) {
                return redirect()->back()->withInput()->with('error', 'La couverture ne doit pas dépasser 2 Mo.');
            }

            if (! in_array($fichierCouverture->getClientMimeType(), $mimesAutorises, true)) {
                return redirect()->back()->withInput()->with('error', 'La couverture doit être une image jpeg, png ou webp.');
            }

            $nomFichier = $fichierCouverture->getRandomName();
            $fichierCouverture->move(FCPATH . 'uploads', $nomFichier);
            $donnees['couverture_fichier'] = $nomFichier;
        }

        try {
            if (! $livreModel->insert($donnees)) {
                return redirect()->back()->withInput()->with('errors', $livreModel->errors());
            }
        } catch (InvalidArgumentException $exception) {
            return redirect()->back()->withInput()->with('error', $exception->getMessage());
        }

        return redirect()->to('/')->with('success', 'Livre ajouté avec succès.');
    }

    public function delete(int $id)
    {
        $livreModel = new LivreModel();

        $livre = $livreModel->find($id);
        if ($livre === null) {
            return redirect()->to('/')->with('error', 'Le livre demandé est introuvable.');
        }

        $livreModel->delete($id);

        return redirect()->to('/')->with('success', 'Livre supprimé avec succès.');
    }

    public function preter(int $id)
    {
        $livreModel = new LivreModel();
        $empruntModel = new EmpruntModel();

        $livre = $livreModel->find($id);
        if ($livre === null) {
            return redirect()->to('/')->with('error', 'Le livre demandé est introuvable.');
        }

        if (($livre['statut'] ?? null) !== 'disponible') {
            return redirect()->back()->with('error', 'Ce livre est déjà prêté.');
        }

        $nomEmprunteur = trim((string) $this->request->getPost('nom_emprunteur'));
        if ($nomEmprunteur === '') {
            return redirect()->back()->withInput()->with('error', 'Le nom de l\'emprunteur est obligatoire.');
        }

        $db = db_connect();
        $db->transStart();

        $empruntModel->insert([
            'livre_id' => $id,
            'nom_emprunteur' => $nomEmprunteur,
            'date_emprunt' => date('Y-m-d'),
            'date_retour' => null,
        ]);

        $livreModel->update($id, [
            'statut' => 'prete',
        ]);

        $db->transComplete();

        if (! $db->transStatus()) {
            return redirect()->back()->with('error', 'Le prêt du livre a échoué.');
        }

        return redirect()->to('/livres/' . $id)->with('success', 'Le livre a été prêté avec succès.');
    }

    public function retour(int $id)
    {
        $livreModel = new LivreModel();
        $empruntModel = new EmpruntModel();

        $livre = $livreModel->find($id);
        if ($livre === null) {
            return redirect()->to('/')->with('error', 'Le livre demandé est introuvable.');
        }

        $empruntActif = $empruntModel->where('livre_id', $id)
            ->where('date_retour', null)
            ->orderBy('date_emprunt', 'DESC')
            ->orderBy('id', 'DESC')
            ->first();

        if ($empruntActif === null) {
            return redirect()->back()->with('error', 'Aucun emprunt actif n\'a été trouvé pour ce livre.');
        }

        $db = db_connect();
        $db->transStart();

        $empruntModel->update($empruntActif['id'], [
            'date_retour' => date('Y-m-d'),
        ]);

        $livreModel->update($id, [
            'statut' => 'disponible',
        ]);

        $db->transComplete();

        if (! $db->transStatus()) {
            return redirect()->back()->with('error', 'Le retour du livre a échoué.');
        }

        return redirect()->to('/livres/' . $id)->with('success', 'Le livre a été marqué comme retourné.');
    }
}
