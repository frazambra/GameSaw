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
            // Reindirizza alla pagina login.html
            window.location.href = 'login.php';
        });
    }

    // Gestione click sul bottone REGISTRATI
    if (registerBtn) {
        registerBtn.addEventListener('click', () => {
            // Reindirizza alla pagina registrazione.html
            window.location.href = 'registrazione.html';
        });
    }

    // Event Listener sul bottone Gioca
    playBtn.addEventListener('click', () => {
        let currentPlayer = "";

        // Se l'input è visibile, prendiamo il nome da lì
        if (!nicknameInput.classList.contains('hidden')) {
            currentPlayer = nicknameInput.value;
            if (currentPlayer.trim() === "") {
                alert("Per favore, inserisci un nickname prima di giocare!");
                return;
            }
        } else {
            // Altrimenti prendiamo il nome dal profilo loggato
            currentPlayer = displayNameSpan.textContent;
        }

        // Avvio del gioco (Qui andrà la logica del gioco vero e proprio)
        alert(`Inizio partita per: ${currentPlayer}! Buon divertimento con GameSAW.`);
        // window.location.href = "gioco.html"; // Esempio di redirect
    });
});