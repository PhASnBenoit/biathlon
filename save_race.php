<?php
require 'cbdd.php';

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['raceName'])) {
    $raceName = $_POST['raceName'];
    $judgeCount = $_POST['judgeCount'];
    $res = $db->saveRace($raceName, $judgeCount);
    $db->setState(1);
    echo "Course sauvegardée. En attente des juges...";
} // if

?>
