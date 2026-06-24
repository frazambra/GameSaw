<?php
 // 1. Includiamo la connessione al DB
  require_once '../connessione_db.php';

  // 2. Avviamo la sessione (FONDAMENTALE per gestire il login)
 session_start();

 if ($_SERVER["REQUEST_METHOD"] == "POST") {

    // VERIFICA INPUT: Controlliamo esplicitamente che i dati esistano nell'array $_POST
    if (isset($_POST['email']) && isset($_POST['password'])) {
        
        $email = trim($_POST['email']);
        $password = $_POST['password'];

        // Verifichiamo che i campi non siano vuoti
        if (!empty($email) && !empty($password)) {

            // 3. Cerchiamo l'utente tramite email (selezioniamo anche is_admin e is_banned)
            $sql = "SELECT id, nome, password, is_admin, is_banned FROM utenti WHERE email = ?";
            
            $stmt = $conn->prepare($sql);
            $stmt->bind_param("s", $email);
            $stmt->execute();
            $result = $stmt->get_result();

            // Se troviamo un utente con quella email
            if ($result->num_rows === 1) {
                $row = $result->fetch_assoc();
                
                // CONTROLLO BAN: Se l'utente è stato bloccato dall'admin, neghiamo l'accesso immediato
                if ($row['is_banned'] == 1) {
                    header("Location: login.php?error=banned");
                    exit();
                }
                
                // 4. Verifichiamo la password
                if (password_verify($password, $row['password'])) {
                    
                    // PASSWORD CORRETTA! Ora controlliamo il RUOLO (is_admin)
                    if ($row['is_admin'] == 1) {
                        
                        // È UN AMMINISTRATORE -> Creiamo la sessione admin e lo mandiamo alla Dashboard
                        $_SESSION['admin_id'] = $row['id'];
                        $_SESSION['admin_nome'] = $row['nome'];
                        $_SESSION['admin_logged_in'] = true;

                        header("Location: ../admin/dashboard.php");
                        exit();
                    } else {
                        
                        // È UN GIOCATORE NORMALE -> Sessione classica e lo mandiamo alla Home del gioco
                        $_SESSION['user_id'] = $row['id'];
                        $_SESSION['user_nome'] = $row['nome'];
                        $_SESSION['logged_in'] = true;

                        header("Location: ../home/Home.php");
                        exit();
                    }

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
        } else {
            // Campi inviati ma vuoti (es. solo spazi)
            header("Location: login.php?error=empty");
            exit();
        }
    }
    $conn->close();
} 

    // Controllo parametri GET direttamente con PHP per mostrare gli avvisi
    if (isset($_GET['error']) && $_GET['error'] == '1') {
        echo '<script>alert("Attenzione: Email o Password errati.");</script>';
    }
    if (isset($_GET['error']) && $_GET['error'] == 'banned') {
        echo '<script>alert("Accesso negato: Il tuo account è stato sospeso dall\'amministratore.");</script>';
    }
    if (isset($_GET['error']) && $_GET['error'] == 'empty') {
        echo '<script>alert("Attenzione: Tutti i campi sono obbligatori.");</script>';
    }
    if (isset($_GET['registered'])) {
        echo '<script>alert("Registrazione completata! Ora puoi accedere.");</script>';
    }
?>

<!DOCTYPE html>
<html lang="it">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Accedi - AimTrainer</title>
    <link rel="stylesheet" href="../style.css">
    <link rel="stylesheet" href="style_auth.css">
</head>
<body>

    <header>
        <div class="logo">AimTrainer</div>
        <nav>
            <ul class="nav-links">
                <li><a href="../home/Home.php">Torna alla Home</a></li>
            </ul>
        </nav>
    </header>

    <main>
        <section class="auth-container">
            <h1>Accedi</h1>
            <p>Inserisci le tue credenziali per entrare.</p>

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

    <script src="../script.js"></script>
</body>
</html>