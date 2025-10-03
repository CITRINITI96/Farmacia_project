CREATE DATABASE FarmaciaOspedaliera;

USE FarmaciaOspedaliera;

-- Creazione della tabella Farmaco
CREATE TABLE Farmaco (
    ID_Farmaco INT AUTO_INCREMENT PRIMARY KEY,
    Nome VARCHAR(100) NOT NULL,
    Categoria VARCHAR(50),
    Pericolosità BOOLEAN DEFAULT FALSE,
    Prezzo_Unitario DECIMAL(10, 2) NOT NULL
);

-- Creazione della tabella Magazzino
CREATE TABLE Magazzino (
    ID_Magazzino INT AUTO_INCREMENT PRIMARY KEY,
    Nome_Magazzino VARCHAR(100) NOT NULL,
    Ubicazione VARCHAR(200)
);

-- Creazione della tabella Reparto
CREATE TABLE Reparto (
    ID_Reparto INT AUTO_INCREMENT PRIMARY KEY,
    Nome_Reparto VARCHAR(100) NOT NULL,
    Responsabile VARCHAR(100)
);

-- Creazione della tabella Stock
CREATE TABLE Stock (
    ID_Stock INT AUTO_INCREMENT PRIMARY KEY,
    ID_Farmaco INT NOT NULL,
    ID_Magazzino INT NOT NULL,
    Quantità INT NOT NULL,
    Data_Scadenza DATE,
    FOREIGN KEY (ID_Farmaco) REFERENCES Farmaco(ID_Farmaco),
    FOREIGN KEY (ID_Magazzino) REFERENCES Magazzino(ID_Magazzino)
);

-- Creazione della tabella Ordine_Fornitore
CREATE TABLE Ordine_Fornitore (
    ID_Ordine INT AUTO_INCREMENT PRIMARY KEY,
    ID_Farmaco INT NOT NULL,
    Quantità_Ordinata INT NOT NULL,
    Data_Ordine DATE NOT NULL,
    Data_Consegna DATE,
    Stato ENUM('In_Elaborazione', 'Evadibile', 'Annullato') NOT NULL,
    FOREIGN KEY (ID_Farmaco) REFERENCES Farmaco(ID_Farmaco)
);

-- Creazione della tabella Reparto
CREATE TABLE Reparto (
    ID_Reparto INT AUTO_INCREMENT PRIMARY KEY,
    Nome_Reparto VARCHAR(100) NOT NULL UNIQUE,
    Responsabile VARCHAR(100)
);

-- Creazione della tabella Ordine_Reparto
CREATE TABLE ordine_reparto (
    ID_Ordine INT AUTO_INCREMENT PRIMARY KEY,
    ID_Reparto INT NOT NULL,
    ID_Farmaco INT NOT NULL,
    Quantità_Richiesta INT NOT NULL,
    Data_Richiesta DATE NOT NULL,
    Stato ENUM('Approvato', 'Evadibile', 'Rifiutato') NOT NULL,
    FOREIGN KEY (ID_Reparto) REFERENCES Reparto(ID_Reparto),
    FOREIGN KEY (ID_Farmaco) REFERENCES Farmaco(ID_Farmaco)
);

-- Tabella Notifiche
CREATE TABLE Notifiche (
    ID_Notifica INT AUTO_INCREMENT PRIMARY KEY,
    Tipo_Notifica ENUM('Scorte_Basse', 'Scadenza_Imminente') NOT NULL,
    ID_Stock INT NOT NULL,
    Data_Notifica TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    Messaggio TEXT,
    FOREIGN KEY (ID_Stock) REFERENCES Stock(ID_Stock)
);
-- Creazione della tabella Utente
CREATE TABLE Utente (
    ID_Utente INT AUTO_INCREMENT PRIMARY KEY, -- Identificativo unico dell'utente
    Nome VARCHAR(100) NOT NULL,              -- Nome dell'utente
    Cognome VARCHAR(100) NOT NULL,           -- Cognome dell'utente
    Email VARCHAR(150) UNIQUE NOT NULL,      -- Email dell'utente (deve essere unica)
    Password VARCHAR(255) NOT NULL,          -- Password crittografata
    Ruolo ENUM('Dottore', 'Dottoressa', 'Magazziniere', 'Farmacista') NOT NULL, -- Ruolo dell'utente
    Data_Creazione TIMESTAMP DEFAULT CURRENT_TIMESTAMP -- Data di creazione dell'account
);
CREATE TABLE Pazienti (
    ID_Paziente INT AUTO_INCREMENT PRIMARY KEY,
    Nome VARCHAR(100) NOT NULL,
    Cognome VARCHAR(100) NOT NULL,
    Data_Nascita DATE NOT NULL,
    Sesso ENUM('M', 'F') NOT NULL,
    ID_Reparto INT,
    FOREIGN KEY (ID_Reparto) REFERENCES Reparto(ID_Reparto)
);
CREATE TABLE Prescrizione (
    ID_Paziente INT,
    ID_Farmaco INT,
    Data_Prescrizione DATE NOT NULL,
    Quantità INT NOT NULL,
    PRIMARY KEY (ID_Paziente, ID_Farmaco),
    FOREIGN KEY (ID_Paziente) REFERENCES Pazienti(ID_Paziente),
    FOREIGN KEY (ID_Farmaco) REFERENCES Farmaco(ID_Farmaco)
);

-- Inserimento farmaci
INSERT INTO Farmaco (Nome, Categoria, Pericolosità, Prezzo_Unitario)
VALUES 
('Paracetamolo', 'Analgesico', FALSE, 1.20),
('Morfina', 'Antidolorifico', TRUE, 25.50),
('Amoxicillina', 'Antibiotico', FALSE, 2.75);

-- Inserimento magazzini
INSERT INTO Magazzino (Nome_Magazzino, Ubicazione)
VALUES 
('Magazzino Centrale', 'Edificio A - Piano Terra'),
('Magazzino Sterili', 'Edificio B - Primo Piano');

-- Inserimento stock
INSERT INTO Stock (ID_Farmaco, ID_Magazzino, Quantità, Data_Scadenza)
VALUES 
(1, 1, 500, '2025-12-31'),
(2, 2, 100, '2024-06-30'),
(3, 1, 300, '2025-03-15');

-- Inserimento ordini fornitori
INSERT INTO Ordine_Fornitore (ID_Farmaco, Quantità_Ordinata, Data_Ordine, Data_Consegna, Stato)
VALUES 
(1, 1000, '2024-11-15', '2024-11-20', 'Evadibile'),
(3, 500, '2024-11-10', NULL, 'In_Elaborazione');

-- Inserimento reparti
INSERT INTO Reparto (Nome_Reparto, Responsabile)
VALUES 
('Oncologia', 'Dr. Rossi'),
('Pediatria', 'Dr.ssa Bianchi'),
('Cardiologia', 'Dr. Citriniti'),


-- Inserimento ordini reparti
INSERT INTO Ordine_Reparto (ID_Reparto, ID_Farmaco, Quantità_Richiesta, Data_Richiesta, Stato)
VALUES 
(1, 2, 10, '2024-11-25', 'Approvato'),
(2, 1, 50, '2024-11-26', 'Evadibile');


INSERT INTO Utente (username, password, ruolo, Nome, Cognome, Email) 
VALUES  
('dottore1', 'password123', 'Dottore', 'Mario', 'Rossi', 'mario1.rossi@example.com'), 
('dottoressa1', 'password456', 'Dottoressa', 'Laura', 'Bianchi', 'laura.bianchi@example.com'), 
('magazziniere1', 'password789', 'Magazziniere', 'Giovanni', 'Verdi', 'giovanni.verdi@example.com'), 
('farmacista1Genera_Notifiche_Scorte_BasseID_Farmaco', 'password101', 'Farmacista', 'Marco', 'Neri', 'marco.neri@example.com');

INSERT INTO Pazienti (Nome, Cognome, Data_Nascita, Sesso, ID_Reparto)
VALUES ('Giovanni', 'Rossi', '1980-05-12', 'M', 1);

INSERT INTO Prescrizione (ID_Paziente, ID_Farmaco, Data_Prescrizione, Quantità)
VALUES (1, 2, '2024-11-30', 2);  -- Esempio: il paziente 1 riceve 2 unità del farmaco 2.









