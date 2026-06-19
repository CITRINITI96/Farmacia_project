-- ============================================================
-- Esegui questo script UNA SOLA VOLTA in phpMyAdmin o MySQL
-- per aggiungere il supporto alla domanda segreta
-- ============================================================

ALTER TABLE Utente
    ADD COLUMN domanda_segreta VARCHAR(255) NULL AFTER Data_Creazione,
    ADD COLUMN risposta_segreta VARCHAR(255) NULL AFTER domanda_segreta;
