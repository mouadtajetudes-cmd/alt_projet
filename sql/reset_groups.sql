-- Script de nettoyage des groupes
-- À exécuter si vous souhaitez supprimer tous les groupes existants

-- Supprimer tous les membres des groupes (cascade devrait le faire automatiquement)
DELETE FROM membre_groupe;

-- Supprimer tous les groupes
DELETE FROM groupes;

-- Réinitialiser la séquence des ID (optionnel)
ALTER SEQUENCE groupes_id_groupe_seq RESTART WITH 1;

-- Vérification
SELECT COUNT(*) as nb_groupes FROM groupes;
SELECT COUNT(*) as nb_membres FROM membre_groupe;
