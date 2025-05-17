<?php
// index.php (juge)

?>
<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>BIATHLON AUTORISATION JUGE</title>
    <link rel="stylesheet" href="/biathlon/style.css">

    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
    <script>
        function checkStatus() {
            $.ajax({
                url: '/biathlon/check_state.php',
                method: 'GET',
                success: function(response) {
                    if (response.trim() == 3) {
                        $('#status').text("Course en cours...");
                        clearInterval(interval);
                        window.location.href = 'juge3.php';
                    }

                    if (response.trim()==1 || response.trim()==2) {
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
        let interval = setInterval(checkStatus, 1000); // Vérifie toutes les secondes
    </script>
</head>
<body>
    <header>
        BIATHLON AUTHORISATION DU JUGE
    </header>
     <div id="status">Attente autorisation de l'arbitre</div>
     <footer>
        © 2025 - Biathlon Supervision System
    </footer>
</body>
</html>
