document.addEventListener('DOMContentLoaded', () => {
    // Riferimenti agli elementi del DOM
    const loginBtn = document.getElementById('btn-login');
    const registerBtn = document.getElementById('btn-register');
    const playBtn = document.getElementById('btn-play');
    
    const nicknameInput = document.getElementById('nickname-input');
    const userDisplay = document.getElementById('user-display');
    const displayNameSpan = document.getElementById('display-name');

    // Funzione simulata per gestire il Login/Registrazione
    function simulateUserLogin() {
        // Simuliamo che l'utente inserisca i dati in un prompt per questo esempio
        const user = prompt("Simulazione: Inserisci il tuo nome utente per il login:");
        
        if (user && user.trim() !== "") {
            // Nascondi l'input
            nicknameInput.classList.add('hidden');
            
            // Imposta il nome nello span
            displayNameSpan.textContent = user;
            
            // Mostra il messaggio di benvenuto
            userDisplay.classList.remove('hidden');
            
            // Opzionale: Cambia i bottoni in alto in "Logout"
            loginBtn.textContent = "Logout";
            registerBtn.style.display = "none";
        }
    }

    // Event Listeners sui bottoni Auth
    loginBtn.addEventListener('click', () => {
        if (loginBtn.textContent === "Logout") {
            // Reset simulato
            window.location.reload();
        } else {
            simulateUserLogin();
        }
    });

    registerBtn.addEventListener('click', () => {
        simulateUserLogin();
    });

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