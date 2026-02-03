<?php
session_start();
header('Content-Type: application/json');
ini_set('display_errors', 0);
error_reporting(E_ALL);

$path_connessione = '../connessione_db.php';
if (!file_exists($path_connessione)) {
    echo json_encode(['success' => false, 'message' => 'Errore db connection']);
    exit();
}
require_once $path_connessione;

if (!isset($_SESSION['logged_in']) || $_SESSION['logged_in'] !== true) {
    echo json_encode(['success' => false, 'message' => 'Non loggato']);
    exit();
}

$input = file_get_contents('php://input');
$data = json_decode($input, true);

if (!isset($data['punteggio']) || !isset($data['difficolta'])) {
    echo json_encode(['success' => false, 'message' => 'Dati mancanti']);
    exit();
}

$nuovo_punteggio = intval($data['punteggio']);
$difficolta = $data['difficolta']; 
$user_id = $_SESSION['user_id'];

// Mappa la difficoltà alla colonna corretta del DB
$colonna_db = '';
switch ($difficolta) {
    case 'facile':
        $colonna_db = 'punteggio_facile';
        break;
    case 'difficile':
        $colonna_db = 'punteggio_difficile';
        break;
    case 'normale': // MODIFICA QUI: Ora intercetta 'normale'
    default:
        $colonna_db = 'punteggio'; // Mappa alla colonna standard
        break;
}

$sql = "SELECT $colonna_db FROM utenti WHERE id = ?";
$stmt = $conn->prepare($sql);
$stmt->bind_param("i", $user_id);
$stmt->execute();
$result = $stmt->get_result();
$row = $result->fetch_assoc();

if ($row) {
    $vecchio_punteggio = ($row[$colonna_db] === NULL) ? 0 : intval($row[$colonna_db]);

    if ($nuovo_punteggio > $vecchio_punteggio) {
        $sql_update = "UPDATE utenti SET $colonna_db = ? WHERE id = ?";
        $update = $conn->prepare($sql_update);
        $update->bind_param("ii", $nuovo_punteggio, $user_id);
        
        if ($update->execute()) {
            echo json_encode(['success' => true, 'message' => "Record $difficolta aggiornato!"]);
        } else {
            echo json_encode(['success' => false, 'message' => 'Errore SQL']);
        }
    } else {
        echo json_encode(['success' => true, 'message' => "Punteggio non supera record $difficolta."]);
    }
} else {
    echo json_encode(['success' => false, 'message' => 'Utente non trovato']);
}
$conn->close();
?>