create DATABASE don;

use don;
CREATE TABLE region (
    id INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    nom VARCHAR(255) NOT NULL
);

CREATE TABLE ville (
    id INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    idRegion INT UNSIGNED NOT NULL,
    nom VARCHAR(255) NOT NULL,
    CONSTRAINT fk_ville_region
        FOREIGN KEY (idRegion) REFERENCES region(id)
);

CREATE TABLE typeBesoin (
    id INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    description VARCHAR(255) NOT NULL
);

CREATE TABLE produit (
    id INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    description VARCHAR(255) NOT NULL,
    pu DOUBLE NOT NULL
    
);

CREATE TABLE besoin (
    id INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    idType INT UNSIGNED NOT NULL,
    idVille INT UNSIGNED NOT NULL,
    idProduit INT UNSIGNED NOT NULL,
    quantite INT NOT NULL,
    CONSTRAINT fk_besoin_type
        FOREIGN KEY (idType) REFERENCES typeBesoin(id),
    CONSTRAINT fk_besoin_ville
        FOREIGN KEY (idVille) REFERENCES ville(id),
    CONSTRAINT fk_besoin_produit
        FOREIGN KEY (idProduit) REFERENCES produit(id)
);

CREATE TABLE don (
    id INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    idProduit INT UNSIGNED NOT NULL,
    quantite INT NOT NULL,
    dateDon DATE NOT NULL,
    dateSaisie DATE NOT NULL,
    CONSTRAINT fk_don_produit
        FOREIGN KEY (idProduit) REFERENCES produit(id)
);

CREATE TABLE besoinSatisfait (
    id INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    idBesoin INT UNSIGNED NOT NULL,
    dateSatisfaction DATE NOT NULL,
    CONSTRAINT fk_bs_besoin
        FOREIGN KEY (idBesoin) REFERENCES besoin(id)
);

CREATE TABLE donDistribue (
    id INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    idDon INT UNSIGNED NOT NULL,
    dateDistribution DATE NOT NULL,
    CONSTRAINT fk_dd_don
        FOREIGN KEY (idDon) REFERENCES don(id)
);

CREATE TABLE attribution (
    id INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    idBesoin INT UNSIGNED NOT NULL,
    idDon INT UNSIGNED NOT NULL,
    quantite INT NOT NULL,
    CONSTRAINT fk_attribution_besoin
        FOREIGN KEY (idBesoin) REFERENCES besoin(id),
    CONSTRAINT fk_attribution_don
        FOREIGN KEY (idDon) REFERENCES don(id)
);

ALTER TABLE produit
ADD unite VARCHAR(20) NOT NULL;

CREATE TABLE achat (
    id INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    idBesoin INT NOT NULL,
    montant DOUBLE NOT NULL,
    FOREIGN KEY (idBesoin) REFERENCES besoin(id)
);


CREATE TABLE configFraisAchat (
    id INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    pourcentage DECIMAL(5,2) NOT NULL,
    dateCreation DATE NOT NULL
);
