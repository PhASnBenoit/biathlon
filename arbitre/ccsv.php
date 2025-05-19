<?php
// CCsv.php
class CCsv {

    private function format_duree($nanoseconds) {
        // Convertir en secondes
        $totalSeconds = $nanoseconds / 1e9;

        // Extraire minutes, secondes et centièmes
        $minutes = floor($totalSeconds / 60);
        $seconds = floor($totalSeconds % 60);
        $centièmes = floor(($totalSeconds - floor($totalSeconds)) * 100);

        // Formater avec deux chiffres pour secondes et centièmes
        return sprintf("%02d:%02d:%02d", $minutes, $seconds, $centièmes);
    } // format_duree

    public function composeFile($raceName, $judgeCount, $race) {
        // Nom du fichier avec date/heure
        $date = date("Y-m-d H:i:s");
        $filename = "/srv/www/htdocs/biathlon/res/biathlon_" . date("Y-m-d_H-i-s") . ".csv";

        // Création du fichier
        $fichier = fopen($filename, 'w');
        if ($fichier === false) {
            die("Impossible de créer le fichier CSV.");
        }

        // Écriture de l'en-tête générale
        fputcsv($fichier, ["Course BIATHLON (@ STS CIEL Lycée BENOIT 2025 by PhA)"]);
        fputcsv($fichier, ["Date", $date]);
        fputcsv($fichier, ["Nom de la course", $raceName]);
        fputcsv($fichier, ["Nombre de coureurs", $judgeCount]);
        fputcsv($fichier, []); // ligne vide

        // Écriture de la ligne d'en-tête des colonnes de données
        fputcsv($fichier, [
            "Position", "Nom du coureur", "Nom de l’arbitre",
            "2T1", "ST1", "Tirs Réussis 1",
            "2T2", "ST2", "Tirs Réussis 2",
            "2T3", "ST3", "Tirs Réussis 3",
            "Temps total"
        ]);

        // Écriture des données pour chaque coureur
        foreach ($race as $coureur) {
            fputcsv($fichier, [
                $coureur['num'], $coureur['runnerName'], $coureur['judgeName'],
                format_duree($coureur['t1']), format_duree($coureur['t2']), format_duree($coureur['seqTirs1']), format_duree($coureur['t3']),
                format_duree($coureur['t4']), format_duree($coureur['t5']), format_duree($coureur['seqTirs2']), format_duree($coureur['t6']),
                format_duree($coureur['t7']), format_duree($coureur['t8']), format_duree($coureur['seqTirs3']), format_duree($coureur['t9']),
                $coureur['totalTime']
            ]);
        } // foreach
        fclose($fichier);
    } // composeFile
} // CCsv
?>
