<?php
/* ==========================================
   PARTE 1: LOGICA (PHP)
   Qui gestiamo i dati prima di creare la pagina
   ========================================== */

// 1. Avvio della sessione (sempre la prima cosa!)
session_start();

// 2. Prepariamo le variabili da usare nell'HTML
// Creiamo una variabile booleana (Vero/Falso) per capire se l'utente è loggato
// Questo rende l'HTML molto più leggibile dopo.
$is_logged_in = isset($_SESSION['logged_in']) && $_SESSION['logged_in'] === true;

// 3. Prepariamo i dati dell'utente
if ($is_logged_in) {
    // Se è loggato, prendiamo il nome dalla sessione
    // Usiamo htmlspecialchars per sicurezza (evita che caratteri strani rompano l'HTML)
    $nome_utente = htmlspecialchars($_SESSION['user_nome']);
    $messaggio_benvenuto = "Bentornato, <strong>$nome_utente</strong>";
} else {
    // Se è un ospite
    $nome_utente = ""; // Vuoto
    $messaggio_benvenuto = "Benvenuto, Ospite";
}

// 4. (Opzionale) Definiamo l'immagine del profilo qui per non sporcare l'HTML
$immagine_profilo = "https://via.placeholder.com/40"; 

/* FINE PARTE PHP - Ora inizia l'HTML */
?>

<!DOCTYPE html>
<html lang="it">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>GameSAW - Home</title>
    <link rel="stylesheet" href="style.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
</head>
<body>

    <header>
        <div class="logo">GameSAW</div>
        
        <nav>
            <ul class="nav-links">
                <li><a href="#">Chi siamo</a></li>
                <li><a href="#">Cosa facciamo</a></li>
                <li><a href="#">Dove siamo</a></li>
                <li><a href="#">Contatti</a></li>
            </ul>

            <?php if ($is_logged_in): ?>
                
                <div class="user-menu-container">
                    <span class="welcome-msg"><?php echo $messaggio_benvenuto; ?></span>
                    
                    <div class="profile-dropdown">
                        <div class="profile-icon" id="profile-toggle">
                            <img src="<?php echo $immagine_profilo; ?>" alt="Profilo">
                        </div>

                        <div class="dropdown-content">
                            <a href="#">Area utente</a>
                            <a href="#">Modifica profilo</a>
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
            <h1 class="game-title">GameSAW</h1>
            
            <div id="user-area">
                <?php if (!$is_logged_in): ?>
                    <input type="text" id="nickname-input" placeholder="Inserisci il tuo Nickname" aria-label="Nickname">
                <?php endif; ?>
                
                <h2 id="user-display" class="<?php echo $is_logged_in ? '' : 'hidden'; ?>">
                    Pronto a giocare, <span id="display-name"><?php echo $nome_utente; ?></span>?
                </h2>
            </div>

            <button id="btn-play" class="btn-play-big">GIOCA ORA</button>
        </div>
    </main>

    <footer>
        <p>&copy; 2023 GameSAW - Tutti i diritti riservati</p>
    </footer>

    <script src="script.js"></script>
</body>
</html>