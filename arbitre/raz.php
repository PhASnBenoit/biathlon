<?php
    require '../cbdd.php';
    session_start();
    $db->setState(0);
//    $db->setNbJuges(0);
    $db->viderTableRace();
    $db->setTokenArbitre("0");
    $db->setTokenJuges("0");
    header("Location: logout.php");
?>
