# Systeme operateur : simulation mobile money

composer install
cp env .env
sqlite3 writable/mobile_money.db < base.sql
php spark serve
## Version 1
http://localhost:8080/
- **Coté client**: Elia (etu003230)


    - `Page 1: Login`
# Systeme operateur : simulation mobile money

## Version 1
- **Coté client**
    - `Page 1: Login`
        - Login automatique avec le numéro de téléphone
    - `Page 2: Dépôt`
    - `Page 2: Dépôt`
        * Formulaire:
            - Montant (nombre) 
            - Designation utilisateur/ compte dépôt (v1 pas de connexion user) datalist / recherche par num  => prefix operateur valable a determiner 
            - Date de dépôt (default now)
            - Frais de dépôt  => en fonction du montant et de transaction 
    - `Page 3: Retrait`  
    - `Page 3: Retrait`  
        * Formulaire: 
            - Montant (nombre) 
            - Designation utilisateur/ compte retrait (v1 pas de connexion user) datalist / recherche par num  => prefix operateur valable a determiner 
            - Date de retrait (default now)
            - Frais de retrait => en fonction du montant et de transaction
    - `Page 4: Transfert`  
    - `Page 4: Transfert`  
        * Formulaire: 
            - Montant (nombre) 
            - Designation utilisateur/ compte origine (v1 pas de connexion user) datalist / recherche par num  => prefix operateur valable a determiner 
            - Designation utilisateur/ compte destinataire (v1 pas de connexion user) datalist / recherche par num  => prefix operateur valable a determiner 
            - Date de Transfert (default now)
            - Frais de Transfert => en fonction du montant et de transaction
    - `Page 5: Detail client`
    - `Page 5: Detail client`
        * Fiche Client: 
            - Nom et Prenom 
            - Num de tel 
            - Detail Montant dans le compte principal => somme dépôt, somme retrait, somme transfert, Situation compte
        * Bouton voir les historiques
        * Bouton voir le solde =>  Montant dans le compte principal => Situation compte = somme dépôt - somme retrait - somme transfert => dont origine client
    
    - `Page 6:Table Historique des transaction`
        - Date
        - Type de transaction (dépôt, retrait, transfert)
        - Compte debite => le compte qui recoit l'argent (optionnel en fonction du type de transaction) 
        - Compte credite => le compte qui envoi l'argent (optionnel en fonction du type de transaction)


- **Coté opérateur** : Jemima (etu003370)
    - `Page 1: Signin`
        - Table comptes
        - Numéro de téléphone
        - Nom et Prenom 
        - Info en plus ...
        - **Non implémenté** (prévoir une architecture pour l'ajouter plus tard)
    - `Page 2: Liste client`
        * Route: `/clients`
        * Table: 
            - Num tel
            - Client (prénom + nom)
            - Num Compte (= num tel par défaut)
            - Solde (mis à jour à chaque opération)
            - Action detail → `/clients/:idCompte`
        * Modèle: `CompteOperatorModel`
            - `getSituationCompte()` → liste tous les comptes clients
            - `getSituationCompteParId(int $id)` → détail d'un compte client
            - `countAllClients()` → nombre total de clients
            - `totalMontantDetenu()` → somme des soldes de tous les comptes
            - `updateSolde(int $id, float $solde)` → met à jour le solde d'un compte
    - `Page 3: Détail client`
        * Route: `/clients/:id`
        * Fiche Client:
            - Nom, Prénom, Téléphone, Numéro de compte
            - Solde actuel (source de vérité, mis à jour à chaque transaction)
        * Situation du compte:
            - Total Dépôts, Total Retraits, Total Transferts
            - Le solde est calculé par `comptes.solde` (dénormalisé, mis à jour atomiquement)
        * Modèle: `CompteOperatorModel::getSituationCompteParIdWithTransactions(int $id)`
    - `Page 4: Configuration`
        * Route: `/configuration`
        * Préfixes:
            - Création: préfixe (string) + libellé
            - Toggle actif/inactif
            - Suppression
            - API: `GET /api/prefixes` → préfixes actifs en JSON (pour datalist côté client)
        * Barèmes de frais:
            - Création: type d'opération + montant min + montant max + frais
            - Suppression
            - Lookup: `ConfigurationOperatorModel::getFraisForMontant(int $typeOperationId, float $montant)` → trouve le barème applicable automatiquement
    - `Page 5: Dashboard`
        * Route: `/`
        * Situation des frais perçus (retraits & transferts)
            - `TransactionOperatorModel::totalFrais()`
        * Situation des comptes clients
            - Nombre total de clients
            - Montant total détenu (somme des soldes)
            - Bouton vers `/clients`
    - `Services métier`
        * `TransactionOperatorModel::creerTransaction(array $data)`
            - Vérifie le solde suffisant (retraits/transferts)
            - Crée 2 lignes par transfert (source + destination)
            - Met à jour `comptes.solde` atomiquement (transaction DB)
            - Retourne `false` si solde insuffisant
        * `CompteOperatorModel::updateSolde(int $id, float $solde)`
            - Écrit le nouveau solde dans `comptes.solde`
            - Appelé par `creerTransaction()` après chaque opération
        * Formule solde:
            - `Solde = +dépôts - retraits - transferts sortants + transferts entrants`
            - Stocké dans `comptes.solde` (pas recalculé à la lecture)

- **Coté opérateur**
    - `Page 1: Signin`
        - Numéro de téléphone
        - Nom et Prenom 
        - Info en plus ...
    - `Page 2: Liste client`
        * Table : 
            - Num tel
            - Client Assigne
            - Num Compte (optionnel - default = num tel)
            - Montant dans le compte principal => Situation compte = somme dépôt - somme retrait - somme transfert => dont origine client 
            - Action detail => voir les information du client 
    - `Page 3: Configuration`
        * Form: Configuration des préfixes valable de l’opérateur (ex: 033 et 037)
            - préfixes (string)
            - libellé 
        * Création de types d'opérations avec des barèmes de frais par tranche de montant
            - select type transaction 
            - montant min 
            - montant max
            - frais applique => datalist   
    - `Page 4: Dashboard`
        * Situation gain via les différents frais ( retrait et transfert)
            - Montant somme frais qu'importe la transaction
        * Situation des comptes clients 
            - Bouton vers liste des clients 
