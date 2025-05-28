 <?php
session_start();
require 'cbdd.php';

    $state = $db->getState();
    $_SESSION['state'] = $state;
    if ($state == -1) { // pour les juges seulement
        setcookie('biathlon_juge_token', '', time() - 3600, '/');
        session_unset();       // efface toutes les variables
        session_destroy();     // détruit la session côté serveur
    } // if
    echo $state;
    /* states
     * 0 : Le master seul peut se connecter. Il s'est authentifié
     * 1 : Le master a paramétré la course. Seuls les juges peuvent se connecter
     * 2 : Juges connectés,  le public peut se connecter
     * 3 : le master a commencé la course.
     */
?>
