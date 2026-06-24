<?php
session_start();
require_once '../connessione_db.php';

if (!isset($_SESSION['user_id'])) {
    header("Location: ../home/Home.php");
    exit();
}

$user_id = $_SESSION['user_id'];

// Query per eliminare l'utente dal database
$stmt = $conn->prepare("DELETE FROM utenti WHERE id = ?");
$stmt->bind_param("i", $user_id);

if ($stmt->execute()) {
    // Dopo l'eliminazione, distruggiamo la sessione (logout forzato)
    session_destroy();
    header("Location: ../home/Home.php?msg=account_eliminato");
} else {
    echo "Errore durante l'eliminazione dell'account.";
}
$stmt->close();
$conn->close();
?><?php
session_start();
require_once '../connessione_db.php';

if (!isset($_SESSION['user_id'])) {
    header("Location: ../home/Home.php");
    exit();
}

$user_id = $_SESSION['user_id'];

// Query per eliminare l'utente dal database
$stmt = $conn->prepare("DELETE FROM utenti WHERE id = ?");
$stmt->bind_param("i", $user_id);

if ($stmt->execute()) {
    // Dopo l'eliminazione, distruggiamo la sessione (logout forzato)
    session_destroy();
    header("Location: ../home/Home.php?msg=account_eliminato");
} else {
    echo "Errore durante l'eliminazione dell'account.";
}
$stmt->close();
$conn->close();
?>