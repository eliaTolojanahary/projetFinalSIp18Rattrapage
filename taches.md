- *Coté client* :  Elia 003230
    - Page 1: Login
        - Login automatique avec le numéro de téléphone
    - Page 2: Dépôt
        * Formulaire:
            - Montant (nombre) 
            - Designation utilisateur/ compte dépôt (v1 pas de connexion user) datalist / recherche par num  => prefix operateur valable a determiner 
            - Date de dépôt (default now)
            - Frais de dépôt  => en fonction du montant et de transaction 
    - Page 3: Retrait  
        * Formulaire: 
            - Montant (nombre) 
            - Designation utilisateur/ compte retrait (v1 pas de connexion user) datalist / recherche par num  => prefix operateur valable a determiner 
            - Date de retrait (default now)
            - Frais de retrait => en fonction du montant et de transaction
    - Page 4: Transfert  
        * Formulaire: 
            - Montant (nombre) 
            - Designation utilisateur/ compte origine (v1 pas de connexion user) datalist / recherche par num  => prefix operateur valable a determiner 
            - Designation utilisateur/ compte destinataire (v1 pas de connexion user) datalist / recherche par num  => prefix operateur valable a determiner 
            - Date de Transfert (default now)
            - Frais de Transfert => en fonction du montant et de transaction
    - Page 5: Detail client
        * Fiche Client: 
            - Nom et Prenom 
            - Num de tel 
            - Detail Montant dans le compte principal => somme dépôt, somme retrait, somme transfert, Situation compte
        * Bouton voir les historiques
        * Bouton voir le solde =>  Montant dans le compte principal => Situation compte = somme dépôt - somme retrait - somme transfert => dont origine client
    
    - Page 5: Table Historique des transaction
        - Date
        - Type de transaction (dépôt, retrait, transfert)
        - Compte debite => le compte qui recoit l'argent (optionnel en fonction du type de transaction) 
        - Compte credite => le compte qui envoi l'argent (optionnel en fonction du type de transaction)
