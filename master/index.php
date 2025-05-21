<?php
// index.php master
require '../cbdd.php';
require '../cpage.php';
$titre = 'BIATHLON AUTHENTIFICATION MASTER v1.6 by PhA (2025)';
$foot = 'Biathlon Supervision System';

// Cas où l'master entre un code
if ($_SERVER['REQUEST_METHOD'] === 'POST' && !empty($_POST['code'])) {
    $code = $_POST['code'];

    if ($db->isGoodCode($code)) {
        session_start();
        $_SESSION = array();  // détruit le tableau session
        $_SESSION['id'] = $code;
        $_SESSION['state'] = 0;
        $db->setState(0);
        $db->setNbJuges(0);
        $db->viderTableRace();

        // effacer les cookies de tous les profils
        foreach ($_COOKIE as $name => $value) {
            // Supprime le cookie en le réécrivant avec une date expirée
            setcookie($name, '', time() - 3600, '/');
        } // for
        // Création et stockage du token
        $token = bin2hex(random_bytes(16));
        setcookie('biathlon_master_token', $token, time() + 3600, "/");
        $db->setTokenmaster($token);
        $_SESSION['tokenmaster'] = $token;
        header("Location: master1.php");
        exit();
    } else {
        echo "<br>master : Code invalide.";
        exit();
    }
}

// Cas où un cookie est présent
if (!empty($_COOKIE['biathlon_master_token'])) {
    $token = $_COOKIE['biathlon_master_token'];
    $tokenBdd = $db->getTokenmaster();

    if ($token === $tokenBdd) {
        session_start();
        $_SESSION['state'] = $db->getState();

        switch ($_SESSION['state']) {
            case 0: // mot de passe entrée
            case 1: // course paramétrée
                header("Location: master1.php");
                break;
            case 2:
                header("Location: master2.php");
                break;
            case 3:
                header("Location: master3.php");
                break;
        } // sw
        echo "Erreur !";
        exit();
    } // if token
} // if cookie

$page->entete($titre);
$page->finHeadBody();
$page->header($titre);
?>

<main>
    <h1>Vérification du Code</h1>
    <form action="#" method="POST" class="form-container">
        <label for="codeInput">Entrez un code à 6 chiffres :</label>
        <input type="password" name="code" id="codeInput" maxlength="6" required>
        <button type="submit">Commencer</button>
    </form>
</main>

<?php $page->footer($foot);?>
