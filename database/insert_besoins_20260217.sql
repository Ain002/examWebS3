-- Script d'insertion des besoins (ordre d'insertion = colonne Ordre)
-- Fichier généré: insert_besoins_20260217.sql
-- Base attendue: `don` (exécuter USE don; si besoin)

USE don;

START TRANSACTION;

-- 1) Créer les types de besoins si nécessaire
INSERT INTO typeBesoin (description)
SELECT 'nature' FROM DUAL WHERE NOT EXISTS (SELECT 1 FROM typeBesoin WHERE description = 'nature');
INSERT INTO typeBesoin (description)
SELECT 'materiel' FROM DUAL WHERE NOT EXISTS (SELECT 1 FROM typeBesoin WHERE description = 'materiel');
INSERT INTO typeBesoin (description)
SELECT 'argent' FROM DUAL WHERE NOT EXISTS (SELECT 1 FROM typeBesoin WHERE description = 'argent');

-- 2) Créer les régions (on crée une région par ville pour rester explicite)
INSERT INTO region (nom)
SELECT 'Toamasina' FROM DUAL WHERE NOT EXISTS (SELECT 1 FROM region WHERE nom = 'Toamasina');
INSERT INTO region (nom)
SELECT 'Mananjary' FROM DUAL WHERE NOT EXISTS (SELECT 1 FROM region WHERE nom = 'Mananjary');
INSERT INTO region (nom)
SELECT 'Farafangana' FROM DUAL WHERE NOT EXISTS (SELECT 1 FROM region WHERE nom = 'Farafangana');
INSERT INTO region (nom)
SELECT 'Nosy Be' FROM DUAL WHERE NOT EXISTS (SELECT 1 FROM region WHERE nom = 'Nosy Be');
INSERT INTO region (nom)
SELECT 'Morondava' FROM DUAL WHERE NOT EXISTS (SELECT 1 FROM region WHERE nom = 'Morondava');

-- 3) Créer les villes (référence les régions ci-dessus)
INSERT INTO ville (idRegion, nom)
SELECT (SELECT id FROM region WHERE nom = 'Toamasina' LIMIT 1), 'Toamasina' FROM DUAL
WHERE NOT EXISTS (SELECT 1 FROM ville WHERE nom = 'Toamasina');

INSERT INTO ville (idRegion, nom)
SELECT (SELECT id FROM region WHERE nom = 'Mananjary' LIMIT 1), 'Mananjary' FROM DUAL
WHERE NOT EXISTS (SELECT 1 FROM ville WHERE nom = 'Mananjary');

INSERT INTO ville (idRegion, nom)
SELECT (SELECT id FROM region WHERE nom = 'Farafangana' LIMIT 1), 'Farafangana' FROM DUAL
WHERE NOT EXISTS (SELECT 1 FROM ville WHERE nom = 'Farafangana');

INSERT INTO ville (idRegion, nom)
SELECT (SELECT id FROM region WHERE nom = 'Nosy Be' LIMIT 1), 'Nosy Be' FROM DUAL
WHERE NOT EXISTS (SELECT 1 FROM ville WHERE nom = 'Nosy Be');

INSERT INTO ville (idRegion, nom)
SELECT (SELECT id FROM region WHERE nom = 'Morondava' LIMIT 1), 'Morondava' FROM DUAL
WHERE NOT EXISTS (SELECT 1 FROM ville WHERE nom = 'Morondava');

-- 4) Créer les produits uniques (description, pu, unite)
-- Règle heuristique pour `unite`: si le libellé contient '(kg)' -> 'kg', '(L)' -> 'L', sinon 'unit'

INSERT INTO produit (description, pu, unite)
SELECT 'Riz (kg)', 3000, 'kg' FROM DUAL WHERE NOT EXISTS (SELECT 1 FROM produit WHERE description = 'Riz (kg)');
INSERT INTO produit (description, pu, unite)
SELECT 'Eau (L)', 1000, 'L' FROM DUAL WHERE NOT EXISTS (SELECT 1 FROM produit WHERE description = 'Eau (L)');
INSERT INTO produit (description, pu, unite)
SELECT 'Tôle', 25000, 'unit' FROM DUAL WHERE NOT EXISTS (SELECT 1 FROM produit WHERE description = 'Tôle');
INSERT INTO produit (description, pu, unite)
SELECT 'Bâche', 15000, 'unit' FROM DUAL WHERE NOT EXISTS (SELECT 1 FROM produit WHERE description = 'Bâche');
INSERT INTO produit (description, pu, unite)
SELECT 'Argent', 1, 'unit' FROM DUAL WHERE NOT EXISTS (SELECT 1 FROM produit WHERE description = 'Argent');
INSERT INTO produit (description, pu, unite)
SELECT 'Huile (L)', 6000, 'L' FROM DUAL WHERE NOT EXISTS (SELECT 1 FROM produit WHERE description = 'Huile (L)');
INSERT INTO produit (description, pu, unite)
SELECT 'Clous (kg)', 8000, 'kg' FROM DUAL WHERE NOT EXISTS (SELECT 1 FROM produit WHERE description = 'Clous (kg)');
INSERT INTO produit (description, pu, unite)
SELECT 'Bois', 10000, 'unit' FROM DUAL WHERE NOT EXISTS (SELECT 1 FROM produit WHERE description = 'Bois');
INSERT INTO produit (description, pu, unite)
SELECT 'Haricots', 4000, 'unit' FROM DUAL WHERE NOT EXISTS (SELECT 1 FROM produit WHERE description = 'Haricots');
INSERT INTO produit (description, pu, unite)
SELECT 'groupe', 6750000, 'unit' FROM DUAL WHERE NOT EXISTS (SELECT 1 FROM produit WHERE description = 'groupe');

-- 5) Insérer les besoins dans l'ordre indiqué (Ordre)
-- Format: ville, date, Ordre, categorie, libelle, prix_unitaire, quantite

-- Ordre 1
INSERT INTO besoin (idType, idVille, idProduit, quantite)
SELECT (SELECT id FROM typeBesoin WHERE description='materiel' LIMIT 1),
       (SELECT id FROM ville WHERE nom='Toamasina' LIMIT 1),
       (SELECT id FROM produit WHERE description='Bâche' LIMIT 1),
       200
FROM DUAL
WHERE NOT EXISTS (
  SELECT 1 FROM besoin b
  JOIN produit p ON b.idProduit = p.id
  JOIN ville v ON b.idVille = v.id
  WHERE p.description='Bâche' AND v.nom='Toamasina' AND b.quantite=200
);

-- Ordre 2
INSERT INTO besoin (idType, idVille, idProduit, quantite)
SELECT (SELECT id FROM typeBesoin WHERE description='materiel' LIMIT 1),
       (SELECT id FROM ville WHERE nom='Nosy Be' LIMIT 1),
       (SELECT id FROM produit WHERE description='Tôle' LIMIT 1),
       40
FROM DUAL
WHERE NOT EXISTS (
  SELECT 1 FROM besoin b
  JOIN produit p ON b.idProduit = p.id
  JOIN ville v ON b.idVille = v.id
  WHERE p.description='Tôle' AND v.nom='Nosy Be' AND b.quantite=40
);

-- Ordre 3
INSERT INTO besoin (idType, idVille, idProduit, quantite)
SELECT (SELECT id FROM typeBesoin WHERE description='argent' LIMIT 1),
       (SELECT id FROM ville WHERE nom='Mananjary' LIMIT 1),
       (SELECT id FROM produit WHERE description='Argent' LIMIT 1),
       6000000
FROM DUAL
WHERE NOT EXISTS (
  SELECT 1 FROM besoin b
  JOIN produit p ON b.idProduit = p.id
  JOIN ville v ON b.idVille = v.id
  WHERE p.description='Argent' AND v.nom='Mananjary' AND b.quantite=6000000
);

-- Ordre 4
INSERT INTO besoin (idType, idVille, idProduit, quantite)
SELECT (SELECT id FROM typeBesoin WHERE description='nature' LIMIT 1),
       (SELECT id FROM ville WHERE nom='Toamasina' LIMIT 1),
       (SELECT id FROM produit WHERE description='Eau (L)' LIMIT 1),
       1500
FROM DUAL
WHERE NOT EXISTS (
  SELECT 1 FROM besoin b
  JOIN produit p ON b.idProduit = p.id
  JOIN ville v ON b.idVille = v.id
  WHERE p.description='Eau (L)' AND v.nom='Toamasina' AND b.quantite=1500
);

-- Ordre 5
INSERT INTO besoin (idType, idVille, idProduit, quantite)
SELECT (SELECT id FROM typeBesoin WHERE description='nature' LIMIT 1),
       (SELECT id FROM ville WHERE nom='Nosy Be' LIMIT 1),
       (SELECT id FROM produit WHERE description='Riz (kg)' LIMIT 1),
       300
FROM DUAL
WHERE NOT EXISTS (
  SELECT 1 FROM besoin b
  JOIN produit p ON b.idProduit = p.id
  JOIN ville v ON b.idVille = v.id
  WHERE p.description='Riz (kg)' AND v.nom='Nosy Be' AND b.quantite=300
);

-- Ordre 6
INSERT INTO besoin (idType, idVille, idProduit, quantite)
SELECT (SELECT id FROM typeBesoin WHERE description='materiel' LIMIT 1),
       (SELECT id FROM ville WHERE nom='Mananjary' LIMIT 1),
       (SELECT id FROM produit WHERE description='Tôle' LIMIT 1),
       80
FROM DUAL
WHERE NOT EXISTS (
  SELECT 1 FROM besoin b
  JOIN produit p ON b.idProduit = p.id
  JOIN ville v ON b.idVille = v.id
  WHERE p.description='Tôle' AND v.nom='Mananjary' AND b.quantite=80
);

-- Ordre 7
INSERT INTO besoin (idType, idVille, idProduit, quantite)
SELECT (SELECT id FROM typeBesoin WHERE description='argent' LIMIT 1),
       (SELECT id FROM ville WHERE nom='Nosy Be' LIMIT 1),
       (SELECT id FROM produit WHERE description='Argent' LIMIT 1),
       4000000
FROM DUAL
WHERE NOT EXISTS (
  SELECT 1 FROM besoin b
  JOIN produit p ON b.idProduit = p.id
  JOIN ville v ON b.idVille = v.id
  WHERE p.description='Argent' AND v.nom='Nosy Be' AND b.quantite=4000000
);

-- Ordre 8
INSERT INTO besoin (idType, idVille, idProduit, quantite)
SELECT (SELECT id FROM typeBesoin WHERE description='materiel' LIMIT 1),
       (SELECT id FROM ville WHERE nom='Farafangana' LIMIT 1),
       (SELECT id FROM produit WHERE description='Bâche' LIMIT 1),
       150
FROM DUAL
WHERE NOT EXISTS (
  SELECT 1 FROM besoin b
  JOIN produit p ON b.idProduit = p.id
  JOIN ville v ON b.idVille = v.id
  WHERE p.description='Bâche' AND v.nom='Farafangana' AND b.quantite=150
);

-- Ordre 9
INSERT INTO besoin (idType, idVille, idProduit, quantite)
SELECT (SELECT id FROM typeBesoin WHERE description='nature' LIMIT 1),
       (SELECT id FROM ville WHERE nom='Mananjary' LIMIT 1),
       (SELECT id FROM produit WHERE description='Riz (kg)' LIMIT 1),
       500
FROM DUAL
WHERE NOT EXISTS (
  SELECT 1 FROM besoin b
  JOIN produit p ON b.idProduit = p.id
  JOIN ville v ON b.idVille = v.id
  WHERE p.description='Riz (kg)' AND v.nom='Mananjary' AND b.quantite=500
);

-- Ordre 10
INSERT INTO besoin (idType, idVille, idProduit, quantite)
SELECT (SELECT id FROM typeBesoin WHERE description='argent' LIMIT 1),
       (SELECT id FROM ville WHERE nom='Farafangana' LIMIT 1),
       (SELECT id FROM produit WHERE description='Argent' LIMIT 1),
       8000000
FROM DUAL
WHERE NOT EXISTS (
  SELECT 1 FROM besoin b
  JOIN produit p ON b.idProduit = p.id
  JOIN ville v ON b.idVille = v.id
  WHERE p.description='Argent' AND v.nom='Farafangana' AND b.quantite=8000000
);

-- Ordre 11
INSERT INTO besoin (idType, idVille, idProduit, quantite)
SELECT (SELECT id FROM typeBesoin WHERE description='nature' LIMIT 1),
       (SELECT id FROM ville WHERE nom='Morondava' LIMIT 1),
       (SELECT id FROM produit WHERE description='Riz (kg)' LIMIT 1),
       700
FROM DUAL
WHERE NOT EXISTS (
  SELECT 1 FROM besoin b
  JOIN produit p ON b.idProduit = p.id
  JOIN ville v ON b.idVille = v.id
  WHERE p.description='Riz (kg)' AND v.nom='Morondava' AND b.quantite=700
);

-- Ordre 12
INSERT INTO besoin (idType, idVille, idProduit, quantite)
SELECT (SELECT id FROM typeBesoin WHERE description='argent' LIMIT 1),
       (SELECT id FROM ville WHERE nom='Toamasina' LIMIT 1),
       (SELECT id FROM produit WHERE description='Argent' LIMIT 1),
       12000000
FROM DUAL
WHERE NOT EXISTS (
  SELECT 1 FROM besoin b
  JOIN produit p ON b.idProduit = p.id
  JOIN ville v ON b.idVille = v.id
  WHERE p.description='Argent' AND v.nom='Toamasina' AND b.quantite=12000000
);

-- Ordre 13
INSERT INTO besoin (idType, idVille, idProduit, quantite)
SELECT (SELECT id FROM typeBesoin WHERE description='argent' LIMIT 1),
       (SELECT id FROM ville WHERE nom='Morondava' LIMIT 1),
       (SELECT id FROM produit WHERE description='Argent' LIMIT 1),
       10000000
FROM DUAL
WHERE NOT EXISTS (
  SELECT 1 FROM besoin b
  JOIN produit p ON b.idProduit = p.id
  JOIN ville v ON b.idVille = v.id
  WHERE p.description='Argent' AND v.nom='Morondava' AND b.quantite=10000000
);

-- Ordre 14
INSERT INTO besoin (idType, idVille, idProduit, quantite)
SELECT (SELECT id FROM typeBesoin WHERE description='nature' LIMIT 1),
       (SELECT id FROM ville WHERE nom='Farafangana' LIMIT 1),
       (SELECT id FROM produit WHERE description='Eau (L)' LIMIT 1),
       1000
FROM DUAL
WHERE NOT EXISTS (
  SELECT 1 FROM besoin b
  JOIN produit p ON b.idProduit = p.id
  JOIN ville v ON b.idVille = v.id
  WHERE p.description='Eau (L)' AND v.nom='Farafangana' AND b.quantite=1000
);

-- Ordre 15
INSERT INTO besoin (idType, idVille, idProduit, quantite)
SELECT (SELECT id FROM typeBesoin WHERE description='materiel' LIMIT 1),
       (SELECT id FROM ville WHERE nom='Morondava' LIMIT 1),
       (SELECT id FROM produit WHERE description='Bâche' LIMIT 1),
       180
FROM DUAL
WHERE NOT EXISTS (
  SELECT 1 FROM besoin b
  JOIN produit p ON b.idProduit = p.id
  JOIN ville v ON b.idVille = v.id
  WHERE p.description='Bâche' AND v.nom='Morondava' AND b.quantite=180
);

-- Ordre 16
INSERT INTO besoin (idType, idVille, idProduit, quantite)
SELECT (SELECT id FROM typeBesoin WHERE description='materiel' LIMIT 1),
       (SELECT id FROM ville WHERE nom='Toamasina' LIMIT 1),
       (SELECT id FROM produit WHERE description='groupe' LIMIT 1),
       3
FROM DUAL
WHERE NOT EXISTS (
  SELECT 1 FROM besoin b
  JOIN produit p ON b.idProduit = p.id
  JOIN ville v ON b.idVille = v.id
  WHERE p.description='groupe' AND v.nom='Toamasina' AND b.quantite=3
);

-- Ordre 17
INSERT INTO besoin (idType, idVille, idProduit, quantite)
SELECT (SELECT id FROM typeBesoin WHERE description='nature' LIMIT 1),
       (SELECT id FROM ville WHERE nom='Toamasina' LIMIT 1),
       (SELECT id FROM produit WHERE description='Riz (kg)' LIMIT 1),
       800
FROM DUAL
WHERE NOT EXISTS (
  SELECT 1 FROM besoin b
  JOIN produit p ON b.idProduit = p.id
  JOIN ville v ON b.idVille = v.id
  WHERE p.description='Riz (kg)' AND v.nom='Toamasina' AND b.quantite=800
);

-- Ordre 18
INSERT INTO besoin (idType, idVille, idProduit, quantite)
SELECT (SELECT id FROM typeBesoin WHERE description='nature' LIMIT 1),
       (SELECT id FROM ville WHERE nom='Nosy Be' LIMIT 1),
       (SELECT id FROM produit WHERE description='Haricots' LIMIT 1),
       200
FROM DUAL
WHERE NOT EXISTS (
  SELECT 1 FROM besoin b
  JOIN produit p ON b.idProduit = p.id
  JOIN ville v ON b.idVille = v.id
  WHERE p.description='Haricots' AND v.nom='Nosy Be' AND b.quantite=200
);

-- Ordre 19
INSERT INTO besoin (idType, idVille, idProduit, quantite)
SELECT (SELECT id FROM typeBesoin WHERE description='materiel' LIMIT 1),
       (SELECT id FROM ville WHERE nom='Mananjary' LIMIT 1),
       (SELECT id FROM produit WHERE description='Clous (kg)' LIMIT 1),
       60
FROM DUAL
WHERE NOT EXISTS (
  SELECT 1 FROM besoin b
  JOIN produit p ON b.idProduit = p.id
  JOIN ville v ON b.idVille = v.id
  WHERE p.description='Clous (kg)' AND v.nom='Mananjary' AND b.quantite=60
);

-- Ordre 20
INSERT INTO besoin (idType, idVille, idProduit, quantite)
SELECT (SELECT id FROM typeBesoin WHERE description='nature' LIMIT 1),
       (SELECT id FROM ville WHERE nom='Morondava' LIMIT 1),
  (SELECT id FROM produit WHERE description='Eau (L)' LIMIT 1),
       1200
FROM DUAL
WHERE NOT EXISTS (
  SELECT 1 FROM besoin b
  JOIN produit p ON b.idProduit = p.id
  JOIN ville v ON b.idVille = v.id
  WHERE p.description='Eau (L)' AND v.nom='Morondava' AND b.quantite=1200
);

-- Ordre 21
INSERT INTO besoin (idType, idVille, idProduit, quantite)
SELECT (SELECT id FROM typeBesoin WHERE description='nature' LIMIT 1),
       (SELECT id FROM ville WHERE nom='Farafangana' LIMIT 1),
       (SELECT id FROM produit WHERE description='Riz (kg)' LIMIT 1),
       600
FROM DUAL
WHERE NOT EXISTS (
  SELECT 1 FROM besoin b
  JOIN produit p ON b.idProduit = p.id
  JOIN ville v ON b.idVille = v.id
  WHERE p.description='Riz (kg)' AND v.nom='Farafangana' AND b.quantite=600
);

-- Ordre 22
INSERT INTO besoin (idType, idVille, idProduit, quantite)
SELECT (SELECT id FROM typeBesoin WHERE description='materiel' LIMIT 1),
       (SELECT id FROM ville WHERE nom='Morondava' LIMIT 1),
       (SELECT id FROM produit WHERE description='Bois' LIMIT 1),
       150
FROM DUAL
WHERE NOT EXISTS (
  SELECT 1 FROM besoin b
  JOIN produit p ON b.idProduit = p.id
  JOIN ville v ON b.idVille = v.id
  WHERE p.description='Bois' AND v.nom='Morondava' AND b.quantite=150
);

-- Ordre 23
INSERT INTO besoin (idType, idVille, idProduit, quantite)
SELECT (SELECT id FROM typeBesoin WHERE description='materiel' LIMIT 1),
       (SELECT id FROM ville WHERE nom='Toamasina' LIMIT 1),
       (SELECT id FROM produit WHERE description='Tôle' LIMIT 1),
       120
FROM DUAL
WHERE NOT EXISTS (
  SELECT 1 FROM besoin b
  JOIN produit p ON b.idProduit = p.id
  JOIN ville v ON b.idVille = v.id
  WHERE p.description='Tôle' AND v.nom='Toamasina' AND b.quantite=120
);

-- Ordre 24
INSERT INTO besoin (idType, idVille, idProduit, quantite)
SELECT (SELECT id FROM typeBesoin WHERE description='materiel' LIMIT 1),
       (SELECT id FROM ville WHERE nom='Nosy Be' LIMIT 1),
       (SELECT id FROM produit WHERE description='Clous (kg)' LIMIT 1),
       30
FROM DUAL
WHERE NOT EXISTS (
  SELECT 1 FROM besoin b
  JOIN produit p ON b.idProduit = p.id
  JOIN ville v ON b.idVille = v.id
  WHERE p.description='Clous (kg)' AND v.nom='Nosy Be' AND b.quantite=30
);

-- Ordre 25
INSERT INTO besoin (idType, idVille, idProduit, quantite)
SELECT (SELECT id FROM typeBesoin WHERE description='nature' LIMIT 1),
       (SELECT id FROM ville WHERE nom='Mananjary' LIMIT 1),
       (SELECT id FROM produit WHERE description='Huile (L)' LIMIT 1),
       120
FROM DUAL
WHERE NOT EXISTS (
  SELECT 1 FROM besoin b
  JOIN produit p ON b.idProduit = p.id
  JOIN ville v ON b.idVille = v.id
  WHERE p.description='Huile (L)' AND v.nom='Mananjary' AND b.quantite=120
);

-- Ordre 26
INSERT INTO besoin (idType, idVille, idProduit, quantite)
SELECT (SELECT id FROM typeBesoin WHERE description='materiel' LIMIT 1),
       (SELECT id FROM ville WHERE nom='Farafangana' LIMIT 1),
       (SELECT id FROM produit WHERE description='Bois' LIMIT 1),
       100
FROM DUAL
WHERE NOT EXISTS (
  SELECT 1 FROM besoin b
  JOIN produit p ON b.idProduit = p.id
  JOIN ville v ON b.idVille = v.id
  WHERE p.description='Bois' AND v.nom='Farafangana' AND b.quantite=100
);

COMMIT;
