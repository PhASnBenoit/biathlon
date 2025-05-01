<?php
require 'cbdd.php';

function format_duree($secondes) {
    $minutes = floor($secondes / 60);
    $restant = $secondes - ($minutes * 60);
    $secondes_int = floor($restant);
    $centiemes = floor(($restant - $secondes_int) * 100);

    return sprintf('%02d:%02d:%02d', $minutes, $secondes_int, $centiemes);
}

// Requête pour récupérer les colonnes nécessaires
$stmt = $db->getRace();

while ($row = $stmt->fetch(PDO::FETCH_ASSOC)) {
    echo "<tr>";
    echo "<td>{$row['num']}</td>";
    echo "<td>{$row['runnerName']}</td>";
    $j=0;
    for ($i = 0; $i <= 9; $i++) {
        $val = format_duree($row["t$i"]);
        $note = null;

        // Ajouter la note si on est sur t2, t5 ou t8
        unset($seq);
        if (in_array($i, [2, 5, 8])) {
            $j++;
            $seqTirs = "seqTirs{$j}";
            $seq = isset($row[$seqTirs]) ? $row[$seqTirs] : null;
        }
        $is_last = ($i === 9);
        echo "<td" . ($is_last ? " class='bold'" : "") . ">";

        echo htmlspecialchars($val);
        if (isset($seq)) {
            echo "<br><span class='sub-value'>" . htmlspecialchars($seq) . "</span>";
        }

        echo "</td>";
    }

    echo "</tr>";
}
?>
