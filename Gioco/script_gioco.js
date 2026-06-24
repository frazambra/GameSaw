document.addEventListener('DOMContentLoaded', () => {
    
    const startBtn = document.getElementById('btn-start-game');
    const arena = document.getElementById('aim-arena');
    const scoreEl = document.getElementById('score');
    const timeEl = document.getElementById('time');
    const overlay = document.getElementById('overlay-screen');
    const diffButtons = document.querySelectorAll('.btn-diff');

    let score = 0;
    let starsCollected = 0; // Contatore stelle sessione corrente
    let timeLeft = 30;
    let gameInterval;
    let gameActive = false;
    
    // Default size (Normale)
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

    if (startBtn) startBtn.addEventListener('click', startGame);

    function startGame() {
        score = 0;
        starsCollected = 0;
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
            if (timeLeft <= 0) endGame();
        }, 1000);
    }

    function spawnTarget() {
        if (!gameActive) return;

        const target = document.createElement('div');
        
        // 10% di possibilità che esca una STELLA invece di un cerchio
        const isStar = Math.random() < 0.10; 

        if (isStar) {
            target.classList.add('target', 'star');
            const starSize = 30; // La stella è piccola
            target.style.width = `${starSize}px`;
            target.style.height = `${starSize}px`;
            
            // La stella scompare da sola dopo 1.2 secondi se non la prendi!
            const disappearTimer = setTimeout(() => {
                if(target.parentNode) {
                    target.remove();
                    spawnTarget(); 
                }
            }, 1200);
            
            target.onmousedown = function(e) {
                e.stopPropagation();
                clearTimeout(disappearTimer); 
                starsCollected++; // +1 Stella
                score += 5;       // +5 Punti bonus
                scoreEl.textContent = score;
                this.remove();
                spawnTarget();
            };

        } else {
            // TARGET NORMALE
            target.classList.add('target');
            target.style.width = `${currentTargetSize}px`;
            target.style.height = `${currentTargetSize}px`;

            target.onmousedown = function(e) {
                e.stopPropagation(); 
                score++;
                scoreEl.textContent = score;
                this.remove(); 
                spawnTarget(); 
            };
        }

        // Posizione Casuale (sicura per i bordi)
        const activeSize = isStar ? 30 : currentTargetSize;
        const maxX = arena.clientWidth - activeSize;
        const maxY = arena.clientHeight - activeSize;
        const randomX = Math.floor(Math.random() * maxX);
        const randomY = Math.floor(Math.random() * maxY);

        target.style.left = `${randomX}px`;
        target.style.top = `${randomY}px`;

        arena.appendChild(target);
    }

    function endGame() {
        gameActive = false;
        clearInterval(gameInterval);
        
        // Determina lo slug per il DB
        let diffName = 'Normale';
        let diffSlug = 'normale'; 
        
        if(currentTargetSize === 80) { diffName = 'Facile'; diffSlug = 'facile'; }
        if(currentTargetSize === 25) { diffName = 'Difficile'; diffSlug = 'difficile'; }

        overlay.innerHTML = `
            <h2>Game Over!</h2>
            <p>Difficoltà: <strong>${diffName}</strong></p>
            <p>Punteggio: <strong>${score}</strong></p>
            <p style="font-size: 0.9em; color: #ffd700;">Stelle Bonus: ${starsCollected}</p>
            <button id="btn-restart" class="btn-play-big">GIOCA ANCORA</button>
            <br><br>
            <a href="../home/Home.php" style="color: #ccc;">Torna alla Home</a>
        `;
        overlay.style.display = 'flex';

        document.getElementById('btn-restart').addEventListener('click', startGame);

        if (typeof USER_IS_LOGGED !== 'undefined' && USER_IS_LOGGED === true) {
            // Inviamo Punteggio, Difficoltà e Stelle al server
            saveScore(score, diffSlug, starsCollected);
        }
    }

    async function saveScore(finalScore, diffSlug, stars) {
        try {
            const response = await fetch('salva_punteggio.php', {
                method: 'POST',
                headers: { 'Content-Type': 'application/json' },
                body: JSON.stringify({ 
                    punteggio: finalScore,
                    difficolta: diffSlug,
                    stelle: stars 
                })
            });

            const result = await response.json();
            
            // Se il server ci risponde che abbiamo sbloccato badge
            if (result.badges_unlocked && result.badges_unlocked.length > 0) {
                let badgeMsg = result.badges_unlocked.join("\n- ");
                alert("🏆 NUOVI BADGE SBLOCCATI!\n\n- " + badgeMsg);
            }
            
        } catch (error) {
            console.error("Errore salvataggio:", error);
        }
    }
});