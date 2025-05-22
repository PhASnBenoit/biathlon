<?php
    require '../cbdd.php';
    session_start();
    $db->setState(-1);
    $db->viderTableRace();
    $db->setTokenmaster("0");
    $db->setTokenJuges("0");
    header("Location: logout.php");
?>
