
CREATE TABLE prefixes (
    id INTEGER PRIMARY KEY AUTOINCREMENT,
    prefixe VARCHAR(3) NOT NULL UNIQUE,     -- ex: '033', '037'
    libelle VARCHAR(50),                     -- ex: 'Orange', 'Airtel'
    actif INTEGER NOT NULL DEFAULT 1         -- 1 = actif, 0 = désactivé
);

-- ============================
-- TABLE : types d'opérations
-- ============================
CREATE TABLE types_operations (
    id INTEGER PRIMARY KEY AUTOINCREMENT,
    code VARCHAR(20) NOT NULL UNIQUE,       -- 'depot', 'retrait', 'transfert'
    libelle VARCHAR(50) NOT NULL            -- 'Dépôt', 'Retrait', 'Transfert'
);

-- ============================
-- TABLE : comptes clients
-- ============================
CREATE TABLE comptes (
    id INTEGER PRIMARY KEY AUTOINCREMENT,
    numero_telephone VARCHAR(15) NOT NULL UNIQUE,   -- sert de login
    nom VARCHAR(100),
    prenom VARCHAR(100),
    solde DECIMAL(15,2) NOT NULL DEFAULT 0,
    created_at DATETIME DEFAULT CURRENT_TIMESTAMP
);

-- ============================
-- TABLE : barèmes de frais par tranche
-- ============================
CREATE TABLE baremes_frais (
    id INTEGER PRIMARY KEY AUTOINCREMENT,
    type_operation_id INTEGER NOT NULL,
    montant_min DECIMAL(15,2) NOT NULL,     -- borne basse de la tranche
    montant_max DECIMAL(15,2) NOT NULL,     -- borne haute de la tranche
    frais DECIMAL(15,2) NOT NULL,           -- frais fixe pour cette tranche
    FOREIGN KEY (type_operation_id) REFERENCES types_operations(id)
);

-- ============================
-- TABLE : historique des opérations
-- ============================
CREATE TABLE transactions (
    id INTEGER PRIMARY KEY AUTOINCREMENT,
    compte_id INTEGER NOT NULL,                     -- compte qui fait l'opération
    type_operation_id INTEGER NOT NULL,
    montant DECIMAL(15,2) NOT NULL,
    baremes_frais_id INTEGER NOT NULL,
    compte_destination_id INTEGER,                  -- rempli seulement si transfert
    solde_apres DECIMAL(15,2) NOT NULL,             -- solde du compte après l'opération
    date_operation DATETIME DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (compte_id) REFERENCES comptes(id),
    FOREIGN KEY (type_operation_id) REFERENCES types_operations(id),
    FOREIGN KEY (baremes_frais_id) REFERENCES baremes_frais(id),
    FOREIGN KEY (compte_destination_id) REFERENCES comptes(id)
);

-- ============================
-- Données de démonstration
-- ============================

-- Préfixes
INSERT INTO prefixes (prefixe, libelle, actif) VALUES ('033', 'Orange', 1);
INSERT INTO prefixes (prefixe, libelle, actif) VALUES ('037', 'Airtel', 1);

-- Types d'opérations
INSERT INTO types_operations (code, libelle) VALUES ('depot', 'Dépôt');
INSERT INTO types_operations (code, libelle) VALUES ('retrait', 'Retrait');
INSERT INTO types_operations (code, libelle) VALUES ('transfert', 'Transfert');

-- Barèmes de frais
-- type_operation_id: 1=depot, 2=retrait, 3=transfert
INSERT INTO baremes_frais (type_operation_id, montant_min, montant_max, frais) VALUES (1, 100, 10000, 50);
INSERT INTO baremes_frais (type_operation_id, montant_min, montant_max, frais) VALUES (1, 10001, 50000, 150);
INSERT INTO baremes_frais (type_operation_id, montant_min, montant_max, frais) VALUES (1, 50001, 200000, 400);
INSERT INTO baremes_frais (type_operation_id, montant_min, montant_max, frais) VALUES (2, 100, 5000, 100);
INSERT INTO baremes_frais (type_operation_id, montant_min, montant_max, frais) VALUES (2, 5001, 20000, 300);
INSERT INTO baremes_frais (type_operation_id, montant_min, montant_max, frais) VALUES (2, 20001, 50000, 500);
INSERT INTO baremes_frais (type_operation_id, montant_min, montant_max, frais) VALUES (3, 100, 5000, 150);
INSERT INTO baremes_frais (type_operation_id, montant_min, montant_max, frais) VALUES (3, 5001, 20000, 400);
INSERT INTO baremes_frais (type_operation_id, montant_min, montant_max, frais) VALUES (3, 20001, 100000, 750);

-- Comptes clients (solde initialisé à 0, mis à jour après les transactions)
INSERT INTO comptes (numero_telephone, nom, prenom, solde) VALUES ('033123456', 'Rakoto', 'Jean', 0);
INSERT INTO comptes (numero_telephone, nom, prenom, solde) VALUES ('037456789', 'Rasoa', 'Marie', 0);
INSERT INTO comptes (numero_telephone, nom, prenom, solde) VALUES ('033987654', 'Andry', 'Paul', 0);
INSERT INTO comptes (numero_telephone, nom, prenom, solde) VALUES ('037111222', 'Hery', 'Claire', 0);
INSERT INTO comptes (numero_telephone, nom, prenom, solde) VALUES ('033333444', 'Rabe', 'Daniel', 0);

-- Transactions (2 lignes par transfert, solde_apres cohérent)
-- baremes_frais_id: 1=depot(100-10000), 2=depot(10001-50000), 3=depot(50001-200000)
--                   4=retrait(100-5000), 5=retrait(5001-20000), 6=retrait(20001-50000)
--                   7=transfert(100-5000), 8=transfert(5001-20000), 9=transfert(20001-100000)

-- Rakoto: depot 15000
INSERT INTO transactions (compte_id, type_operation_id, montant, baremes_frais_id, compte_destination_id, solde_apres, date_operation) VALUES (1, 1, 15000, 2, NULL, 15000, '2026-06-01 10:00:00');
-- Rasoa: depot 10000
INSERT INTO transactions (compte_id, type_operation_id, montant, baremes_frais_id, compte_destination_id, solde_apres, date_operation) VALUES (2, 1, 10000, 1, NULL, 10000, '2026-06-02 09:00:00');
-- Rasoa: retrait 3000
INSERT INTO transactions (compte_id, type_operation_id, montant, baremes_frais_id, compte_destination_id, solde_apres, date_operation) VALUES (2, 2, 3000, 4, NULL, 7000, '2026-06-03 09:00:00');
-- Andry: depot 20000
INSERT INTO transactions (compte_id, type_operation_id, montant, baremes_frais_id, compte_destination_id, solde_apres, date_operation) VALUES (3, 1, 20000, 2, NULL, 20000, '2026-06-04 10:00:00');
-- Andry → Hery: transfert 5000 (source)
INSERT INTO transactions (compte_id, type_operation_id, montant, baremes_frais_id, compte_destination_id, solde_apres, date_operation) VALUES (3, 3, 5000, 7, 4, 15000, '2026-06-05 14:30:00');
-- Hery ← Andry: transfert 5000 (destination)
INSERT INTO transactions (compte_id, type_operation_id, montant, baremes_frais_id, compte_destination_id, solde_apres, date_operation) VALUES (4, 3, 5000, 7, 3, 5000, '2026-06-05 14:30:00');
-- Hery: depot 20000
INSERT INTO transactions (compte_id, type_operation_id, montant, baremes_frais_id, compte_destination_id, solde_apres, date_operation) VALUES (4, 1, 20000, 2, NULL, 25000, '2026-06-06 11:00:00');
-- Rabe: depot 5000
INSERT INTO transactions (compte_id, type_operation_id, montant, baremes_frais_id, compte_destination_id, solde_apres, date_operation) VALUES (5, 1, 5000, 1, NULL, 5000, '2026-06-07 08:00:00');
-- Rabe: retrait 2000
INSERT INTO transactions (compte_id, type_operation_id, montant, baremes_frais_id, compte_destination_id, solde_apres, date_operation) VALUES (5, 2, 2000, 4, NULL, 3000, '2026-06-08 08:00:00');
-- Rakoto → Andry: transfert 3000 (source)
INSERT INTO transactions (compte_id, type_operation_id, montant, baremes_frais_id, compte_destination_id, solde_apres, date_operation) VALUES (1, 3, 3000, 7, 3, 12000, '2026-06-09 16:00:00');
-- Andry ← Rakoto: transfert 3000 (destination)
INSERT INTO transactions (compte_id, type_operation_id, montant, baremes_frais_id, compte_destination_id, solde_apres, date_operation) VALUES (3, 3, 3000, 7, 1, 18000, '2026-06-09 16:00:00');

-- Mise à jour des soldes des comptes (solde = solde_apres de la dernière transaction)
UPDATE comptes SET solde = (SELECT t.solde_apres FROM transactions t WHERE t.compte_id = comptes.id ORDER BY t.id DESC LIMIT 1);
