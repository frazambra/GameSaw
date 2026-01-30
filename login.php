<!DOCTYPE html>
<html lang="it">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Accedi - AimTrainer</title>
    <link rel="stylesheet" href="style.css">
</head>
<body>

    <header>
        <div class="logo">AimTrainer</div>
        <nav>
            <ul class="nav-links">
                <li><a href="Game.php">Torna alla Home</a></li>
            </ul>
        </nav>
    </header>

    <main>
        <section class="auth-container">
            <h1>Accedi</h1>
            <p>Bentornato! Inserisci le tue credenziali.</p>

            <form action="login.php" method="POST" class="auth-form">
                
                <div class="form-group">
                    <label for="email">Email:</label>
                    <input type="email" id="email" name="email" required placeholder="La tua email">
                </div>

                <div class="form-group">
                    <label for="pwd">Password:</label>
                    <input type="password" id="pwd" name="password" required placeholder="La tua password">
                </div>

                <button type="submit" class="btn-play">ACCEDI</button>
            </form>
            
            <p class="auth-footer">Non hai un account? <a href="registrazione.html">Registrati qui</a></p>
        </section>
    </main>


    <script src="script.js"></script>

    <?php
 // 1. Includiamo la connessione al DB
  require_once 'connessione_db.php';

  // 2. Avviamo la sessione (FONDAMENTALE per gestire il login)
 session_start();

 if ($_SERVER["REQUEST_METHOD"] == "POST") {

    $email = $_POST['email'];
    $password = $_POST['password'];

    // 3. Cerchiamo l'utente tramite email
    // Selezioniamo id, nome e password (l'hash) dal DB
    $sql = "SELECT id, nome, password FROM utenti WHERE email = ?";
    
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("s", $email);
    $stmt->execute();
    $result = $stmt->get_result();

    // Se troviamo un utente con quella email
    if ($result->num_rows === 1) {
        $row = $result->fetch_assoc();
        
        // 4. Verifichiamo la password
        // password_verify confronta la password scritta con l'hash nel DB
        if (password_verify($password, $row['password'])) {
            
            // PASSWORD CORRETTA!
            
            // Salviamo i dati nella sessione del server
            $_SESSION['user_id'] = $row['id'];
            $_SESSION['user_nome'] = $row['nome'];
            $_SESSION['logged_in'] = true;

            // Rimandiamo l'utente alla Home del gioco (o alla dashboard)
            header("Location: Game.php");
            exit();

        } else {
            // Password sbagliata
            header("Location: login.php?error=1");
            exit();
        }
    } else {
        // Nessun utente trovato con questa email
        header("Location: login.php?error=1");
        exit();
    }

    $stmt->close();
    $conn->close();
} 

    // Controllo parametri GET direttamente con PHP (Slide PHP/Form)
    if (isset($_GET['error'])) {
        echo '<script>alert("Attenzione: Email o Password errati.");</script>';
    }
    if (isset($_GET['registered'])) {
        echo '<script>alert("Registrazione completata! Ora puoi accedere.");</script>';
    }
    ?>

</body>
</html>