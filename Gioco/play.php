<?php
session_start();

// Verifica se l'utente è loggato
$is_logged_in = isset($_SESSION['logged_in']) && $_SESSION['logged_in'] === true;
$nome_giocatore = "Ospite";

// LOGICA NOME GIOCATORE
if ($is_logged_in) {
    // Se loggato, prende il nome dalla sessione
    $nome_giocatore = htmlspecialchars($_SESSION['user_nome']);
} 
?>

<!DOCTYPE html>
<html lang="it">
<head>
    <meta charset="UTF-8">
    <title>AimTrainer Arena</title>
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    
    <link rel="stylesheet" href="../style.css"> 
    
    <link rel="stylesheet" href="style_gioco.css"> 
</head>
<body>

    <header>
        <div class="logo">AimTrainer_Arena</div>
        <nav>
            <ul class="nav-links">
                <li><a href="../home/Home.php">Torna alla Home</a></li>
            </ul>
        </nav>
    </header>

    <main>
        <div class="game-wrapper">
            
            <div class="hud-header">
                <h2>Player: <span style="color: #e94560;"><?php echo $nome_giocatore; ?></span></h2>
                
                <?php if (!$is_logged_in): ?>
                    <span class="guest-warning">⚠️ Modalità Ospite: Punteggio non salvato</span>
                <?php endif; ?>
            </div>

            <div id="hud-bar">
                <div class="hud-item">PUNTI: <span id="score">0</span></div>
                <div class="hud-item">TEMPO: <span id="time">30</span>s</div>
            </div>

            <div id="aim-arena">
                <div id="overlay-screen">
                    <h2>Pronto?</h2>
                    <p>Colpisci i bersagli rossi!</p>
                    <button id="btn-start-game" class="btn-play-big">START</button>
                </div>
            </div>

        </div>
    </main>
    
    <script>
        const USER_IS_LOGGED = <?php echo $is_logged_in ? 'true' : 'false'; ?>;
    </script>
    
    <script src="script_gioco.js"></script>

</body>
</html>