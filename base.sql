CREATE DATABASE mobile_money;
USE mobile_money;
CREATE TABLE prefixes (
    id INTEGER PRIMARY KEY AUTOINCREMENT,
    prefixe VARCHAR(3) NOT NULL UNIQUE,     -- ex: '033', '037'
    actif INTEGER NOT NULL DEFAULT 1        -- 1 = actif, 0 = désactivé
);

INSERT INTO prefixes (prefixe) VALUES ('033');
INSERT INTO prefixes (prefixe) VALUES ('037');


-- ============================
-- TABLE : types d'opérations
-- ============================
CREATE TABLE types_operations (
    id INTEGER PRIMARY KEY AUTOINCREMENT,
    code VARCHAR(20) NOT NULL UNIQUE,       -- 'depot', 'retrait', 'transfert'
    libelle VARCHAR(50) NOT NULL            -- 'Dépôt', 'Retrait', 'Transfert'
);

INSERT INTO types_operations (code, libelle) VALUES ('depot', 'Dépôt');
INSERT INTO types_operations (code, libelle) VALUES ('retrait', 'Retrait');
INSERT INTO types_operations (code, libelle) VALUES ('transfert', 'Transfert');


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

-- Exemple de barème pour un retrait (à adapter selon l'exemple du sujet)
INSERT INTO baremes_frais (type_operation_id, montant_min, montant_max, frais)
VALUES (2, 100, 5000, 100);

INSERT INTO baremes_frais (type_operation_id, montant_min, montant_max, frais)
VALUES (2, 5001, 20000, 300);

INSERT INTO baremes_frais (type_operation_id, montant_min, montant_max, frais)
VALUES (2, 20001, 50000, 500);


-- ============================
-- TABLE : comptes clients
-- ============================
CREATE TABLE comptes (
    id INTEGER PRIMARY KEY AUTOINCREMENT,
    numero_telephone VARCHAR(15) NOT NULL UNIQUE,   -- sert de login
    solde DECIMAL(15,2) NOT NULL DEFAULT 0,
    created_at DATETIME DEFAULT CURRENT_TIMESTAMP
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
    solde_apres DECIMAL(15,2) NOT NULL,              -- solde du compte après l'opération
    date_operation DATETIME DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (compte_id) REFERENCES comptes(id),
    FOREIGN KEY (type_operation_id) REFERENCES types_operations(id),
    FOREIGN KEY (compte_destination_id) REFERENCES comptes(id)
);