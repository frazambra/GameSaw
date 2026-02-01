<?php
// api/register.php

// 1. Includiamo la connessione
require_once '../connessione_db.php';

// 2. Verifichiamo il metodo POST
if ($_SERVER["REQUEST_METHOD"] == "POST") {

    // Raccogliamo SOLO i dati obbligatori
    $nome = $_POST['nome'];
    $cognome = $_POST['cognome'];
    $email = $_POST['email'];
    $password = $_POST['password'];
    $confirm_password = $_POST['password_confirm'];

    // 3. Controllo Password
    if ($password !== $confirm_password) {
        die("Errore: Le password non coincidono. <a href='registrazione.html'>Torna indietro</a>");
    }

    // 4. Controllo Email esistente (Prepared Statement)
    $sql_check = "SELECT id FROM utenti WHERE email = ?";
    $stmt = $conn->prepare($sql_check);
    $stmt->bind_param("s", $email);
    $stmt->execute();
    $stmt->store_result();

    if ($stmt->num_rows > 0) {
        die("Errore: Email già registrata. <a href='login.php'>Vai al Login</a>");
    }
    $stmt->close();

    // 5. Cifratura Password
    $hashed_password = password_hash($password, PASSWORD_DEFAULT);

    // 6. Inserimento nel DB (Query più corta!)
    // Inseriamo solo i 4 campi fondamentali. Il resto (id, livello, data) lo fa MySQL in automatico.
    $sql_insert = "INSERT INTO utenti (nome, cognome, email, password) VALUES (?, ?, ?, ?)";
    
    $stmt = $conn->prepare($sql_insert);
    
    // "ssss" indica 4 stringhe: nome, cognome, email, hash password
    $stmt->bind_param("ssss", $nome, $cognome, $email, $hashed_password);

    if ($stmt->execute()) {
        // SUCCESSO
        // Rimandiamo al Login con un messaggio di conferma
        header("Location: login.php?registered=1");
        exit();
    } else {
        echo "Errore durante la registrazione: " . $stmt->error;
    }

    $stmt->close();
    $conn->close();

} else {
    // Accesso diretto bloccato
    header("Location: registrazione.html");
    exit();
}
?>