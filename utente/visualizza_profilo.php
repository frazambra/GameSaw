<?php
session_start();
require_once '../connessione_db.php';

// Se non è loggato, via
if (!isset($_SESSION['logged_in']) || $_SESSION['logged_in'] !== true) {
    header("Location: ../home/Home.php");
    exit();
}

$user_id = $_SESSION['user_id'];

// Recupero dati
$stmt = $conn->prepare("SELECT * FROM utenti WHERE id = ?");
$stmt->bind_param("i", $user_id);
$stmt->execute();
$result = $stmt->get_result();
$user = $result->fetch_assoc();

if (!$user) {
    die("Errore: Utente non trovato.");
}
?>
<!DOCTYPE html>
<html lang="it">
<head>
    <meta charset="UTF-8">
    <title>Mio Profilo - AimTrainer</title>
    <link rel="stylesheet" href="../style.css">
    <link rel="stylesheet" href="style_utente.css">
</head>
<body>
    <header style="width: 100%">
        <div class="logo">AimTrainer</div>
        <nav><ul class="nav-links"><li><a href="../home/Home.php">Home</a></li></ul></nav>
    </header>

    <main>
        <div class="profile-card">
            <div style="text-align: center;">
                <h1>Il Mio Profilo</h1>
            </div>
            
            <hr style="border: 0.5px solid #1a1a2e; margin: 20px 0;">

            <div class="info-box">
                <span class="label">Nome</span>
                <span class="value"><?php echo htmlspecialchars($user['nome'] . " " . $user['cognome']); ?></span>
            </div>

            <div class="info-box">
                <span class="label">Email</span>
                <span class="value"><?php echo htmlspecialchars($user['email']); ?></span>
            </div>

            <div class="info-box">
                <span class="label">Livello Esperienza</span>
                <span class="value"><?php echo htmlspecialchars($user['livello'] ?? 'Beginner'); ?></span>
            </div>

            <?php if (!empty($user['citta'])): ?>
            <div class="info-box">
                <span class="label">Città</span>
                <span class="value"><?php echo htmlspecialchars($user['citta']); ?></span>
            </div>
            <?php endif; ?>

            <?php if (!empty($user['bio'])): ?>
            <div class="info-box">
                <span class="label">Bio</span>
                <p class="value"><?php echo nl2br(htmlspecialchars($user['bio'])); ?></p>
            </div>
            <?php endif; ?>

            <?php if (!empty($user['social_link'])): ?>
            <div class="info-box">
                <span class="label">Social</span>
                <a href="<?php echo htmlspecialchars($user['social_link']); ?>" target="_blank" style="color: #4ee44e;">Visita Link</a>
            </div>
            <?php endif; ?>

            <div style="text-align: center; margin-top: 30px;">
                <a href="profilo.php" class="btn-play" style="text-decoration: none; display: inline-block;">MODIFICA PROFILO</a>
            </div>
        </div>
    </main>
</body>
</html>