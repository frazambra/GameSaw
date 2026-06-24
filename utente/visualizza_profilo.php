<?php
session_start();
require_once '../connessione_db.php';

if (!isset($_SESSION['logged_in']) || $_SESSION['logged_in'] !== true) {
    header("Location: ../home/Home.php");
    exit();
}

$user_id = $_SESSION['user_id'];

// Recupero dati utente
$stmt = $conn->prepare("SELECT * FROM utenti WHERE id = ?");
$stmt->bind_param("i", $user_id);
$stmt->execute();
$result = $stmt->get_result();
$user = $result->fetch_assoc();

// Recupero Badge sbloccati dall'utente
$sql_badges = "SELECT b.nome, b.descrizione, b.icona 
               FROM badges b 
               JOIN user_badges ub ON b.id = ub.badge_id 
               WHERE ub.user_id = ?";
$stmt_b = $conn->prepare($sql_badges);
$stmt_b->bind_param("i", $user_id);
$stmt_b->execute();
$result_badges = $stmt_b->get_result();
?>
<!DOCTYPE html>
<html lang="it">
<head>
    <meta charset="UTF-8">
    <title>Mio Profilo - AimTrainer</title>
    <link rel="stylesheet" href="../style.css">
    <link rel="stylesheet" href="style_utente.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
</head>
<body>
    <header style="width: 100%">
        <div class="logo">AimTrainer</div>
        <nav><ul class="nav-links"><li><a href="../home/Home.php">Home</a></li></ul></nav>
    </header>

    <main>
        <div class="profile-card">
            <div style="text-align: center;">
                <h1><?php echo htmlspecialchars($user['nome']); ?></h1>
                <p style="color: #aaa; margin-top: -15px;">Profilo Giocatore</p>
            </div>

            <div class="stats-row">
                <div class="stat-box">
                    <i class="fa-solid fa-star" style="color: gold;"></i>
                    <span class="stat-num"><?php echo $user['stelle_totali'] ?? 0; ?></span>
                    <span class="stat-label">Stelle Rare</span>
                </div>
                <div class="stat-box">
                    <i class="fa-solid fa-trophy" style="color: #e94560;"></i>
                    <span class="stat-num"><?php echo $result_badges->num_rows; ?></span>
                    <span class="stat-label">Badge</span>
                </div>
            </div>

            <hr style="border: 0; border-top: 1px solid #333; margin: 20px 0;">

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

            <div class="badges-section">
                <span class="label">I Miei Badge</span>
                <div class="badges-grid">
                    <?php if ($result_badges->num_rows > 0): ?>
                        <?php while($badge = $result_badges->fetch_assoc()): ?>
                            <div class="badge-item" title="<?php echo htmlspecialchars($badge['descrizione']); ?>">
                                <div class="badge-icon">
                                    <i class="fa-solid <?php echo $badge['icona']; ?>"></i>
                                </div>
                                <span class="badge-name"><?php echo htmlspecialchars($badge['nome']); ?></span>
                            </div>
                        <?php endwhile; ?>
                    <?php else: ?>
                        <p style="color: #777; font-size: 0.9em;">Ancora nessun badge sbloccato. Gioca per vincerli!</p>
                    <?php endif; ?>
                </div>
            </div>

            <div style="text-align: center; margin-top: 30px;">
                <a href="profilo.php" class="btn-play" style="text-decoration: none; display: inline-block;">MODIFICA PROFILO</a>
            </div>
        </div>
    </main>
</body>
</html>