CREATE TABLE region (
    id INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    nom VARCHAR(255) NOT NULL
);

CREATE TABLE ville (
    id INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    idRegion INT NOT NULL,
    nom VARCHAR(255) NOT NULL,
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
    idType INT NOT NULL,
    idVille INT NOT NULL,
    idProduit INT NOT NULL,
    quantite INT NOT NULL,
    FOREIGN KEY (idType) REFERENCES typeBesoin(id),
    FOREIGN KEY (idVille) REFERENCES ville(id),
    FOREIGN KEY (idProduit) REFERENCES produit(id)
);

CREATE TABLE don (
    id INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    idProduit INT NOT NULL,
    quantite INT NOT NULL,
    dateDon DATE NOT NULL,
    dateSaisie DATE NOT NULL,
    FOREIGN KEY (idProduit) REFERENCES produit(id)
);

CREATE TABLE besoinSatisfait (
    id INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    idBesoin INT NOT NULL,
    dateSatisfaction DATE NOT NULL,
    FOREIGN KEY (idBesoin) REFERENCES besoin(id)
);

CREATE TABLE donDistribue (
    id INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    idDon INT NOT NULL,
    dateDistribution DATE NOT NULL,
    FOREIGN KEY (idDon) REFERENCES don(id)
);
