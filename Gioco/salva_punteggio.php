<?php
session_start();

// Imposta l'header per rispondere in formato JSON
header('Content-Type: application/json');

// Gestione Errori PHP: Mostra errori nel file di log del server, non a video (romperebbe il JSON)
ini_set('display_errors', 0);
error_reporting(E_ALL);

// 1. Collegamento al DB
// Se questo file è nella cartella 'game', dobbiamo tornare indietro di uno per trovare connessione_db.php
$path_connessione = '../connessione_db.php';

if (!file_exists($path_connessione)) {
    echo json_encode(['success' => false, 'message' => 'Errore critico: File connessione non trovato.']);
    exit();
}
require_once $path_connessione;

// 2. Controllo Login
if (!isset($_SESSION['logged_in']) || $_SESSION['logged_in'] !== true) {
    echo json_encode(['success' => false, 'message' => 'Utente non loggato. Effettua il login.']);
    exit();
}

// 3. Ricezione Dati
$input = file_get_contents('php://input');
$data = json_decode($input, true);

if (!isset($data['punteggio'])) {
    echo json_encode(['success' => false, 'message' => 'Nessun punteggio ricevuto.']);
    exit();
}

$nuovo_punteggio = intval($data['punteggio']);
$user_id = $_SESSION['user_id'];

// 4. Logica Salvataggio
// Cerchiamo il punteggio attuale
$stmt = $conn->prepare("SELECT punteggio FROM utenti WHERE id = ?");
$stmt->bind_param("i", $user_id);
$stmt->execute();
$result = $stmt->get_result();
$row = $result->fetch_assoc();

if ($row) {
    $vecchio_punteggio = intval($row['punteggio']);

    // DEBUG: Se vuoi salvare SEMPRE per testare, rimuovi l'IF qui sotto.
    // Altrimenti, salva solo se è un record.
    if ($nuovo_punteggio > $vecchio_punteggio) {
        
        $update = $conn->prepare("UPDATE utenti SET punteggio = ? WHERE id = ?");
        $update->bind_param("ii", $nuovo_punteggio, $user_id);
        
        if ($update->execute()) {
            echo json_encode([
                'success' => true, 
                'message' => "Nuovo Record! (Punti: $nuovo_punteggio)"
            ]);
        } else {
            echo json_encode(['success' => false, 'message' => 'Errore SQL durante aggiornamento.']);
        }
    } else {
        // Il punteggio non è un record
        echo json_encode([
            'success' => true, // È true perché la chiamata ha funzionato, anche se non abbiamo aggiornato
            'message' => "Punteggio $nuovo_punteggio non supera il record attuale ($vecchio_punteggio)."
        ]);
    }
} else {
    echo json_encode(['success' => false, 'message' => 'Utente non trovato nel DB.']);
}

$conn->close();
?>