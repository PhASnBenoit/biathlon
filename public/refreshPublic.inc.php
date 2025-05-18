<?php
require '../cbdd.php';

function format_duree($nanoseconds) {
    // Convertir en secondes
    $totalSeconds = $nanoseconds / 1e9;

    // Extraire minutes, secondes et centièmes
    $minutes = floor($totalSeconds / 60);
    $seconds = floor($totalSeconds % 60);
    $centièmes = floor(($totalSeconds - floor($totalSeconds)) * 100);

    // Formater avec deux chiffres pour secondes et centièmes
    return sprintf("%02d:%02d:%02d", $minutes, $seconds, $centièmes);
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
