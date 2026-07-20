# TODO - Application Gestion de Bibliotheque

## Etat actuel

- [x] Base `bibliotheque` + migrations `livres` et `emprunts`
- [x] Routes principales (catalogue, detail, ajout, suppression, pret, retour)
- [x] Model `LivreModel` (validation, recherche, pagination)
- [x] Model `EmpruntModel` (dernier emprunt)
- [x] Controleur `Livres` (CRUD + pret/retour)
- [x] Vues avec layout (`main`, `index`, `detail`, `create`)
- [x] CSRF global active + tokens CSRF dans les formulaires POST

## Priorite haute (a faire maintenant)

- [ ] Corriger l'environnement PHP local (passer a PHP 8.2+)
- [ ] Relancer les migrations avec le bon binaire PHP
- [ ] Demarrer le serveur (`php spark serve`) et verifier le chargement des pages
- [ ] Tester le flux complet: ajout -> pret -> retour -> suppression

## Verification fonctionnelle (criteres TP)

- [ ] CRUD complet fonctionnel sans erreur
- [ ] Validation des champs + messages d'erreur visibles
- [ ] Validation upload image (jpeg/png/webp, max 2 Mo)
- [ ] Changement de statut correct (`disponible` <-> `prete`)
- [ ] Boutons d'action coherents selon statut (pretable/retournable)
- [ ] Pagination visible a partir de 11 livres
- [ ] Protection CSRF validee en execution
- [ ] Echappement XSS (`esc()`) verifie sur toutes les vues

## Finitions recommandees

- [ ] Ajouter des seeds de donnees de test (au moins 15 livres)
- [ ] Ajouter un affichage lisible des erreurs d'upload couverture dans le formulaire
- [ ] Harmoniser les libelles UI (accents, orthographe, style)
- [ ] Nettoyer les messages flash pour une UX plus claire
- [ ] Ajouter une courte section "Installation/Run" dans `README.md`

## Commandes utiles

```powershell
# Exemple avec binaire PHP 8.2 explicite
C:\php82\php.exe spark migrate
C:\php82\php.exe spark serve
```
