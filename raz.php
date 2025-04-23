<?php
    session_start();
    require 'cbdd.php';
    $db->setState(0);
    header("Location: logout.php");
?>
