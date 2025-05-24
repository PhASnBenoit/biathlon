<?php
//
//
//
session_start();
// Vérifie que l'utilisateur est connecté
//var_dump($_SESSION);

if (!isset($_SESSION['id'])) {
    exit();
    header('Location: /master/');
}

require '../cbdd.php';
require '../cpage.php';

$success = "";
$error = "";

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $old_pass = $_POST['old_password'] ?? '';
    $new_pass = $_POST['new_password'] ?? '';
    $confirm_pass = $_POST['confirm_password'] ?? '';

    if ($new_pass !== $confirm_pass) {
        $error = "Les nouveaux mots de passe ne correspondent pas.";
    } else {
        // Récupération du mot de passe actuel
        $user = $db->getCodeMaster();

        if ($user && password_verify($old_pass, $user)) {
            // Mise à jour du mot de passe
            $hashed = password_hash($new_pass, PASSWORD_DEFAULT);
            $db->setCodeMaster($hashed);
            $success = "Mot de passe mis à jour avec succès.";
            header("Location:/master/");
        } else {
            $error = "Ancien mot de passe incorrect.";
        }
    }
}
/*
<style>
        body { font-family: Arial, sans-serif; padding: 2em; background: #f0f0f0; }
        form { background: white; padding: 2em; max-width: 400px; margin: auto; border-radius: 8px; box-shadow: 0 2px 10px rgba(0,0,0,0.1); }
        input[type="password"], input[type="submit"] {
            width: 100%; padding: 10px; margin-bottom: 1em; border: 1px solid #ccc; border-radius: 5px;
        }
        .message { text-align: center; margin-top: 1em; }
        .success { color: green; }
        .error { color: red; }
    </style>
*/
$titre = "BIATHLON MODIFIER LE CODE MASTER";
$foot = 'Biathlon Supervision System';
$page->entete($titre);
$page->finHeadBody();
$page->header($titre);
?>
<main>
<form method="post" action="">
    <label>Ancien mot de passe :</label>
    <input type="password" name="old_password" required>

    <label>Nouveau mot de passe :</label>
    <input type="password" name="new_password" required>

    <label>Confirmer le nouveau mot de passe :</label>
    <input type="password" name="confirm_password" required>

    <input type="submit" value="Mettre à jour">
</form>

<div class="message">
    <?php if ($success): ?>
        <p class="success"><?= htmlspecialchars($success) ?></p>
    <?php elseif ($error): ?>
        <p class="error"><?= htmlspecialchars($error) ?></p>
    <?php endif; ?>
</div>
</main>

<?php $page->footer($foot);?>
