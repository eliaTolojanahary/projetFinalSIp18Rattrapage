-- ============================
-- DROP TABLES
-- ============================

DROP TABLE IF EXISTS transactions;
DROP TABLE IF EXISTS commission;
DROP TABLE IF EXISTS baremes_frais;
DROP TABLE IF EXISTS comptes;
DROP TABLE IF EXISTS types_operations;
DROP TABLE IF EXISTS prefixes;
DROP TABLE IF EXISTS promotion;
-- ============================
-- TABLE : préfixes
-- ============================

CREATE TABLE prefixes (
    id INTEGER PRIMARY KEY AUTOINCREMENT,
    prefixe VARCHAR(3) NOT NULL UNIQUE,
    libelle VARCHAR(50),
    est_operateur_principal INTEGER NOT NULL DEFAULT 0,
    actif INTEGER NOT NULL DEFAULT 1
);

-- ============================
-- TABLE : types d'opérations
-- ============================

CREATE TABLE types_operations (
    id INTEGER PRIMARY KEY AUTOINCREMENT,
    code VARCHAR(20) NOT NULL UNIQUE,
    libelle VARCHAR(50) NOT NULL
);

CREATE TABLE promotion (
    id INTEGER PRIMARY KEY AUTOINCREMENT,
    pourcentage DECIMAL(15,2) NOT NULL
);

-- ============================
-- TABLE : comptes
-- ============================

CREATE TABLE comptes (
    id INTEGER PRIMARY KEY AUTOINCREMENT,
    numero_telephone VARCHAR(15) NOT NULL UNIQUE,
    nom VARCHAR(100),
    prenom VARCHAR(100),
    solde DECIMAL(15,2) NOT NULL DEFAULT 0,
    created_at DATETIME DEFAULT CURRENT_TIMESTAMP
);

-- ============================
-- TABLE : barèmes
-- ============================

CREATE TABLE baremes_frais (
    id INTEGER PRIMARY KEY AUTOINCREMENT,
    type_operation_id INTEGER NOT NULL,
    montant_min DECIMAL(15,2) NOT NULL,
    montant_max DECIMAL(15,2) NOT NULL,
    frais DECIMAL(15,2) NOT NULL,

    FOREIGN KEY(type_operation_id)
        REFERENCES types_operations(id)
);

-- ============================
-- TABLE : commissions
-- ============================

CREATE TABLE commission (
    id INTEGER PRIMARY KEY AUTOINCREMENT,
    id_prefixe INTEGER NOT NULL,
    pourcentage DECIMAL(15,2) NOT NULL,

    FOREIGN KEY(id_prefixe)
        REFERENCES prefixes(id)
);

-- ============================
-- TABLE : transactions
-- ============================

CREATE TABLE transactions (
    id INTEGER PRIMARY KEY AUTOINCREMENT,

    compte_id INTEGER NOT NULL,

    type_operation_id INTEGER NOT NULL,

    montant DECIMAL(15,2) NOT NULL,

    baremes_frais_id INTEGER NOT NULL,

    solde_apres DECIMAL(15,2) NOT NULL,

    date_operation DATETIME DEFAULT CURRENT_TIMESTAMP,

    compte_destination_id INTEGER,

    prefixe_destination_id INTEGER,

    inclure_frais_retrait INTEGER DEFAULT 0,

    commission DECIMAL(15,2),
    promotion DECIMAL(15,2),
    frais_retrait DECIMAL(15,2),
    FOREIGN KEY(compte_id)
        REFERENCES comptes(id),

    FOREIGN KEY(type_operation_id)
        REFERENCES types_operations(id),

    FOREIGN KEY(baremes_frais_id)
        REFERENCES baremes_frais(id),

    FOREIGN KEY(compte_destination_id)
        REFERENCES comptes(id),

    FOREIGN KEY(prefixe_destination_id)
        REFERENCES prefixes(id)
);

-- ============================
-- DONNÉES DE TEST
-- ============================
--prom

INSERT INTO promotion(pourcentage) VALUES
(10);   
-- Préfixes
INSERT INTO prefixes(prefixe, libelle, est_operateur_principal, actif) VALUES
('034','Yas',1,1),
('038','Yas',1,1),
('033','Orange',0,1),
('032','Orange',0,1),
('037','Airtel',0,1),
('031','Airtel',0,1);

-- Types d'opérations
INSERT INTO types_operations(code, libelle) VALUES
('depot','Dépôt'),
('retrait','Retrait'),
('transfert','Transfert');

-- Barèmes dépôt
INSERT INTO baremes_frais(type_operation_id,montant_min,montant_max,frais) VALUES
(1,100,10000,50),
(1,10001,50000,150),
(1,50001,200000,400);

-- Barèmes retrait
INSERT INTO baremes_frais(type_operation_id,montant_min,montant_max,frais) VALUES
(2,100,5000,100),
(2,5001,20000,300),
(2,20001,50000,500);

-- Barèmes transfert
INSERT INTO baremes_frais(type_operation_id,montant_min,montant_max,frais) VALUES
(3,100,5000,150),
(3,5001,20000,400),
(3,20001,100000,750);

-- Commission supplémentaire vers les autres opérateurs
INSERT INTO commission(id_prefixe,pourcentage) VALUES
(3,10),   -- Orange
(4,10),   -- Orange
(5,15),   -- Airtel
(6,15);   -- Airtel

-- Comptes
INSERT INTO comptes(numero_telephone,nom,prenom,solde) VALUES
('034123456','Rakoto','Jean',150000),
('038654321','Rasoa','Marie',120000),
('033111222','Andry','Paul',80000),
('032333444','Rabe','Daniel',60000),
('037555666','Hery','Claire',90000),
('031777888','Soa','Luc',40000);

-- Quelques transactions
INSERT INTO transactions(
compte_id,
type_operation_id,
montant,
baremes_frais_id,
solde_apres,
compte_destination_id,
prefixe_destination_id,
inclure_frais_retrait
)
VALUES
(1,1,50000,2,150000,NULL,NULL,0),

(1,3,10000,8,139600,3,3,0),

(2,2,5000,4,114900,NULL,NULL,0),

(2,3,20000,9,94250,5,5,1);