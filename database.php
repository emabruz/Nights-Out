<?php 
	include 'conn_db.php';

	if (!$connessione->query("CREATE TABLE IF NOT EXISTS utente (
	  id_utente int(4) NOT NULL AUTO_INCREMENT,
	  nome varchar(30) NOT NULL,
	  cognome varchar(40) NOT NULL,
	  email varchar(40),
	  password varchar(200),
	  PRIMARY KEY (id_utente)
	)")) {
	  echo "Errore della query: ";
	}else{
	  echo "Tabella creata correttamente.";
	}
	// chiusura della connessione
	//$connessione->close();


	if (!$connessione->query("CREATE TABLE IF NOT EXISTS locale (
	  id_locale int(4) NOT NULL AUTO_INCREMENT,
	  id_utente int(4) NOT NULL,
	  nomeLocale varchar(40) NOT NULL,
	  descrizione_loc MEDIUMTEXT,
	  telefono varchar(30),
	  indirizzo varchar(100) NOT NULL,
	  sitoWeb varchar(50),
	  pIva varchar(30) NOT NULL,
	  latitudine varchar(50),
	  longitudine varchar(50),


	  PRIMARY KEY (id_locale),
	  FOREIGN KEY (id_utente) REFERENCES utente (id_utente)
	)")) {
	  echo "Errore della query: " . $connessione->error . ".";
	}else{
	  echo "Tabella 2 creata correttamente.";
	}
	// chiusura della connessione
	//$connessione->close();

	if (!$connessione->query("CREATE TABLE IF NOT EXISTS evento (
	  id_evento int(4) NOT NULL AUTO_INCREMENT,
	  id_locale int(4) NOT NULL,
	  titoloEvento varchar(100) NOT NULL,
	  inizio varchar(30) NOT NULL,
	  fine varchar(30) NOT NULL,
	  ora_inizio int(4) NOT NULL,
	  ora_fine int(4) NOT NULL,
	  minuti_inizio int(4) NOT NULL DEFAULT 0,
	  minuti_fine int(4) NOT NULL DEFAULT 0,
	  descrizione_ev MEDIUMTEXT,
	  latitudine varchar(50),
	  longitudine varchar(50),


	  PRIMARY KEY (id_evento),
	  FOREIGN KEY (id_locale) REFERENCES locale (id_locale)
	)")) {
	  echo "Errore della query: " . $connessione->error . ".";
	}else{
	  echo "Tabella 3 creata correttamente.";
	}
	// chiusura della connessione
	//$connessione->close();


	if (!$connessione->query("CREATE TABLE IF NOT EXISTS foto_locale (
	  id_foto_locale int(4) NOT NULL AUTO_INCREMENT,
	  id_locale int(4) NOT NULL,
	  id_utente int(4) NOT NULL,
	  data_inserimento DATETIME,
	  nome_galleria varchar(30) NOT NULL,
	  nome_immagine varchar(40) NOT NULL,
	  estensione varchar(40) NOT NULL,

	  PRIMARY KEY (id_foto_locale),
	  FOREIGN KEY (id_locale) REFERENCES locale (id_locale),
	  FOREIGN KEY (id_utente) REFERENCES utente (id_utente)
	)")) {
	  echo "Errore della query: " . $connessione->error . ".";
	}else{
	  echo "Tabella 4 creata correttamente.";
	}
	// chiusura della connessione
	//$connessione->close();

	if (!$connessione->query("CREATE TABLE IF NOT EXISTS foto_evento (
	  id_foto_evento int(4) NOT NULL AUTO_INCREMENT,
	  id_evento int(4) NOT NULL,
	  id_locale int(4) NOT NULL, 
	  id_utente int(4) NOT NULL,
	  data_inserimento DATETIME,
	  nome_galleria varchar(30) NOT NULL,
	  nome_immagine varchar(40) NOT NULL,
	  estensione varchar(40) NOT NULL,

	  PRIMARY KEY (id_foto_evento),
	  FOREIGN KEY (id_evento) REFERENCES evento (id_evento),
	  FOREIGN KEY (id_utente) REFERENCES utente (id_utente)
	)")) {
	  echo "Errore della query: " . $connessione->error . ".";
	}else{
	  echo "Tabella 5 creata correttamente.";
	}
	// chiusura della connessione
	//$connessione->close();


	if (!$connessione->query("CREATE TABLE IF NOT EXISTS notifica (
	  id_notifica int(4) NOT NULL AUTO_INCREMENT,
	  id_utente int(4) NOT NULL,
	  testo_notifica MEDIUMTEXT,
	  letta tinyint(1),
	  inviata tinyint(1),
	  nome_evento varchar(200),
	  data_fine varchar(30),

	  PRIMARY KEY (id_notifica),
	  FOREIGN KEY (id_utente) REFERENCES utente (id_utente)
	)")) {
	  echo "Errore della query: " . $connessione->error . ".";
	}else{
	  echo "Tabella 6 creata correttamente.";
	}
	// chiusura della connessione
	$connessione->close();

?>
