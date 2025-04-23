<?php
require 'cbdd.php';

    $stmt = $db->getState();
    $row = $stmt->fetch(PDO::FETCH_ASSOC);
    echo $row ? $row['state'] : '0';

    /* states
     * 0 : L'arbitre seul peut se connecter
     * 1 : Arbitre a paramétré la course. Seuls les juges peuvent se connecter
     * 2 : Juges connectés,  le public peut se connecter
     * 3 : l'arbitre a commencé la course.
     */
?>
