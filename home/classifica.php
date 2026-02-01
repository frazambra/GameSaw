<?php
session_start();
require_once '../connessione_db.php';

// Query per prendere i migliori 10 giocatori
// Usiamo ORDER BY DESC per avere prima i punteggi alti
$sql = "SELECT nome, cognome, punteggio, livello FROM utenti ORDER BY punteggio DESC LIMIT 10";
$result = $conn->query($sql);
?>

<!DOCTYPE html>
<html lang="it">
<head>
    <meta charset="UTF-8">
    <title>Classifica Top 10 - GameSAW</title>
    <link rel="stylesheet" href="../style.css">
    <style>
        /* Stile specifico per la tabella della classifica */
        .leaderboard-container {
            max-width: 800px;
            margin: 40px auto;
            background-color: #16213e;
            padding: 30px;
            border-radius: 15px;
            box-shadow: 0 4px 15px rgba(0,0,0,0.5);
            text-align: center;
        }

        h1 {
            color: #e94560;
            margin-bottom: 20px;
            text-transform: uppercase;
        }

        table {
            width: 100%;
            border-collapse: collapse;
            margin-top: 20px;
        }

        th, td {
            padding: 15px;
            text-align: center;
            border-bottom: 1px solid #1a1a2e;
        }

        th {
            background-color: #1a1a2e;
            color: #e94560;
            font-size: 1.1em;
            text-transform: uppercase;
        }

        tr:hover {
            background-color: #0f3460; /* Effetto hover sulle righe */
        }

        /* Colora d'oro, argento e bronzo i primi 3 posti */
        tr:nth-child(1) td:first-child { color: #FFD700; font-weight: bold; font-size: 1.2em; } /* Oro */
        tr:nth-child(2) td:first-child { color: #C0C0C0; font-weight: bold; font-size: 1.2em; } /* Argento */
        tr:nth-child(3) td:first-child { color: #CD7F32; font-weight: bold; font-size: 1.2em; } /* Bronzo */

        .score-badge {
            background-color: #e94560;
            color: white;
            padding: 5px 10px;
            border-radius: 15px;
            font-weight: bold;
        }
    </style>
</head>
<body>

    <header>
        <div class="logo">GameSAW</div>
        <nav>
            <ul class="nav-links">
                <li><a href="Home.php">Torna al Gioco</a></li>
            </ul>
        </nav>
    </header>

    <main>
        <div class="leaderboard-container">
            <h1>🏆 Top 10 Giocatori 🏆</h1>
            <p>I migliori guerrieri di GameSAW</p>

            <table>
                <thead>
                    <tr>
                        <th>Posizione</th>
                        <th>Giocatore</th>
                        <th>Livello</th>
                        <th>Punteggio</th>
                    </tr>
                </thead>
                <tbody>
                    <?php
                    if ($result->num_rows > 0) {
                        $rank = 1;
                        // Ciclo su ogni riga trovata dal database
                        while($row = $result->fetch_assoc()) {
                            echo "<tr>";
                            
                            // Colonna Posizione (1°, 2°, 3°...)
                            echo "<td>#" . $rank . "</td>";
                            
                            // Colonna Nome
                            echo "<td>" . htmlspecialchars($row['nome']) . " " . htmlspecialchars($row['cognome'][0]) . ".</td>";
                            
                            // Colonna Livello
                            echo "<td>" . htmlspecialchars($row['livello']) . "</td>";
                            
                            // Colonna Punteggio
                            echo "<td><span class='score-badge'>" . $row['punteggio'] . "</span></td>";
                            
                            echo "</tr>";
                            $rank++;
                        }
                    } else {
                        echo "<tr><td colspan='4'>Ancora nessun giocatore in classifica!</td></tr>";
                    }
                    ?>
                </tbody>
            </table>
        </div>
    </main>

</body>
</html>