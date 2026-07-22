## Installation
## Installation

composer install
cp env .env
sqlite3 writable/mobile_money.db < base.sql
php spark serve

## Accès côté client


## Accès côté client

http://localhost:8080/

## Accès côté operateur

http://localhost:8080/operator


## Version 1

- **Côté client** : Elia (etu003230)

    - `Page 1 : Login` — route `GET /`, `POST /login` => `Client::form`, `Client::login`
        - Connexion automatique avec le numéro de téléphone
        - Vérification du préfixe opérateur avant connexion (`ClientPrefixeModel::estValide`)

    - `Page 2 : Dépôt` — route `GET/POST /depot` => `ClientOperation::depotForm`, `ClientOperation::depotStore`
        - Formulaire :
            - Montant (nombre)
            - Compte concerné : celui du numéro connecté (session)
            - Date de dépôt (par défaut : maintenant)
            - Frais de dépôt calculés automatiquement selon le montant  (`ClientBaremeModel::calculerFrais`)

    - `Page 3 : Retrait` — route `GET/POST /retrait` => `ClientOperation::retraitForm`, `ClientOperation::retraitStore`
        - Formulaire :
            - Montant (nombre)
            - Compte concerné : celui du numéro connecté (session)
            - Date de retrait (par défaut : maintenant)
            - Frais de retrait calculés automatiquement selon le montant
            - Vérification du solde suffisant (montant + frais)

    - `Page 4 : Transfert` — route `GET/POST /transfert` => `ClientOperation::transfertForm`, `ClientOperation::transfertStore`
        - Formulaire :
            - Montant total à transferer (nombre)
            - Compte origine : celui du numéro connecté (session)
            - Un ou plusieurs comptes destinataires, recherche par numéro
            - Date de transfert (par défaut : maintenant)
            - Frais de transfert calculés automatiquement selon le montant réparti par destinataire

    - `Page 5 : Détail client / detail` — route `GET /detail` → `ClientOperation::detail`
        - Fiche client :
            - Numéro de téléphone
            - Solde actuel du compte
        - Lien vers l'historique des transactions
        - Lien vers les opérations (dépôt, retrait, transfert)

    - `Page 6 : Historique des transactions` — route `GET /historique` → `ClientOperation::historique`
        - Date de l'opération

## Version 1

- **Côté client** : Elia (etu003230)

    - `Page 1 : Login` — route `GET /`, `POST /login` => `Client::form`, `Client::login`
        - Connexion automatique avec le numéro de téléphone
        - Vérification du préfixe opérateur avant connexion (`ClientPrefixeModel::estValide`)

    - `Page 2 : Dépôt` — route `GET/POST /depot` => `ClientOperation::depotForm`, `ClientOperation::depotStore`
        - Formulaire :
            - Montant (nombre)
            - Compte concerné : celui du numéro connecté (session)
            - Date de dépôt (par défaut : maintenant)
            - Frais de dépôt calculés automatiquement selon le montant  (`ClientBaremeModel::calculerFrais`)

    - `Page 3 : Retrait` — route `GET/POST /retrait` => `ClientOperation::retraitForm`, `ClientOperation::retraitStore`
        - Formulaire :
            - Montant (nombre)
            - Compte concerné : celui du numéro connecté (session)
            - Date de retrait (par défaut : maintenant)
            - Frais de retrait calculés automatiquement selon le montant
            - Vérification du solde suffisant (montant + frais)

    - `Page 4 : Transfert` — route `GET/POST /transfert` => `ClientOperation::transfertForm`, `ClientOperation::transfertStore`
        - Formulaire :
            - Montant total à transferer (nombre)
            - Compte origine : celui du numéro connecté (session)
            - Un ou plusieurs comptes destinataires, recherche par numéro
            - Date de transfert (par défaut : maintenant)
            - Frais de transfert calculés automatiquement selon le montant réparti par destinataire

    - `Page 5 : Détail client / detail` — route `GET /detail` → `ClientOperation::detail`
        - Fiche client :
            - Numéro de téléphone
            - Solde actuel du compte
        - Lien vers l'historique des transactions
        - Lien vers les opérations (dépôt, retrait, transfert)

    - `Page 6 : Historique des transactions` — route `GET /historique` → `ClientOperation::historique`
        - Date de l'opération
        - Type de transaction (dépôt, retrait, transfert)
        - Compte débité (celui qui reçoit l'argent, optionnel selon le type)
        - Compte crédité (celui qui envoie l'argent, optionnel selon le type)

    - `Déconnexion` — route `GET /logout` → `Client::logout`
        - Compte débité (celui qui reçoit l'argent, optionnel selon le type)
        - Compte crédité (celui qui envoie l'argent, optionnel selon le type)

    - `Déconnexion` — route `GET /logout` → `Client::logout`


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


## Version 2
- **Coté client**: Elia (etu003230)
    -modif de la Configuration % en plus de commissions pour les transferts vers les autres opérateurs par rapport au montant envoye
        + Table commission
            - id 
            - idprefixe  
            - % 
    - `Page 4: Transfert`  
        + Bouton Option inclure frais de retrait lors de l’envoi 
            * recuperer les frais de retrait correspondant au bareme du montant a transferer 
            * frais de transfert = bareme   montant a transferer (apres inclusion)
        + Envoi multiple vers plusieurs numéros ( divisé le montant pour chaque numéro)
            * modif plusieur num destinataire au lieu de 1
    - `Page 3: Retrait`  
        + pas de frais de retrait pour les autres opérateurs si operateur different operateur du num pas de frais de retrais

- **Coté opérateur** : Jemima (etu003370) 
    - Table prefixe:
        + boolean est operateur principal
    - Table transactions:
        + commission  
    - `Page 5: Dashboard`
            - `TransactionOperatorModel::totalFrais()` 
                * Retourne uniquement le montant total des frais perçus par l'opérateur principal.
                * Agrège les frais des opérations de retrait et de transfert appartenant à l'opérateur principal.
                * Utilisé pour afficher les gains de l'opérateur principal sur le Dashboard. 
            - `TransactionOperatorModel::totalFraisAutre()` => modifier pour avoir seulement les frais pour operateur les autres
                * Retourne uniquement le montant total des frais perçus par les autres opérateurs.
                * Agrège les frais des opérations de retrait et de transfert réalisées par les opérateurs secondaires.
                * Utilisé pour distinguer les gains des autres opérateurs de ceux de l'opérateur principal.
            - Situation des montants à envoyer à chaque opérateur
                => Affiche le montant total des frais ou des sommes dues à chaque opérateur.
                - `TransactionOperatorModel::montantsParOperateur()`
                    * Regroupe les transactions par opérateur.
                    * Calcule le total des montants ou frais à reverser pour chaque opérateur.
                    * Retourne une liste contenant :
                        - id_operateur
                        - nom_operateur
                        - montant_total
                        - nombre_transactions (optionnel)
    - `Page 4: Configuration`
        * Route: `/configuration`
        * Préfixes:
            - Création/Edit: + radio ou toggle  est_operateur_principal 
            - Toggle actif/inactif
            - Suppression
            - API: `GET /api/prefixes` → préfixes actifs en JSON (pour datalist côté client)

## Fix error main : 
- **Coté client**: Jemima (etu003370)
    - `Page 5: Detail client` - inexistant a faire
        * Fiche Client: 
            - Nom et Prenom 
            - Num de tel 
            - Detail Montant dans le compte principal => somme dépôt, somme retrait, somme transfert, Situation compte
        * Bouton voir les historiques
        * Bouton voir le solde =>  Montant dans le compte principal => Situation compte = somme dépôt - somme retrait - somme transfert => dont origine client
    - route /detail 

- **Coté opérateur** : Jemima (etu003370) 
    - `Page 6: login unique operateur`
        - login admin/admin (ecrit en dur sur le form)
        + crsf_field()
        - redirect in dashboardOperator url: /operator form 

## Fix liste clients :
- **Coté opérateur** : Jemima (etu003370) 
    - `Page 2: Liste client`
            * Route: `/clients`
            * Modèle: `ClientPrefixeModel` 
                -  `estPrincipal(num)` → determine si un numero est de l'eperateur principal 
            * Modèle: `CompteOperatorModel` 
                - `updateSolde(int $id, float $solde)` → supprimmer existant Coté client
                - `getSituationCompte()` → liste tous les comptes clients dont le numero correspond a l'operateur principal

Alea 1 misy prom sur frais de transfert:meme operateur ( rehefa manao transfert zany dia mihena 10 % ny bareme de frais de transfert )
base : config % prom ()
bonus : page manova config prom

Alea 2 : epargne 
Client miteny epargne % 
ex : epargne 50% :  50 pourcent vers solde principal / 50 pourcent vers solde epargne 
1 client - 1 epargne 
Sur les transfert uniquement    

## fix épargne
- **Côté client**: Jemima (etu003370) 

    - `Page 7 : Épargne` — route `GET/POST /epargne` => `ClientOperation::epargneForm`, `ClientOperation::updateEpargne`
        * Formulaire :
            - Pourcentage épargne (0 à 100 %)
            - Valeur pré-remplie avec le pourcentage actuel du compte
        * Affichage :
            - Compte (numéro de téléphone)
            - Solde principal actuel
            - Solde épargne actuel
            - Pourcentage épargne actuel
        * Règle métier :
            - 1 client = 1 compte épargne (table `epargnes`, contrainte UNIQUE sur `id_compte`)
            - Le pourcentage est stocké dans `comptes.pourcentage_epargne`
            - Validation : nombre entre 0 et 100
        * Modèle : `ClientModel` (`comptes.pourcentage_epargne`)

    - `Page 4 : Transfert` — `ClientOperation::transfertStore`
        * Logique épargne lors du transfert :
            - Appliqué uniquement aux opérations de transfert
            - `(100 - pourcentage)%` du montant reçu crédité sur le **compte principal** du destinataire
            - `pourcentage%` du montant reçu crédité sur le **compte épargne** du destinataire
        * Exemple : transfert 100 000 Ar, épargne 20% → 80 000 Ar sur principal / 20 000 Ar sur épargne
        * Montant épargné enregistré dans `transactions.epargnes`
        * Le compte émetteur est débité du montant total (montant + frais + commission) comme avant

    - `Page 5 : Détail client` — route `GET /detail` => `ClientOperation::detail`
        * Affichage ajouté :
            - Solde épargne actuel
            - Pourcentage épargne
        * Section "Compte Épargne" dans la fiche client
        * Section "Montant dans le compte épargne" dans le bloc "Voir le solde"
        * Données passées depuis `ClientEpargneModel::creerSiInexistant()`

    - `Page 6 : Historique des transactions` — `historiquePourCompte()`
        * Affichage de la colonne épargne conditionné :
            - Si l'utilisateur connecté est le destinataire de la transaction → affiche `transactions.epargnes`
            - Si l'utilisateur connecté est l'émetteur → affiche `0`
        * Condition : `$t['compte_destinataire'] === $compte['numero_telephone']`
        * Le numéro du compte connecté est passé à la vue depuis le contrôleur

    - `Modèle` — `ClientEpargneModel` (nouveau, table `epargnes`)
        * `parCompte(int $compteId)` → retourne la ligne épargne d'un compte
        * `creerSiInexistant(int $compteId)` → crée la ligne épargne si elle n'existe pas
        * `crediter(int $compteId, float $montant)` → crédite le solde épargne

    - `Modèle` — `ClientModel::trouverOuCreerCompte()`
        * Modification : lors de la création d'un nouveau compte, crée automatiquement la ligne correspondante dans `epargnes` via `ClientEpargneModel::creerSiInexistant()`

    - `Base de données` :
        * Table `epargnes` (déjà existante) :
            - `id` INT PK AUTO
            - `id_compte` INT UNIQUE FK → comptes(id)
            - `solde` DECIMAL(15,2) DEFAULT 0
            - `created_at` DATETIME
        * Table `comptes` :
            - `pourcentage_epargne` DECIMAL(15,2) DEFAULT 0 (ajouté par migration)
        * Table `transactions` :
            - `epargnes` DECIMAL(15,2) NULL (ajouté par migration)
        * Aucune migration supplémentaire nécessaire