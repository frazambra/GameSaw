<?php
// Game.php
// 1. Avviamo la sessione per sapere se l'utente è loggato
session_start();
?>
<!DOCTYPE html>
<html lang="it">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>GameSAW - Home</title>
    <link rel="stylesheet" href="style.css">
    
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

            <?php if (isset($_SESSION['logged_in']) && $_SESSION['logged_in'] === true): ?>
                
                <div class="user-menu-container">
                    <span class="welcome-msg">Benvenuto, <strong><?php echo htmlspecialchars($_SESSION['user_nome']); ?></strong></span>
                    
                    <div class="profile-dropdown">
                        <div class="profile-icon" id="profile-toggle">
                            <img src="https://via.placeholder.com/40" alt="Profilo">
                        </div>

                        <div class="dropdown-content">
                            <a href="#">Area utente</a>
                            <a href="#">Modifica profilo</a>
                            <a href="#" style="color: red;">Cancella profilo</a>
                            <hr> <a href="logout.php">Logout</a>
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
                <?php if (!isset($_SESSION['logged_in'])): ?>
                    <input type="text" id="nickname-input" placeholder="Inserisci il tuo Nickname" aria-label="Nickname">
                <?php endif; ?>
                
                <h2 id="user-display" class="<?php echo isset($_SESSION['logged_in']) ? '' : 'hidden'; ?>">
                    Pronto a giocare, <span id="display-name">
                        <?php echo isset($_SESSION['user_nome']) ? $_SESSION['user_nome'] : ''; ?>
                    </span>?
                </h2>
            </div>

            <button id="btn-play" class="btn-play-big">GIOCA ORA</button>
        </div>
    </main>


    <script src="script.js"></script>
</body>
</html>