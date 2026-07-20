# Plan : Cohérence Solde = Situation Compte

## Objectif
Assurer que `comptes.solde` = situation calculée depuis les transactions. Le solde est mis à jour atomiquement à chaque transaction.

## Règles métier
- **Solde** = +dépôts - retraits - transferts sortants + transferts entrants
- **2 lignes** dans `transactions` par transfert (sortante + entrante)
- **Solde ne peut pas devenir négatif** — bloquer retraits/transferts si solde insuffisant

## Formule
```
Solde = SUM(montant WHERE type=1 AND compte_id=X)          -- dépôts
      - SUM(montant WHERE type=2 AND compte_id=X)          -- retraits
      - SUM(montant WHERE type=3 AND compte_id=X)          -- transferts sortants
      + SUM(montant WHERE type=3 AND compte_destination_id=X) -- transferts entrants
```

---

## Fichiers affectés (5 fichiers)

### 1. `app/Models/CompteOperatorModel.php`
- **Ajouter** `updateSolde(int $id, float $solde): void` — met à jour `comptes.solde`
- **Supprimer** `solde_calcule` de `getSituationCompteParIdWithTransactions()` — le `solde` du compte est déjà correct
- **Conserver** la situation détaillée (dépôts/retraits/transferts) pour l'affichage

### 2. `app/Models/TransactionOperatorModel.php`
- **Ajouter** `creerTransaction(array $data): bool`
- Paramètres : `compte_id`, `type_operation_id`, `montant`, `baremes_frais_id`, `compte_destination_id`
- Logique (transaction DB atomique) :
  1. Lire solde actuel compte source
  2. Vérifier solde suffisant (retrait/transfert sortant) → false si insuffisant
  3. Calculer nouveau solde source (dépôt: +montant, retrait/transfert: -montant)
  4. Insérer 1ère transaction avec `solde_apres`
  5. `CompteOperatorModel::updateSolde(compte_id, nouveau_solde)`
  6. Si type=3 et compte_destination_id existe :
     - Lire solde dest, calculer nouveau solde dest (+montant)
     - Insérer 2ème transaction avec `solde_apres`
     - `CompteOperatorModel::updateSolde(dest_id, nouveau_solde_dest)`
  7. Commit

### 3. `app/Views/operator/client_detail.php`
- **Supprimer** la ligne "Solde calculé" (doublon avec "Solde actuel")
- Le "Solde actuel" affiché en haut est déjà le solde correct

### 4. `app/Database/Seeds/DataSeeder.php`
- Recalculer les `solde` des comptes via une requête SQL après l'insertion des transactions
- Les transactions seedées doivent être cohérentes avec la règle "pas de solde négatif"
- Ajuster les données seedées si nécessaire

### 5. `base.sql`
- Corriger les `solde` des comptes pour correspondre au calcul depuis les transactions
- Les données seedées dans `base.sql` doivent respecter la contrainte de solde >= 0

---

## Données seed corrigées

Ajustement nécessaire pour respecter la contrainte solde >= 0 :
- Les comptes 2 (Rasoa) et 5 (Rabe) ont des retraits supérieurs à leurs dépôts
- Ajustement : augmenter les dépôts initiaux ou réduire les retraits

### Seed transactions révisées :
| Compte | Type | Montant | Description |
|--------|------|---------|-------------|
| 1 (Rakoto) | Dépôt | 15 000 | Dépôt initial |
| 1 (Rakoto) | Transfert sortant → 3 | 3 000 | Envoi à Andry |
| 2 (Rasoa) | Dépôt | 10 000 | Dépôt initial |
| 2 (Rasoa) | Retrait | 3 000 | Retrait |
| 3 (Andry) | Dépôt | 20 000 | Dépôt initial |
| 3 (Andry) | Transfert sortant → 4 | 5 000 | Envoi à Hery |
| 4 (Hery) | Dépôt | 20 000 | Dépôt initial |
| 4 (Hery) | Dépôt | 10 000 | Dépôt |
| 5 (Rabe) | Dépôt | 5 000 | Dépôt initial |
| 5 (Rabe) | Retrait | 2 000 | Retrait |

### Soldes résultants :
| Client | Dépôts | Retraits | Trans Out | Trans In | Solde |
|--------|--------|----------|-----------|----------|-------|
| Rakoto | 15 000 | 0 | 3 000 | 0 | **12 000** |
| Rasoa | 10 000 | 3 000 | 0 | 0 | **7 000** |
| Andry | 20 000 | 0 | 5 000 | 3 000 | **18 000** |
| Hery | 30 000 | 0 | 0 | 5 000 | **35 000** |
| Rabe | 5 000 | 2 000 | 0 | 0 | **3 000** |

---

## Vérification
1. `php spark migrate -n 0` — recréer la DB
2. `php spark db:seed DataSeeder` — peupler avec données cohérentes
3. Vérifier `comptes.solde` = calcul depuis transactions
4. Syntax check : `php -l` sur tous les fichiers modifiés
