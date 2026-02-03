document.addEventListener('DOMContentLoaded', () => {
    
    const startBtn = document.getElementById('btn-start-game');
    const arena = document.getElementById('aim-arena');
    const scoreEl = document.getElementById('score');
    const timeEl = document.getElementById('time');
    const overlay = document.getElementById('overlay-screen');
    const diffButtons = document.querySelectorAll('.btn-diff');

    let score = 0;
    let timeLeft = 30;
    let gameInterval;
    let gameActive = false;
    
    let currentTargetSize = 50; 

    // GESTIONE DIFFICOLTÀ
    diffButtons.forEach(btn => {
        btn.addEventListener('click', () => {
            if(gameActive) return;

            diffButtons.forEach(b => b.classList.remove('active'));
            btn.classList.add('active');

            currentTargetSize = parseInt(btn.getAttribute('data-size'));
        });
    });

    // START GAME
    if (startBtn) {
        startBtn.addEventListener('click', startGame);
    }

    function startGame() {
        score = 0;
        timeLeft = 30; 
        gameActive = true;
        
        scoreEl.textContent = score;
        timeEl.textContent = timeLeft;
        overlay.style.display = 'none'; 

        document.querySelectorAll('.target').forEach(t => t.remove());
        
        spawnTarget();

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

        target.style.width = `${currentTargetSize}px`;
        target.style.height = `${currentTargetSize}px`;

        const maxX = arena.clientWidth - currentTargetSize;
        const maxY = arena.clientHeight - currentTargetSize;
        
        const randomX = Math.floor(Math.random() * maxX);
        const randomY = Math.floor(Math.random() * maxY);

        target.style.left = `${randomX}px`;
        target.style.top = `${randomY}px`;

        target.onmousedown = function(e) {
            e.stopPropagation(); 
            score++;
            scoreEl.textContent = score;
            this.remove(); 
            spawnTarget(); 
        };

        arena.appendChild(target);
    }

    function endGame() {
        gameActive = false;
        clearInterval(gameInterval);
        
        // MODIFICA QUI: Label cambiata
        let diffName = 'Normale';
        if(currentTargetSize === 80) diffName = 'Facile';
        if(currentTargetSize === 25) diffName = 'Difficile';

        overlay.innerHTML = `
            <h2>Game Over!</h2>
            <p>Difficoltà: <strong>${diffName}</strong></p>
            <p>Punteggio finale: <strong style="color: white;">${score}</strong></p>
            <button id="btn-restart" class="btn-play-big">GIOCA ANCORA</button>
            <br><br>
            <a href="../home/Home.php" style="color: #ccc;">Torna alla Home</a>
        `;
        overlay.style.display = 'flex';

        document.getElementById('btn-restart').addEventListener('click', startGame);

        if (typeof USER_IS_LOGGED !== 'undefined' && USER_IS_LOGGED === true) {
            saveScore(score, currentTargetSize);
        }
    }

    async function saveScore(finalScore, size) {
        // MODIFICA QUI: Slug cambiato
        let difficoltaSlug = 'normale';
        if(size === 80) difficoltaSlug = 'facile';
        if(size === 25) difficoltaSlug = 'difficile';

        try {
            const response = await fetch('salva_punteggio.php', {
                method: 'POST',
                headers: { 'Content-Type': 'application/json' },
                body: JSON.stringify({ 
                    punteggio: finalScore,
                    difficolta: difficoltaSlug 
                })
            });

            const result = await response.json();
            console.log("Salvataggio:", result.message);
            
        } catch (error) {
            console.error("Errore connessione:", error);
        }
    }
});