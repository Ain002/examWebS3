-- Script d'insertion des dons
-- Fichier généré: insert_dons_20260217.sql
-- Base attendue: `don` (exécuter USE don; si besoin)

USE don;

START TRANSACTION;

-- Insère les dons en vérifiant l'existence pour éviter les doublons.
-- On utilise dateSaisie = dateDon (peut être ajusté si vous préférez la date actuelle)

-- 1
INSERT INTO don (idProduit, quantite, dateDon, dateSaisie)
SELECT (SELECT id FROM produit WHERE description = 'Argent' LIMIT 1), 5000000, '2026-02-16', '2026-02-16'
FROM DUAL
WHERE NOT EXISTS (
  SELECT 1 FROM don d JOIN produit p ON d.idProduit = p.id
  WHERE p.description = 'Argent' AND d.quantite = 5000000 AND d.dateDon = '2026-02-16'
);

-- 2
INSERT INTO don (idProduit, quantite, dateDon, dateSaisie)
SELECT (SELECT id FROM produit WHERE description = 'Argent' LIMIT 1), 3000000, '2026-02-16', '2026-02-16'
FROM DUAL
WHERE NOT EXISTS (
  SELECT 1 FROM don d JOIN produit p ON d.idProduit = p.id
  WHERE p.description = 'Argent' AND d.quantite = 3000000 AND d.dateDon = '2026-02-16'
);

-- 3
INSERT INTO don (idProduit, quantite, dateDon, dateSaisie)
SELECT (SELECT id FROM produit WHERE description = 'Argent' LIMIT 1), 4000000, '2026-02-17', '2026-02-17'
FROM DUAL
WHERE NOT EXISTS (
  SELECT 1 FROM don d JOIN produit p ON d.idProduit = p.id
  WHERE p.description = 'Argent' AND d.quantite = 4000000 AND d.dateDon = '2026-02-17'
);

-- 4
INSERT INTO don (idProduit, quantite, dateDon, dateSaisie)
SELECT (SELECT id FROM produit WHERE description = 'Argent' LIMIT 1), 1500000, '2026-02-17', '2026-02-17'
FROM DUAL
WHERE NOT EXISTS (
  SELECT 1 FROM don d JOIN produit p ON d.idProduit = p.id
  WHERE p.description = 'Argent' AND d.quantite = 1500000 AND d.dateDon = '2026-02-17'
);

-- 5
INSERT INTO don (idProduit, quantite, dateDon, dateSaisie)
SELECT (SELECT id FROM produit WHERE description = 'Argent' LIMIT 1), 6000000, '2026-02-17', '2026-02-17'
FROM DUAL
WHERE NOT EXISTS (
  SELECT 1 FROM don d JOIN produit p ON d.idProduit = p.id
  WHERE p.description = 'Argent' AND d.quantite = 6000000 AND d.dateDon = '2026-02-17'
);

-- 6
INSERT INTO don (idProduit, quantite, dateDon, dateSaisie)
SELECT (SELECT id FROM produit WHERE description = 'Riz (kg)' LIMIT 1), 400, '2026-02-16', '2026-02-16'
FROM DUAL
WHERE NOT EXISTS (
  SELECT 1 FROM don d JOIN produit p ON d.idProduit = p.id
  WHERE p.description = 'Riz (kg)' AND d.quantite = 400 AND d.dateDon = '2026-02-16'
);

-- 7
INSERT INTO don (idProduit, quantite, dateDon, dateSaisie)
SELECT (SELECT id FROM produit WHERE description = 'Eau (L)' LIMIT 1), 600, '2026-02-16', '2026-02-16'
FROM DUAL
WHERE NOT EXISTS (
  SELECT 1 FROM don d JOIN produit p ON d.idProduit = p.id
  WHERE p.description = 'Eau (L)' AND d.quantite = 600 AND d.dateDon = '2026-02-16'
);

-- 8
INSERT INTO don (idProduit, quantite, dateDon, dateSaisie)
SELECT (SELECT id FROM produit WHERE description = 'Tôle' LIMIT 1), 50, '2026-02-17', '2026-02-17'
FROM DUAL
WHERE NOT EXISTS (
  SELECT 1 FROM don d JOIN produit p ON d.idProduit = p.id
  WHERE p.description = 'Tôle' AND d.quantite = 50 AND d.dateDon = '2026-02-17'
);

-- 9
INSERT INTO don (idProduit, quantite, dateDon, dateSaisie)
SELECT (SELECT id FROM produit WHERE description = 'Bâche' LIMIT 1), 70, '2026-02-17', '2026-02-17'
FROM DUAL
WHERE NOT EXISTS (
  SELECT 1 FROM don d JOIN produit p ON d.idProduit = p.id
  WHERE p.description = 'Bâche' AND d.quantite = 70 AND d.dateDon = '2026-02-17'
);

-- 10
INSERT INTO don (idProduit, quantite, dateDon, dateSaisie)
SELECT (SELECT id FROM produit WHERE description = 'Haricots' LIMIT 1), 100, '2026-02-17', '2026-02-17'
FROM DUAL
WHERE NOT EXISTS (
  SELECT 1 FROM don d JOIN produit p ON d.idProduit = p.id
  WHERE p.description = 'Haricots' AND d.quantite = 100 AND d.dateDon = '2026-02-17'
);

-- 11
INSERT INTO don (idProduit, quantite, dateDon, dateSaisie)
SELECT (SELECT id FROM produit WHERE description = 'Riz (kg)' LIMIT 1), 2000, '2026-02-18', '2026-02-18'
FROM DUAL
WHERE NOT EXISTS (
  SELECT 1 FROM don d JOIN produit p ON d.idProduit = p.id
  WHERE p.description = 'Riz (kg)' AND d.quantite = 2000 AND d.dateDon = '2026-02-18'
);

-- 12
INSERT INTO don (idProduit, quantite, dateDon, dateSaisie)
SELECT (SELECT id FROM produit WHERE description = 'Tôle' LIMIT 1), 300, '2026-02-18', '2026-02-18'
FROM DUAL
WHERE NOT EXISTS (
  SELECT 1 FROM don d JOIN produit p ON d.idProduit = p.id
  WHERE p.description = 'Tôle' AND d.quantite = 300 AND d.dateDon = '2026-02-18'
);

-- 13
INSERT INTO don (idProduit, quantite, dateDon, dateSaisie)
SELECT (SELECT id FROM produit WHERE description = 'Eau (L)' LIMIT 1), 5000, '2026-02-18', '2026-02-18'
FROM DUAL
WHERE NOT EXISTS (
  SELECT 1 FROM don d JOIN produit p ON d.idProduit = p.id
  WHERE p.description = 'Eau (L)' AND d.quantite = 5000 AND d.dateDon = '2026-02-18'
);

-- 14
INSERT INTO don (idProduit, quantite, dateDon, dateSaisie)
SELECT (SELECT id FROM produit WHERE description = 'Argent' LIMIT 1), 20000000, '2026-02-19', '2026-02-19'
FROM DUAL
WHERE NOT EXISTS (
  SELECT 1 FROM don d JOIN produit p ON d.idProduit = p.id
  WHERE p.description = 'Argent' AND d.quantite = 20000000 AND d.dateDon = '2026-02-19'
);

-- 15
INSERT INTO don (idProduit, quantite, dateDon, dateSaisie)
SELECT (SELECT id FROM produit WHERE description = 'Bâche' LIMIT 1), 500, '2026-02-19', '2026-02-19'
FROM DUAL
WHERE NOT EXISTS (
  SELECT 1 FROM don d JOIN produit p ON d.idProduit = p.id
  WHERE p.description = 'Bâche' AND d.quantite = 500 AND d.dateDon = '2026-02-19'
);

-- 16
INSERT INTO don (idProduit, quantite, dateDon, dateSaisie)
SELECT (SELECT id FROM produit WHERE description = 'Haricots' LIMIT 1), 88, '2026-02-17', '2026-02-17'
FROM DUAL
WHERE NOT EXISTS (
  SELECT 1 FROM don d JOIN produit p ON d.idProduit = p.id
  WHERE p.description = 'Haricots' AND d.quantite = 88 AND d.dateDon = '2026-02-17'
);

COMMIT;
