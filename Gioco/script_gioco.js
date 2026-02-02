document.addEventListener('DOMContentLoaded', () => {
    
    // Riferimenti agli elementi HTML
    const startBtn = document.getElementById('btn-start-game');
    const arena = document.getElementById('aim-arena');
    const scoreEl = document.getElementById('score');
    const timeEl = document.getElementById('time');
    const overlay = document.getElementById('overlay-screen');

    let score = 0;
    let timeLeft = 60;
    let gameInterval;
    let gameActive = false;

    // Se il bottone esiste, attacchiamo l'evento
    if (startBtn) {
        startBtn.addEventListener('click', startGame);
    }

    function startGame() {
        score = 0;
        timeLeft = 30; // Durata partita
        gameActive = true;
        
        // Reset UI
        scoreEl.textContent = score;
        timeEl.textContent = timeLeft;
        overlay.style.display = 'none'; // Nascondi schermata start

        // Rimuovi eventuali target residui
        document.querySelectorAll('.target').forEach(t => t.remove());
        
        // Crea il primo bersaglio
        spawnTarget();

        // Avvia il timer
        gameInterval = setInterval(() => {
            timeLeft--;
            timeEl.textContent = timeLeft;

            if (timeLeft <= 0) {
                endGame();
            }
        }, 1000);
    }

    function spawnTarget() {
        if (!gameActive) return;

        const target = document.createElement('div');
        target.classList.add('target');

        // Calcola posizione randomica (sottraendo la dimensione del target per non uscire)
        const maxX = arena.clientWidth - 50;
        const maxY = arena.clientHeight - 50;
        
        const randomX = Math.floor(Math.random() * maxX);
        const randomY = Math.floor(Math.random() * maxY);

        target.style.left = `${randomX}px`;
        target.style.top = `${randomY}px`;

        // Evento click sul bersaglio
        target.onmousedown = function(e) {
            e.stopPropagation(); // Evita problemi di sovrapposizione
            score++;
            scoreEl.textContent = score;
            this.remove(); // Rimuove il bersaglio colpito
            spawnTarget(); // Ne crea subito uno nuovo
        };

        arena.appendChild(target);
    }

    function endGame() {
        gameActive = false;
        clearInterval(gameInterval);
        
        // Mostra schermata Game Over
        overlay.innerHTML = `
            <h2>Game Over!</h2>
            <p>Punteggio finale: <strong style="color: white;">${score}</strong></p>
            <button id="btn-restart" class="btn-play-big">GIOCA ANCORA</button>
            <br><br>
            <a href="../home/Home.php" style="color: #ccc;">Torna alla Home</a>
        `;
        overlay.style.display = 'flex';

        // Riattiva il bottone per rigiocare
        document.getElementById('btn-restart').addEventListener('click', startGame);

        // Se l'utente è loggato (variabile definita in PHP), salviamo il punteggio
        if (typeof USER_IS_LOGGED !== 'undefined' && USER_IS_LOGGED === true) {
            saveScore(score);
        }
    }

    // Funzione asincrona per salvare i dati nel DB
    async function saveScore(finalScore) {
        try {
            // Nota il percorso "../api/..." per uscire dalla cartella game ed entrare in api
            const response = await fetch('salva_punteggio.php', {
                method: 'POST',
                headers: { 'Content-Type': 'application/json' },
                body: JSON.stringify({ punteggio: finalScore })
            });

            const result = await response.json();
            console.log("Salvataggio:", result.message);
            
        } catch (error) {
            console.error("Errore connessione:", error);
        }
    }
});