<?php
session_start();
require_once '../connessione_db.php';

// SICUREZZA: Controllo accessi per impedire URL Injection tramite la barra degli indirizzi
if (!isset($_SESSION['admin_logged_in']) || $_SESSION['admin_logged_in'] !== true) {
    header("Location: ../autenticazione/login.php");
    exit();
}

$messaggio = "";

if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST['azione'])) {
    
    // 1. CANCELLAZIONE UTENTE (E PULIZIA RELATIVA)
    if ($_POST['azione'] === 'elimina_utente' && isset($_POST['user_id']) && is_numeric($_POST['user_id'])) {
        $id_da_eliminare = (int)$_POST['user_id'];
        
        if ($id_da_eliminare !== (int)$_SESSION['admin_id']) {
            
            // Rimozione preventiva delle recensioni dell'utente per rispettare i vincoli di Foreign Key
            $stmt_clean = $conn->prepare("DELETE FROM recensioni WHERE user_id = ?");
            $stmt_clean->bind_param("i", $id_da_eliminare);
            $stmt_clean->execute();
            $stmt_clean->close();

            // Rimozione dell'account utente vero e proprio
            $stmt_del = $conn->prepare("DELETE FROM utenti WHERE id = ? AND is_admin = 0");
            $stmt_del->bind_param("i", $id_da_eliminare);
            if ($stmt_del->execute()) { 
                $messaggio = "Giocatore e relative recensioni eliminati definitivamente."; 
            }
            $stmt_del->close();
        }
    }
    
   
    if ($_POST['azione'] === 'toggle_ban' && isset($_POST['user_id']) && is_numeric($_POST['user_id']) && isset($_POST['stato_attuale'])) {
        $id_utente = (int)$_POST['user_id'];
        $stato_attuale = (int)$_POST['stato_attuale'];
        $nuovo_stato = ($stato_attuale === 1) ? 0 : 1;

        if ($id_utente !== (int)$_SESSION['admin_id']) {
            $stmt_ban = $conn->prepare("UPDATE utenti SET is_banned = ? WHERE id = ? AND is_admin = 0");
            $stmt_ban->bind_param("ii", $nuovo_stato, $id_utente);
            if ($stmt_ban->execute()) { 
                $messaggio = ($nuovo_stato === 1) ? "Utente bannato correttamente." : "Utente sbloccato correttamente."; 
            }
            $stmt_ban->close();
        }
    }

    
    if ($_POST['azione'] === 'elimina_recensione' && isset($_POST['review_id']) && is_numeric($_POST['review_id'])) {
        $review_id = (int)$_POST['review_id'];
        
        $stmt_del_rev = $conn->prepare("DELETE FROM recensioni WHERE id = ?");
        $stmt_del_rev->bind_param("i", $review_id);
        if ($stmt_del_rev->execute()) {
            $messaggio = "Recensione rimossa con successo.";
        }
        $stmt_del_rev->close();
    }
}


$recensioni_utente = null;
$nome_utente_selezionato = "";

if (isset($_GET['view_reviews']) && is_numeric($_GET['view_reviews'])) {
    $id_utente_scelto = (int)$_GET['view_reviews'];
    
    $stmt_u = $conn->prepare("SELECT nome FROM utenti WHERE id = ?");
    $stmt_u->bind_param("i", $id_utente_scelto);
    $stmt_u->execute();
    $res_u = $stmt_u->get_result();
    if($u_row = $res_u->fetch_assoc()) {
        $nome_utente_selezionato = $u_row['nome'];
    }
    $stmt_u->close();

   
    $stmt_rev = $conn->prepare("SELECT id, voto, commento, data_recensione FROM recensioni WHERE user_id = ? ORDER BY id DESC");
    $stmt_rev->bind_param("i", $id_utente_scelto);
    $stmt_rev->execute();
    $recensioni_utente = $stmt_rev->get_result();
}

$sql_utenti = "SELECT u.id, u.nome, u.email, u.livello, u.punteggio, u.is_banned, COUNT(r.id) AS tot_recensioni 
               FROM utenti u 
               LEFT JOIN recensioni r ON u.id = r.user_id 
               WHERE u.is_admin = 0 
               GROUP BY u.id, u.nome, u.email, u.livello, u.punteggio, u.is_banned 
               ORDER BY u.id DESC";
$result_utenti = $conn->query($sql_utenti);
?>

<!DOCTYPE html>
<html lang="it">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Dashboard Admin - AimTrainer</title>
    <link rel="stylesheet" href="../style.css">
    <link rel="stylesheet" href="style_admin.css?v=<?php echo time(); ?>">
</head>
<body>

    <header>
        <div class="logo">AimTrainer <span class="logo-subtitle">| Dashboard Admin</span></div>
        <nav>
            <ul class="nav-links admin-nav-links">
                <li class="logged-in-text">Loggato come: <strong class="username-highlight"><?php echo htmlspecialchars($_SESSION['admin_nome']); ?></strong></li>
                <li><a href="../autenticazione/logout.php" class="btn btn-outline btn-logout-override">Logout</a></li>
            </ul>
        </nav>
    </header>

    <main class="admin-main">
        
        <div class="admin-container">
            <h1 class="admin-title">Gestione Giocatori</h1>
            
            <?php if ($messaggio !== "" && !isset($_POST['review_id'])): ?>
                <div class="alert-success"><?php echo htmlspecialchars($messaggio); ?></div>
            <?php endif; ?>

            <div class="admin-table-wrapper">
                <table class="admin-table">
                    <thead>
                        <tr>
                            <th>ID</th>
                            <th>Nome Giocatore</th>
                            <th>Email</th>
                            <th>Punteggio Max</th>
                            <th>Stato</th>
                            <th>Recensioni</th>
                            <th>Azioni Account</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php if ($result_utenti->num_rows > 0): ?>
                            <?php while($row = $result_utenti->fetch_assoc()): ?>
                                <tr>
                                    <td class="text-muted">#<?php echo $row['id']; ?></td>
                                    <td class="text-bold"><?php echo htmlspecialchars($row['nome']); ?></td>
                                    <td><?php echo htmlspecialchars($row['email']); ?></td>
                                    <td><span class="text-highlight"><?php echo $row['punteggio'] ?? 0; ?></span> pti</td>
                                    <td>
                                        <?php if ($row['is_banned'] == 1): ?>
                                            <span class="badge badge-banned">Bannato</span>
                                        <?php else: ?>
                                            <span class="badge badge-active">Attivo</span>
                                        <?php endif; ?>
                                    </td>
                                    
                                    <td>
                                        <?php if ($row['tot_recensioni'] > 0): ?>
                                            <a href="dashboard.php?view_reviews=<?php echo $row['id']; ?>" class="btn-action btn-view">
                                                Vedi (<?php echo $row['tot_recensioni']; ?>)
                                            </a>
                                        <?php else: ?>
                                            <span class="text-italic text-small text-muted">Nessuna</span>
                                        <?php endif; ?>
                                    </td>
                                    
                                    <td>
                                        <div class="action-buttons">
                                            <form action="dashboard.php" method="POST">
                                                <input type="hidden" name="azione" value="toggle_ban">
                                                <input type="hidden" name="user_id" value="<?php echo $row['id']; ?>">
                                                <input type="hidden" name="stato_attuale" value="<?php echo $row['is_banned']; ?>">
                                                <button type="submit" class="btn-action <?php echo ($row['is_banned'] == 1) ? 'btn-unban' : 'btn-ban'; ?>">
                                                    <?php echo ($row['is_banned'] == 1) ? 'Sblocca' : 'Banna'; ?>
                                                </button>
                                            </form>

                                            <form action="dashboard.php" method="POST" onsubmit="return confirm('Sei sicuro? Questa azione eliminerà permanentemente l\'account e tutte le recensioni collegate.');">
                                                <input type="hidden" name="azione" value="elimina_utente">
                                                <input type="hidden" name="user_id" value="<?php echo $row['id']; ?>">
                                                <button type="submit" class="btn-action btn-delete">Elimina</button>
                                            </form>
                                        </div>
                                    </td>
                                </tr>
                            <?php endwhile; ?>
                        <?php else: ?>
                            <tr>
                                <td colspan="7" class="no-data">Nessun giocatore presente all'interno della piattaforma.</td>
                            </tr>
                        <?php endif; ?>
                    </tbody>
                </table>
            </div>
        </div>

        <?php if ($recensioni_utente !== null): ?>
            <div class="reviews-panel">
                <h2 class="admin-title reviews-title">
                    Recensioni pubblicate da: <span class="username-highlight"><?php echo htmlspecialchars($nome_utente_selezionato); ?></span>
                </h2>

                <?php if ($messaggio !== "" && isset($_POST['review_id'])): ?>
                    <div class="alert-success"><?php echo htmlspecialchars($messaggio); ?></div>
                <?php endif; ?>

                <div class="admin-table-wrapper">
                    <table class="admin-table">
                        <thead>
                            <tr>
                                <th>ID Recensione</th>
                                <th>Valutazione</th>
                                <th>Commento</th>
                                <th>Data di Pubblicazione</th>
                                <th>Azione</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php if ($recensioni_utente->num_rows > 0): ?>
                                <?php while($rev = $recensioni_utente->fetch_assoc()): ?>
                                    <tr>
                                        <td class="text-muted">#<?php echo $rev['id']; ?></td>
                                        <td class="rating-col"><?php echo $rev['voto']; ?> / 5 ⭐</td>
                                        <td class="review-comment-col"><?php echo nl2br(htmlspecialchars($rev['commento'])); ?></td>
                                        <td class="review-date-col"><?php echo $rev['data_recensione']; ?></td>
                                        <td>
                                            <form action="dashboard.php?view_reviews=<?php echo $id_utente_scelto; ?>" method="POST" onsubmit="return confirm('Vuoi davvero rimuovere questa recensione?');">
                                                <input type="hidden" name="azione" value="elimina_recensione">
                                                <input type="hidden" name="review_id" value="<?php echo $rev['id']; ?>">
                                                <button type="submit" class="btn-action btn-delete btn-delete-review">
                                                    Elimina Recensione
                                                </button>
                                            </form>
                                        </td>
                                    </tr>
                                <?php endwhile; ?>
                            <?php else: ?>
                                <tr>
                                    <td colspan="5" class="no-data">Questo utente non possiede più alcuna recensione attiva.</td>
                                </tr>
                            <?php endif; ?>
                        </tbody>
                    </table>
                </div>
                
                <div class="close-panel-wrapper">
                    <a href="dashboard.php" class="btn-action btn-close-panel">Chiudi Dettaglio</a>
                </div>
            </div>
            <?php if(isset($stmt_rev)) $stmt_rev->close(); ?>
        <?php endif; ?>

    </main>

</body>
</html>
<?php $conn->close(); ?>