<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Vérification du Code</title>
    <link rel="stylesheet" href="style.css">
</head>
<body>

    <header>
        BIATHLON ESPACE ARBITRE
    </header>

    <main>
        <h1>Vérification du Code</h1>
        <form action="verif_code.php" method="POST" class="form-container">
            <label for="codeInput">Entrez un code à 6 chiffres :</label>
            <input type="password" name="code" id="codeInput" maxlength="6" required>
            <button type="submit">Commencer</button>
        </form>
    </main>

    <footer>
        © 2025 - Biathlon Verification System
    </footer>

</body>
</html>
