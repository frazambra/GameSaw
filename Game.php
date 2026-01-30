<?php
/* ==========================================
   LOGICA PHP
   ========================================== */
session_start();

$is_logged_in = isset($_SESSION['logged_in']) && $_SESSION['logged_in'] === true;

// Prepariamo il nome utente se loggato
if ($is_logged_in) {
    $nome_utente = htmlspecialchars($_SESSION['user_nome']);
} else {
    $nome_utente = ""; 
}
?>

<!DOCTYPE html>
<html lang="it">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>AimTrainer - Home</title>
    <link rel="stylesheet" href="style.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
</head>
<body>

    <header>
        <div class="logo">AimTrainer</div>
        
        <nav>
            <ul class="nav-links">
                <li><a href="info.html">Informazioni & Contatti</a></li>
                <li><a href="classifica.php">🏆 Classifica</a></li>
            </ul>

            <?php if ($is_logged_in): ?>
                
                <div class="user-menu-container">
                    <div class="profile-dropdown">
                        <div class="profile-icon">
                            <i class="fa-solid fa-circle-user fa-2x" style="color: #e94560;"></i>
                        </div>

                        <div class="dropdown-content">
                            <div style="padding: 10px; color: #aaa; font-size: 12px; text-align: center;">
                                <?php echo $nome_utente; ?>
                            </div>
                            <hr>
                            <a href="visualizza_profilo.php">Area utente</a>
                            <a href="#" style="color: red;">Cancella profilo</a>
                            <hr>
                            <a href="logout.php">Logout</a>
                        </div>
                    </div>
                </div>

            <?php else: ?>

                <div class="auth-buttons">
                    <a href="login.php"><button id="btn-login" class="btn btn-outline">Login</button></a>
                    <a href="registrazione.html"><button id="btn-register" class="btn btn-solid">Registrati</button></a>
                </div>

            <?php endif; ?>

        </nav>
    </header>

    <main>
        <div class="game-container">
            
            <?php if ($is_logged_in): ?>
                <h2 class="welcome-center">
                    Bentornato, <span style="color: #e94560;"><?php echo $nome_utente; ?></span>
                </h2>
            <?php endif; ?>

            <h1 class="game-title">AimTrainer</h1>
            
            <div id="user-area">
                <?php if (!$is_logged_in): ?>
                    <input type="text" id="nickname-input" placeholder="Inserisci il tuo Nickname" aria-label="Nickname">
                <?php endif; ?>
                
                </div>

            <button id="btn-play" class="btn-play">GIOCA ORA</button>
        </div>
    </main>

    <script src="script.js"></script>
</body>
</html>