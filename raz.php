<?php
    session_start();
    require 'cbdd.php';
    $db->setState(0);
    $db->setNbJuges(0);
    // TODO Effacer les enr de la table race
    header("Location: logout.php");
?>
