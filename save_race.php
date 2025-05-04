<?php
session_start();
require 'cbdd.php';

//if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['raceName'])) {
    $raceName = $_POST['raceName'];
    $judgeCount = $_POST['judgeCount'];
    $res = $db->saveParamsRace($raceName, $judgeCount);
    $db->setState(1);
    $_SESSION['state'] = 1;
    $_SESSION['raceName'] = $raceName;
    $_SESSION['judgeCount'] = $judgeCount;
    echo "Course sauvegardée.";
//} // if

?>
