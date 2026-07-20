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
 CREATE TABLE commission (
    id INTEGER PRIMARY KEY AUTOINCREMENT,
    id_prefixe INTEGER NOT NULL,
    pourcentage DECIMAL(15,2) NOT NULL
 )
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

    -- si transfert
    compte_destination_id INTEGER,

    -- opérateur du destinataire
    prefixe_destination_id INTEGER,

    -- option "inclure frais de retrait"
    inclure_frais_retrait INTEGER DEFAULT 0,

    FOREIGN KEY(compte_id)
        REFERENCES comptes(id),

    FOREIGN KEY(type_operation_id)
        REFERENCES types_operations(id),

    FOREIGN KEY(baremes_frais_id)
        REFERENCES baremes_frais(id),

    FOREIGN KEY(compte_destination_id)
        REFERENCES comptes(id),

    FOREIGN KEY(operateur_destination_id)
        REFERENCES prefixes(id)
);

