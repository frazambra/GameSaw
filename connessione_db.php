<?php
// Configurazione (Slide 352 suggerisce di non metterla direttamente nello script ma per ora va bene qui)
$host = "localhost";
$user = "francesco";      // L'utente creato al punto 1
$password = "NAZCANT26";   // La password creata al punto 1
$dbname = "gamesaw";

// Stabilire la connessione (Stile Object Oriented - Slide 352)
$conn = new mysqli($host, $user, $password, $dbname);

// Controllo errore di connessione (Slide 352)
if ($conn->connect_error) {
    die("Errore di connessione: " . $conn->connect_error);
} 
?>