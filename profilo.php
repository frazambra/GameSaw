<?php
session_start();
require_once 'connessione_db.php';

// Se non è loggato, torna alla home
if (!isset($_SESSION['logged_in'])) {
    header("Location: game.php");
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
    <link rel="stylesheet" href="style.css">
    <style>
        /* RESET E STILE FORZATO PER QUESTA PAGINA */
        body {
            display: flex;
            flex-direction: column;
            min-height: 100vh;
            align-items: center; /* Centra orizzontalmente tutto il body */
        }

        .edit-container {
            background-color: #16213e; /* Sfondo scuro del box */
            width: 100%;
            max-width: 500px; /* Larghezza massima contenuta */
            margin-top: 50px;
            margin-bottom: 50px;
            padding: 40px;
            border-radius: 15px;
            box-shadow: 0 4px 15px rgba(0,0,0,0.5);
            text-align: center; /* Centra tutti i testi e elementi inline */
            display: flex;
            flex-direction: column;
            align-items: center; /* Centra i div interni (flex items) */
        }

        h1 {
            color: #e94560;
            margin-bottom: 30px;
            font-size: 28px;
            text-transform: uppercase;
            width: 100%;
            border-bottom: 1px solid #1a1a2e;
            padding-bottom: 15px;
        }

        /* Forza il form a stare in colonna e centrato */
        form {
            width: 100%;
            display: flex;
            flex-direction: column;
            align-items: center; 
            gap: 20px; /* Spazio tra i campi */
        }

        .form-group {
            width: 100%;
            text-align: left; /* Le etichette si leggono meglio a sinistra */
        }

        .form-group label {
            display: block;
            margin-bottom: 5px;
            color: #e94560;
            font-weight: bold;
            font-size: 0.9em;
            text-align: center; /* OK, centro anche le etichette come richiesto */
        }

        /* Stile input per farli sembrare centrati e belli */
        input[type="text"],
        input[type="url"],
        textarea,
        select {
            width: 100%;
            padding: 12px;
            border-radius: 5px;
            border: 1px solid #1a1a2e;
            background-color: #0f1526;
            color: white;
            font-size: 16px;
            text-align: center; /* Testo centrato dentro l'input */
        }

        input:disabled {
            background-color: #1a1a2e;
            color: #666;
            border: 1px solid transparent;
        }

        .btn-play {
            width: 100%; /* Bottone largo quanto il box */
            margin-top: 10px;
            padding: 15px;
            font-size: 18px;
            cursor: pointer;
        }

        .back-link {
            margin-top: 20px;
            color: #aaa;
            text-decoration: none;
            font-size: 14px;
        }
        .back-link:hover { color: white; }

    </style>
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