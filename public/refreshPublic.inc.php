<?php
require '../cbdd.php';

function format_duree($secondes) {
    $minutes = floor($secondes / 60);
    $restant = $secondes - ($minutes * 60);
    $secondes_int = floor($restant);
    $centiemes = floor(($restant - $secondes_int) * 100);
    return sprintf('%02d:%02d:%02d', $minutes, $secondes_int, $centiemes);
}

$etat = $db->getState();
if ($etat == 3) {
    // Requête pour récupérer les colonnes nécessaires
    $stmt = $db->getRace();

    while ($row = $stmt->fetch(PDO::FETCH_ASSOC)) {
        echo "<tr>";
        echo "<td>{$row['num']}</td>";
        echo "<td>{$row['runnerName']}</td>";
        $j=0;
        for ($i = 1; $i <= 9; $i++) {
            $val = format_duree($row["t$i"]);
            $note = null;

            // Ajouter la note si on est sur t2, t5 ou t8
            unset($seq);
            if (in_array($i, [2, 5, 8])) {
                $j++;
                $seqTirs = "seqTirs{$j}";
                $seq = isset($row[$seqTirs]) ? $row[$seqTirs] : null;
            }
            echo "<td>".htmlspecialchars($val);
            if (isset($seq)) {
                echo "<br><span class='sub-value'>" . htmlspecialchars($seq) . "</span>";
            }
            echo "</td>";
        } // for
        echo "<td  class='bold'>".htmlspecialchars(format_duree($row['totalTime']))."</td>";
        echo "</tr>";
    } // wh
} else {
    echo "<td class='bold' colspan='12'>Pas de course en cours !</td>";
}// else etat
?>
