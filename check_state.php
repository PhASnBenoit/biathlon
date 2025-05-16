 <?php
session_start();
require 'cbdd.php';

    $state = $db->getState();
    $_SESSION['state'] = $state;
    echo $state;

    /* states
     * 0 : L'arbitre seul peut se connecter. Il s'est authentifié
     * 1 : Arbitre a paramétré la course. Seuls les juges peuvent se connecter
     * 2 : Juges connectés,  le public peut se connecter
     * 3 : l'arbitre a commencé la course.
     */
?>
