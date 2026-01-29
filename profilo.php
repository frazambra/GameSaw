<?php
session_start();
require_once 'connessione_db.php';

// Protezione pagina: se non è loggato, torna alla home
if (!isset($_SESSION['logged_in']) || $_SESSION['logged_in'] !== true) {
    header("Location: Game.php");
    exit();
}

$user_id = $_SESSION['user_id'];
$messaggio = "";

// --- LOGICA DI AGGIORNAMENTO (POST) ---
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $citta = $_POST['citta'];
    $bio = $_POST['bio'];
    $social = $_POST['social'];
    $livello = $_POST['livello'];

    $sql_update = "UPDATE utenti SET citta = ?, bio = ?, social_link = ?, livello = ? WHERE id = ?";
    $stmt = $conn->prepare($sql_update);
    $stmt->bind_param("ssssi", $citta, $bio, $social, $livello, $user_id);

    if ($stmt->execute()) {
        $messaggio = "<p style='color: #4ee44e;'>Profilo aggiornato con successo!</p>";
    } else {
        $messaggio = "<p style='color: #ff4d4d;'>Errore durante l'aggiornamento.</p>";
    }
    $stmt->close();
}

// --- RECUPERO DATI CORRENTI ---
$sql = "SELECT * FROM utenti WHERE id = ?";
$stmt = $conn->prepare($sql);
$stmt->bind_param("i", $user_id);
$stmt->execute();
$result = $stmt->get_result();
$user = $result->fetch_assoc();
?>

<!DOCTYPE html>
<html lang="it">
<head>
    <meta charset="UTF-8">
    <title>Profilo - GameSAW</title>
    <link rel="stylesheet" href="style.css">
</head>
<body>
    <header>
        <div class="logo">GameSAW</div>
        <nav>
            <ul class="nav-links">
                <li><a href="Game.php">Torna al Gioco</a></li>
            </ul>
        </nav>
    </header>

    <main>
        <section class="auth-container">
            <h1>Il tuo Profilo</h1>
            <?php echo $messaggio; ?>

            <form action="profilo.php" method="POST" class="auth-form">
                <div class="form-group">
                    <label>Email (non modificabile):</label>
                    <input type="text" value="<?php echo htmlspecialchars($user['email']); ?>" disabled>
                </div>

                <div class="form-group">
                    <label for="nome">Nome:</label>
                    <input type="text" name="nome" value="<?php echo htmlspecialchars($user['nome']); ?>" disabled>
                </div>

                <div class="form-group">
                    <label for="citta">Città:</label>
                    <input type="text" id="citta" name="citta" value="<?php echo htmlspecialchars($user['citta'] ?? ''); ?>" placeholder="La tua città">
                </div>

                <div class="form-group">
                    <label for="bio">Su di me (Bio):</label>
                    <textarea id="bio" name="bio" rows="4"><?php echo htmlspecialchars($user['bio'] ?? ''); ?></textarea>
                </div>

                <div class="form-group">
                    <label for="social">Link Social:</label>
                    <input type="url" id="social" name="social" value="<?php echo htmlspecialchars($user['social_link'] ?? ''); ?>" placeholder="https://instagram.com/tuo-profilo">
                </div>

                <div class="form-group">
                    <label for="livello">Livello Esperienza:</label>
                    <select name="livello" id="livello">
                        <option value="Beginner" <?php if($user['livello'] == 'Beginner') echo 'selected'; ?>>Beginner</option>
                        <option value="Intermediate" <?php if($user['livello'] == 'Intermediate') echo 'selected'; ?>>Intermediate</option>
                        <option value="Expert" <?php if($user['livello'] == 'Expert') echo 'selected'; ?>>Expert</option>
                    </select>
                </div>

                <button type="submit" class="btn-play">SALVA MODIFICHE</button>
            </form>
            
            <p class="auth-footer"><a href="logout.php">Vuoi uscire? Logout</a></p>
        </section>
    </main>
</body>
</html>