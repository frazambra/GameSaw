<?php
session_start();
require_once '../connessione_db.php'; // Controlla che il percorso sia giusto!

// 1. Controllo se l'utente è loggato
if (!isset($_SESSION['logged_in']) || $_SESSION['logged_in'] !== true) {
    header("Location: ../autenticazione/login.php");
    exit();
}

$user_id = $_SESSION['user_id'];
$can_review = false;
$msg = "";

// 2. Controllo se l'utente ha mai giocato (Punteggio > 0)
// Nota: Se nel tuo gioco il punteggio parte da 0 anche dopo aver giocato, 
// dovremmo aggiungere una colonna "partite_giocate" nel DB. 
// Per ora assumiamo che se hai punteggio > 0 hai giocato.
$sql = "SELECT punteggio FROM utenti WHERE id = ?";
$stmt = $conn->prepare($sql);
$stmt->bind_param("i", $user_id);
$stmt->execute();
$result = $stmt->get_result();
$row = $result->fetch_assoc();

if ($row['punteggio'] > 0) {
    $can_review = true;
}

// 3. Gestione salvataggio recensione
if ($_SERVER["REQUEST_METHOD"] == "POST" && $can_review) {
    $voto = $_POST['rating'];
    $commento = $_POST['commento'];

    // Controllo che il voto sia valido (1-5)
    if ($voto >= 1 && $voto <= 5) {
        $insert_sql = "INSERT INTO recensioni (user_id, voto, commento) VALUES (?, ?, ?)";
        $stmt_ins = $conn->prepare($insert_sql);
        $stmt_ins->bind_param("iis", $user_id, $voto, $commento);
        
        if ($stmt_ins->execute()) {
            // Redirect alla home o pagina di ringraziamento
            header("Location: Home.php?msg=recensione_ok");
            exit();
        } else {
            $msg = "Errore nel salvataggio.";
        }
    } else {
        $msg = "Devi selezionare un voto!";
    }
}
?>

<!DOCTYPE html>
<html lang="it">
<head>
    <meta charset="UTF-8">
    <title>Lascia una Recensione</title>
    <link rel="stylesheet" href="../style.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
</head>
<body>

    <header>
        <div class="logo">GameSAW</div>
        <nav><ul class="nav-links"><li><a href="Home.php">Torna alla Home</a></li></ul></nav>
    </header>

    <main>
        <div class="auth-container" style="max-width: 600px;">
            <h1>Valuta la tua esperienza</h1>
            
            <?php if ($can_review): ?>
                
                <p>Hai giocato e totalizzato <strong><?php echo $row['punteggio']; ?></strong> punti.</p>
                <p>Facci sapere cosa ne pensi!</p>

                <?php if($msg) echo "<p style='color:red'>$msg</p>"; ?>

                <form action="recensioni.php" method="POST" class="auth-form">
                    
                    <div class="rating-box">
                        <label>Il tuo voto:</label>
                        <div class="stars">
                            <input type="radio" id="star5" name="rating" value="5" /><label for="star5" title="Eccezionale"></label>
                            <input type="radio" id="star4" name="rating" value="4" /><label for="star4" title="Buono"></label>
                            <input type="radio" id="star3" name="rating" value="3" /><label for="star3" title="Medio"></label>
                            <input type="radio" id="star2" name="rating" value="2" /><label for="star2" title="Scarso"></label>
                            <input type="radio" id="star1" name="rating" value="1" /><label for="star1" title="Pessimo"></label>
                        </div>
                    </div>

                    <div class="form-group">
                        <label for="commento">Il tuo commento:</label>
                        <textarea name="commento" id="commento" rows="5" placeholder="Cosa ti è piaciuto? Cosa miglioreresti?" required></textarea>
                    </div>

                    <button type="submit" class="btn-play" style="width:100%">INVIA RECENSIONE</button>
                </form>

            <?php else: ?>
                
                <div style="text-align: center; padding: 20px;">
                    <i class="fa-solid fa-gamepad" style="font-size: 50px; color: #e94560; margin-bottom: 20px;"></i>
                    <h2 style="color: #ccc;">Non hai ancora giocato!</h2>
                    <p>Per poter valutare GameSAW devi completare almeno una partita.</p>
                    <br>
                    <a href="play.php" class="btn-play-big">GIOCA ORA</a>
                </div>

            <?php endif; ?>
        </div>
    </main>

</body>
</html>