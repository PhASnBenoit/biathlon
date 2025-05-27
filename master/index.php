<?php
// index.php master
/*
etat(bdd)       cookie du term      cookie master bd        conn/reconn     info
    -1                  X                   X                   OUI         Condition normale de démarrage d'une course
    0-3                 A                   A                   OUI         Reconnexion du master
    0-3                 A                   B                   NON         Tentative d'un autre master
    0-3             Existe pas              B                   NON         ancienne course non terminée ou tentative

TODO A la mise en route, mettre un script qui place state à -1 dans la BDD
*/
session_start();
require '../cbdd.php';
require '../cpage.php';
$titre = 'BIATHLON AUTHENTIFICATION MASTER v2.2 by PhA (2025)';
$foot = 'Biathlon Supervision System';

$state = $db->getState();

// Cas où un cookie est présent
if (!empty($_COOKIE['biathlon_master_token'])) {
    $token = $_COOKIE['biathlon_master_token'];
    $tokenBdd = $db->getTokenmaster();
    if ($token === $tokenBdd) {
        $_SESSION['state'] = $state;
        switch ($_SESSION['state']) {
            case 0: // mot de passe entrée
            case 1: // course paramétrée
                header("Location: master1.php");
                exit();
            case 2: // juges connectés, attente du go
                header("Location: master2.php");
                exit();
            case 3: // course en cours
                header("Location: master3.php");
                exit();
                //  -1 on continue
        } // sw
    } // if token
} // if cookie

if ($state != -1) {
    echo "Un juge est déjà connecté !<br>";
    echo "Si ce n'est pas le cas, vous pouvez réinitialiser la course.<br>";
    echo "Pour réinitialiser la course :<br>";
    // Bouton qui après authentification raz la course
    echo "<form id='fraz' action='secours.php' method='POST'>";
    echo "<label for='codeInput'>Entrez un code à 6 chiffres :</label>";
    echo "<input type='password' name='codeRaz' id='codeRaz' maxlength='6' required>";
    echo "<button type='submit'>RAZ</button>";
    echo "</form>";
    exit();
} // if state

// Cas où le master entre un code
if ($_SERVER['REQUEST_METHOD'] === 'POST' && !empty($_POST['code'])) {
    $code = $_POST['code'];
    $hashedBdd = $db->getCodeMaster();
    if (password_verify($code, $hashedBdd)) {
        // Supprime le cookie en le réécrivant avec une date expirée
        setcookie('biathlon_master_token', '', time() - 3600, '/');
        setcookie('biathlon_juge_token', '', time() - 3600, '/');
        session_unset();       // efface toutes les variables
        session_destroy();     // détruit la session côté serveur
        session_start();       // redémarre une nouvelle session propre
        $_SESSION['id'] = $code;
        $_SESSION['state'] = 0;
        $db->setState(0);
        $db->setNbJuges(0);
        $db->viderTableRace();
        // Création et stockage du token
        $token = bin2hex(random_bytes(16));
        setcookie('biathlon_master_token', $token, time() + 3600, "/");
        $db->setTokenmaster($token);
        $_SESSION['tokenmaster'] = $token;
        header("Location:master1.php");
    } else {
        echo "<br>master : Code invalide.";
        exit();
    }
}

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['purger'])) {
    require '../ccsv.php';
    $csv->purgerCsv();
} // if

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

<?php
    $repertoire = __DIR__ . '/../res';  // dossier où sont stockés les fichiers CSV
    $baseUrl = '/biathlon/res/';               // chemin relatif depuis le navigateur
    $fichiers = glob($repertoire . '/*.csv');
    echo "<h2>Fichiers CSV disponibles</h2>";
    echo "<ul>";
    foreach ($fichiers as $cheminComplet) {
        $nomFichier = basename($cheminComplet);
        echo '<a href="' . $baseUrl . $nomFichier . '" target="_blank">' . $nomFichier . '</a><br>';
    } // foreach
    echo "</ul>";
    echo "<form method='post'><button type='submit' name='purger'>Purger les anciens fichiers (>1an)</button></form>";
?>

</main>

<?php $page->footer($foot);?>
