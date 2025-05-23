<?php
require '../cbdd.php';

$etat = $db->getState();
if ($etat == 3) {
    require '../ccsv.php';
    $result = $db->getParamsCourse();
    $conf = $result->fetch(PDO::FETCH_ASSOC);
    $dist2T = $conf['dist2T'];
    $distPen = $conf['distPen'];

    // Requête pour récupérer les colonnes nécessaires
    $donnees = $db->getRace();
    foreach ($donnees as $row) {
        echo "<tr>";
        echo "<td>{$row['num']}</td>";
        echo "<td>{$row['runnerName']}</td>";
        $j=0;$i=0;
        $mem = 0;
        for ($i = 1; $i <= $row['judgeState']; $i++) {
            $val = $csv->format_duree($row["t$i"]);
            //$dval = format_duree($mem);  // delta t
            $note = null;

            // Ajouter les tirs réussis si on est sur t2, t5 ou t8
            unset($seq);
            if (in_array($i, [2, 5, 8])) {
                $j++;
                $seqTirs = "seqTirs{$j}";
                $seq = isset($row[$seqTirs]) ? $row[$seqTirs] : null;
                echo "<td>".htmlspecialchars($val);
            } // if
            if (in_array($i, [1, 4, 7])) {
                    $dist = $conf['dist2T'];
                    echo "<td>".htmlspecialchars($val)."<br><span class='sub-value'>(".number_format($csv->calculerVitesse($dist,$mem,$row["t$i"]), 2, ',', ' ')." km/h)</span>";
            } // if
            if (in_array($i, [3, 6, 9])) {
                    $dist = $conf['distPen'];
                    echo "<td>".htmlspecialchars($val)."<br><span class='sub-value'>(".number_format($csv->calculerVitesse($dist,$mem,$row["t$i"]), 2, ',', ' ')." km/h)</span>";
            } // if
            $deltaT = $row["t$i"]-$mem;
            echo "<br><span class='sub-value'>(&Delta;t=" . htmlspecialchars($csv->format_duree($deltaT)) . ")</span>";
            $mem = $row["t$i"];
            if (isset($seq)) {
                echo "<br><span class='sub-value'>" . htmlspecialchars($seq) . "</span>";
            }
            echo "</td>";
        } // for
        if ($i == 10)
            echo "<td  class='bold'>".htmlspecialchars($csv->format_duree($row['totalTime']))."</td>";
        echo "</tr>";
    } // foreach
} else {
    echo "<td class='bold' colspan='12'>Pas de course en cours !</td>";
}// else etat
?>
