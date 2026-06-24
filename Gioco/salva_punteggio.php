<?php
session_start();
header('Content-Type: application/json');
require_once '../connessione_db.php';

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
$nuove_stelle = isset($data['stelle']) ? intval($data['stelle']) : 0;
$difficolta = $data['difficolta'];
$user_id = $_SESSION['user_id'];

// 1. MAPPING COLONNE (Basato sul tuo screenshot del DB)
$colonna_db = 'punteggio'; // Default Normale
if ($difficolta === 'facile') $colonna_db = 'punteggio_facile';
if ($difficolta === 'difficile') $colonna_db = 'punteggio_difficile';

// 2. RECUPERA DATI ATTUALI
$sql = "SELECT $colonna_db, stelle_totali FROM utenti WHERE id = ?";
$stmt = $conn->prepare($sql);
$stmt->bind_param("i", $user_id);
$stmt->execute();
$res = $stmt->get_result();
$user_data = $res->fetch_assoc();

if (!$user_data) { echo json_encode(['success' => false]); exit(); }

// 3. CALCOLA NUOVI VALORI
$vecchio_punteggio = intval($user_data[$colonna_db]);
// Se stelle_totali è NULL (per i vecchi utenti), consideralo 0
$totale_stelle_old = ($user_data['stelle_totali'] === NULL) ? 0 : intval($user_data['stelle_totali']);
$totale_stelle_new = $totale_stelle_old + $nuove_stelle;

// 4. AGGIORNAMENTO
// Aggiorniamo SEMPRE le stelle. Aggiorniamo il punteggio SOLO se è record.
$sql_update = "UPDATE utenti SET stelle_totali = ?";
$params_type = "i";
$params_values = [$totale_stelle_new];

if ($nuovo_punteggio > $vecchio_punteggio) {
    $sql_update .= ", $colonna_db = ?";
    $params_type .= "i";
    $params_values[] = $nuovo_punteggio;
}

$sql_update .= " WHERE id = ?";
$params_type .= "i";
$params_values[] = $user_id;

$stmt_up = $conn->prepare($sql_update);
$stmt_up->bind_param($params_type, ...$params_values);
$stmt_up->execute();

// --- 5. LOGICA ASSEGNAZIONE BADGE ---
$badges_new = [];

function checkAndAwardBadge($conn, $u_id, $b_id, &$list) {
    // Controlla se l'utente ha già questo badge
    $check = $conn->query("SELECT * FROM user_badges WHERE user_id=$u_id AND badge_id=$b_id");
    if ($check->num_rows == 0) {
        $conn->query("INSERT INTO user_badges (user_id, badge_id) VALUES ($u_id, $b_id)");
        // Recupera nome per l'alert
        $resB = $conn->query("SELECT nome FROM badges WHERE id=$b_id");
        if($r = $resB->fetch_assoc()) $list[] = $r['nome'];
    }
}

// Badge 1: Facile > 55
if ($colonna_db == 'punteggio_facile' && $nuovo_punteggio >= 55) checkAndAwardBadge($conn, $user_id, 1, $badges_new);

// Badge 2: Normale > 50 (User ID 3 "Francesco" lo otterrà alla prossima partita valida!)
if ($colonna_db == 'punteggio' && $nuovo_punteggio >= 50) checkAndAwardBadge($conn, $user_id, 2, $badges_new);

// Badge 3: Difficile > 45
if ($colonna_db == 'punteggio_difficile' && $nuovo_punteggio >=30) checkAndAwardBadge($conn, $user_id, 3, $badges_new);

// Badge 4: Stelle Totali >= 15
if ($totale_stelle_new >= 12) checkAndAwardBadge($conn, $user_id, 4, $badges_new);

echo json_encode([
    'success' => true, 
    'badges_unlocked' => $badges_new
]);

$conn->close();
?>