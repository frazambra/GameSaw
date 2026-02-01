document.addEventListener('DOMContentLoaded', () => {


    // Riferimenti agli elementi del DOM
    const loginBtn = document.getElementById('btn-login');
    const registerBtn = document.getElementById('btn-register');
    const playBtn = document.getElementById('btn-play');
    
    const nicknameInput = document.getElementById('nickname-input');
    const userDisplay = document.getElementById('user-display');
    const displayNameSpan = document.getElementById('display-name');

   // Gestione click sul bottone LOGIN
    if (loginBtn) {
        loginBtn.addEventListener('click', () => {
            const loginLink = loginBtn.closest('a');
            const href = loginLink ? loginLink.getAttribute('href') : null;

            window.location.href = href || 'autenticazione/login.php';
        });
    }

    // Gestione click sul bottone REGISTRATI
    if (registerBtn) {
        registerBtn.addEventListener('click', () => {
            const registerLink = registerBtn.closest('a');
            const href = registerLink ? registerLink.getAttribute('href') : null;

            window.location.href = href || 'autenticazione/registrazione.html';
        });
    }

    // Event Listener sul bottone Gioca
    if (playBtn) playBtn.addEventListener('click', () => {
        let currentPlayer = "";


        // Se l'input è visibile, prendiamo il nome da lì
        if (nicknameInput && !nicknameInput.classList.contains('hidden')) {
            currentPlayer = nicknameInput.value;
            if (currentPlayer.trim() === "") {
                alert("Per favore, inserisci un nickname prima di giocare!");
                return;
            }
        } else {
            // Altrimenti prendiamo il nome dal profilo loggato
            currentPlayer = displayNameSpan ? displayNameSpan.textContent : "";
        }


        // Avvio del gioco (Qui andrà la logica del gioco vero e proprio)
        alert(`Inizio partita per: ${currentPlayer}! Buon divertimento con GameSAW.`);
        // window.location.href = "gioco.html"; // Esempio di redirect
    });
});