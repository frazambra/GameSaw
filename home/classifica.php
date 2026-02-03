<?php
session_start();
require_once '../connessione_db.php';

// 1. FACILE
$sqlFacile = "SELECT nome, cognome, punteggio_facile as pt, livello FROM utenti WHERE punteggio_facile > 0 ORDER BY punteggio_facile DESC LIMIT 10";
$resFacile = $conn->query($sqlFacile);

// 2. NORMALE (usa colonna 'punteggio')
$sqlNormale = "SELECT nome, cognome, punteggio as pt, livello FROM utenti WHERE punteggio > 0 ORDER BY punteggio DESC LIMIT 10";
$resNormale = $conn->query($sqlNormale);

// 3. DIFFICILE
$sqlDifficile = "SELECT nome, cognome, punteggio_difficile as pt, livello FROM utenti WHERE punteggio_difficile > 0 ORDER BY punteggio_difficile DESC LIMIT 10";
$resDifficile = $conn->query($sqlDifficile);

function renderTable($result, $id, $isActive = false) {
    $displayStyle = $isActive ? 'block' : 'none';
    // Nota: Ho aggiunto tbody qui sotto per sicurezza HTML standard
    echo "<div id='$id' class='ranking-tab' style='display: $displayStyle;'>";
    echo "<table>
            <thead>
                <tr>
                    <th>Pos</th>
                    <th>Giocatore</th>
                    <th>Liv</th>
                    <th>Punti</th>
                </tr>
            </thead>
            <tbody>";
    
    if ($result && $result->num_rows > 0) {
        $rank = 1;
        while($row = $result->fetch_assoc()) {
            echo "<tr>";
            echo "<td>#" . $rank . "</td>";
            
            $cognome = isset($row['cognome']) ? $row['cognome'] : '';
            $iniziale = strlen($cognome) > 0 ? $cognome[0] : '';
            echo "<td>" . htmlspecialchars($row['nome']) . " " . htmlspecialchars($iniziale) . ".</td>";
            
            echo "<td>" . htmlspecialchars($row['livello']) . "</td>";
            echo "<td><span class='score-badge'>" . $row['pt'] . "</span></td>";
            echo "</tr>";
            $rank++;
        }
    } else {
        echo "<tr><td colspan='4'>Nessun record per questa difficoltà!</td></tr>";
    }
    
    echo "</tbody></table></div>";
}
?>

<!DOCTYPE html>
<html lang="it">
<head>
    <meta charset="UTF-8">
    <title>Classifiche - GameSAW</title>
    <link rel="stylesheet" href="../style.css">
    <link rel="stylesheet" href="style_home.css">
</head>
<body>

    <header>
        <div class="logo">AimTrainer</div>
        <nav>
            <ul class="nav-links">
                <li><a href="Home.php">Torna al Gioco</a></li>
            </ul>
        </nav>
    </header>

    <main>
        <div class="leaderboard-container">
            <h1>🏆 Top 10 Giocatori 🏆</h1>
            
            <div class="tabs-header">
                <button class="tab-btn" onclick="openTab('tab-facile', this)">FACILE</button>
                <button class="tab-btn active" onclick="openTab('tab-normale', this)">NORMALE</button>
                <button class="tab-btn" onclick="openTab('tab-difficile', this)">DIFFICILE</button>
            </div>

            <?php renderTable($resFacile, 'tab-facile', false); ?>
            <?php renderTable($resNormale, 'tab-normale', true); ?>
            <?php renderTable($resDifficile, 'tab-difficile', false); ?>

        </div>
    </main>

    <script>
        function openTab(tabId, btnElement) {
            document.querySelectorAll('.ranking-tab').forEach(el => el.style.display = 'none');
            document.querySelectorAll('.tab-btn').forEach(el => el.classList.remove('active'));
            document.getElementById(tabId).style.display = 'block';
            btnElement.classList.add('active');
        }
    </script>
</body>
</html>