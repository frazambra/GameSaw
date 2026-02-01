<?php
session_start();
require_once '../connessione_db.php';

// Se non è loggato, torna alla home
if (!isset($_SESSION['logged_in'])) {
    header("Location: ../home/Home.php");
    exit();
}

$user_id = $_SESSION['user_id'];
$status_msg = "";

// Gestione del salvataggio dati
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $citta = $_POST['citta'];
    $bio = $_POST['bio'];
    $social = $_POST['social'];
    $livello = $_POST['livello'];

    $stmt = $conn->prepare("UPDATE utenti SET citta=?, bio=?, social_link=?, livello=? WHERE id=?");
    $stmt->bind_param("ssssi", $citta, $bio, $social, $livello, $user_id);
    
    if ($stmt->execute()) {
        header("Location: visualizza_profilo.php");
        exit();
    } else {
        $status_msg = "Errore durante l'aggiornamento.";
    }
    $stmt->close();
}

// Recupero dati
$stmt = $conn->prepare("SELECT * FROM utenti WHERE id = ?");
$stmt->bind_param("i", $user_id);
$stmt->execute();
$result = $stmt->get_result();
$user = $result->fetch_assoc();
?>

<!DOCTYPE html>
<html lang="it">
<head>
    <meta charset="UTF-8">
    <title>Modifica Profilo</title>
    <link rel="stylesheet" href="../style.css">
    <link rel="stylesheet" href="style_utente.css">
</head>
<body>
    
    <header style="width: 100%;">
        <div class="logo">AimTrainer</div>
        <nav><ul class="nav-links"><li><a href="visualizza_profilo.php">Annulla</a></li></ul></nav>
    </header>

    <div class="edit-container">
        
        <h1>Modifica Profilo</h1>
        
        <?php if($status_msg) echo "<p style='color:red; margin-bottom:15px;'>$status_msg</p>"; ?>

        <form action="profilo.php" method="POST">
            
            <div class="form-group">
                <label>EMAIL (Non modificabile)</label>
                <input type="text" value="<?php echo htmlspecialchars($user['email']); ?>" disabled>
            </div>

            <div class="form-group">
                <label for="citta">CITTÀ</label>
                <input type="text" id="citta" name="citta" value="<?php echo htmlspecialchars($user['citta'] ?? ''); ?>" placeholder="Inserisci città">
            </div>

            <div class="form-group">
                <label for="livello">LIVELLO ESPERIENZA</label>
                <select name="livello" id="livello">
                    <option value="Beginner" <?php echo ($user['livello'] == 'Beginner') ? 'selected' : ''; ?>>Beginner</option>
                    <option value="Intermediate" <?php echo ($user['livello'] == 'Intermediate') ? 'selected' : ''; ?>>Intermediate</option>
                    <option value="Expert" <?php echo ($user['livello'] == 'Expert') ? 'selected' : ''; ?>>Expert</option>
                </select>
            </div>

            <div class="form-group">
                <label for="social">SOCIAL LINK</label>
                <input type="url" id="social" name="social" value="<?php echo htmlspecialchars($user['social_link'] ?? ''); ?>" placeholder="https://...">
            </div>

            <div class="form-group">
                <label for="bio">BIOGRAFIA</label>
                <textarea id="bio" name="bio" rows="4" placeholder="Scrivi qualcosa su di te..."><?php echo htmlspecialchars($user['bio'] ?? ''); ?></textarea>
            </div>

            <button type="submit" class="btn-play">SALVA MODIFICHE</button>
        </form>

        <a href="visualizza_profilo.php" class="back-link">Torna al profilo senza salvare</a>

    </div>

</body>
</html>