Database Farmacia Ospedaliera

Questo progetto contiene la struttura di un database relazionale per la gestione informatizzata di una farmacia ospedaliera. 
Include il file .sql generato con MySQL Workbench e istruzioni per eseguirlo in locale con XAMPP.


Requisiti

XAMPP
MySQL Workbench 
PHP 

Installazione e Avvio

Apri il pannello di controllo XAMPP
Avvia i moduli Apache e MSQL
Impostare le porte 80,443 per il modulo Apache
Impostare la porta 80 per il modulo MySQL
Aprire il Manage Server Connections e creare una Local Instance MySQL80 impostando i seguenti paramenti:
Hostname:localhost
Port:3306
Username:root
Password:root12345
Nella cartella farmacia_project è presente un file FarmaciaOspedaliera.sql cliccare su quello e avviare mySql e collegare il database alla connessione
creata in precedenza
Il database è stato creato e testato con MySQL Workbench utilizzando l’istanza locale `Local instance MySQL80`.


Interfaccia PHP

Copiare la cartella nominata farmacia_project nella cartella htdocs di XAMPP 
Accedi via browser a: http://localhost/farmacia_project/login.php
