<?php
    session_start();
    require 'cbdd.php';
    $db->setState(0);
    $db->setNbJuges(0);
    $db->viderTableRace();
    header("Location: logout.php");
?>
