-- Script de données de test simple

USE don;

-- Insertion des régions
INSERT INTO region (nom) VALUES
('Analamanga'),
('Vakinankaratra'),
('Itasy');

-- Insertion des villes
INSERT INTO ville (idRegion, nom) VALUES
(1, 'Antananarivo'),
(1, 'Ambohimangakely'),
(2, 'Antsirabe'),
(3, 'Miarinarivo');

-- Insertion des types de besoin
INSERT INTO typeBesoin (description) VALUES
('nature'),
('materiaux'),
('argent');

--Insertion des produits (avec unite)
INSERT INTO produit (description, pu, unite) VALUES
('Riz', 3500.00, 'kg'),
('Eau', 1200.00, 'litre'),
('Medicaments', 25000.00, 'boite');

-- INSERT INTO produit (description, pu, unite, idType) VALUES
-- ('Riz', 3500.00, 'kg', 1),        
-- ('Eau', 1200.00, 'litre', 1),     
-- ('Medicaments', 25000.00, 'boite', 1); 


-- Insertion des besoins
INSERT INTO besoin (idType, idVille, idProduit, quantite) VALUES
(1, 1, 1, 100),
(2, 2, 2, 50),
(3, 3, 3, 20),
(1, 1, 1, 75);  -- Nouveau besoin de Riz à Antananarivo

-- Insertion des dons
INSERT INTO don (idProduit, quantite, dateDon, dateSaisie) VALUES
(1, 150, '2026-02-10', '2026-02-10'),
(2, 80, '2026-02-12', '2026-02-12'),
(3, 30, '2026-02-14', '2026-02-14');

-- Insertion des attributions
INSERT INTO attribution (idBesoin, idDon, quantite) VALUES
(1, 1, 100),
(2, 2, 50),
(3, 3, 20);

-- Insertion des besoins satisfaits
INSERT INTO besoinSatisfait (idBesoin, dateSatisfaction) VALUES
(1, '2026-02-11'),
(2, '2026-02-13');

-- Insertion des dons distribués
INSERT INTO donDistribue (idDon, dateDistribution) VALUES
(1, '2026-02-11'),
(2, '2026-02-13');

INSERT INTO produit (description, pu, unite) VALUES
('Argent', 1, 'Ar'); 

-- INSERT INTO produit (description, pu, unite, idType) VALUES
-- ('Argent', 1, 'Ar', 3); 


INSERT INTO besoin (idType, idVille, idProduit, quantite) VALUES
(1, 1, 1, 75);

INSERT INTO besoin (idType, idVille, idProduit, quantite) VALUES
(1, 1, 1, 25);
