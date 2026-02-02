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
            window.location.href = 'autenticazione/login.php';
        });
    }

    // Gestione click sul bottone REGISTRATI
    if (registerBtn) {
        registerBtn.addEventListener('click', () => {
            // Reindirizza alla pagina registrazione.html
            window.location.href = 'autenticazione/registrazione.html';
        });
    }

    // Event Listener sul bottone Gioca
    if (playBtn) playBtn.addEventListener('click', () => {
    
        window.location.href = `../Gioco/play.php`;
    
    });
});