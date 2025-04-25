<?php
session_start();
require "cbdd.php";

// Est-ce une reprise de connexion ?
if (isset($_COOKIE['biathlon_token'])) {
    // le juge s'est déjà connecté

    // lire l'état dans la base
    $state = $db = getState();

    // donner la page correspondante


} // si token



?>


<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Biathlon : Juge</title>
    <link rel="stylesheet" href="style.css">

    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>

    <script>
        function checkStatus() {
            $.ajax({
                url: 'check_state.php',
                method: 'GET',
                success: function(response) {
                    if (response.trim() === '1') {
                        $('#status').text("Autorisation de l'arbitre");
                        clearInterval(interval);
                        window.location.href = 'juge2.php';
                    }
                },
                error: function() {
                    console.error('Erreur lors de la récupération des données.');
                }
            });
        }

        let interval = setInterval(checkStatus, 3000); // Vérifie toutes les secondes
    </script>
</head>
<body>
    <header>
        JUGE
    </header>
     <div id="status">En attente de l'arbitre</div>
     <footer>
        © 2025 - Biathlon Supervision System
    </footer>
</body>
</html>
