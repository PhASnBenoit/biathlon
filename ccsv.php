<?php
// CCsv.php
class CCsv {

    public function calculerVitesse($dist, $tempsDeb, $tempsFin) {
        // temps reçus en nanoseconds
        // dist en m
        // Différence en nanosecondes
        $dureeNano = $tempsFin - $tempsDeb;
        // Conversion en centièmes de seconde
        $dureeSecondes = ($tempsFin - $tempsDeb) / 1_000_000_000;
        $vitesse = ($dist / $dureeSecondes)*3.6;
        return $vitesse;
    } // calculerVitesse

    public function format_duree($nanoseconds) {
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
        $filename =  __DIR__ . "/res/biathlon_" . date("Y-m-d_H-i-s") . ".csv";
        // Création du fichier
        $fichier = fopen($filename, 'w');
        if ($fichier === false) {
            die("Impossible de créer le fichier CSV.");
        } // if fichier
        // ⚠️ BOM pour forcer Excel à reconnaître UTF-8
        fwrite($fichier, "\xEF\xBB\xBF");
        // Écriture de l'en-tête générale
        fputcsv($fichier, ["Course BIATHLON (@ STS CIEL Lycée BENOIT 2025 by PhA)"],';');
        fputcsv($fichier, ["Date", $date],';');
        fputcsv($fichier, ["Nom de la course", $raceName],';');
        fputcsv($fichier, ["Nombre de coureurs", $judgeCount],';');
        fputcsv($fichier, [],';'); // ligne vide

        // Écriture de la ligne d'en-tête des colonnes de données
        fputcsv($fichier, [
            "Position", "Coureur", "Juge",
            "2T1", "ST1", "Tirs 1", "TP1",
            "2T2", "ST2", "Tirs 2", "TP2",
            "2T3", "ST3", "Tirs 3", "TP3",
            "Temps total"
        ],';');

        // Écriture des données pour chaque coureur
        global $db;
        $result = $db->getParamsCourse();
        $row = $result->fetch(PDO::FETCH_ASSOC);
        $dist2T = $row['dist2T'];
        $distPen = $row['distPen'];
        foreach ($race as $coureur) {
            fputcsv($fichier, [
                $coureur['num'], $coureur['runnerName'], $coureur['judgeName'],
                $this->format_duree($coureur['t1'])." (".number_format($this->calculerVitesse($dist2T,0,$coureur['t1']), 2, ',', ' ')." km/h) (Δt=" . htmlspecialchars($this->format_duree($coureur['t1'])) . ")",
                $this->format_duree($coureur['t2']), $coureur['seqTirs1'],
                $this->format_duree($coureur['t3'])." (".number_format($this->calculerVitesse($distPen,$coureur['t2'],$coureur['t3']), 2, ',', ' ')." km/h) (Δt=" . htmlspecialchars($this->format_duree($coureur['t3']-$coureur['t2'])) . ")",
                $this->format_duree($coureur['t4'])." (".number_format($this->calculerVitesse($dist2T,$coureur['t3'],$coureur['t4']), 2, ',', ' ')." km/h) (Δt=" . htmlspecialchars($this->format_duree($coureur['t4']-$coureur['t3'])) . ")",
                $this->format_duree($coureur['t5']), $coureur['seqTirs2'],
                $this->format_duree($coureur['t6'])." (".number_format($this->calculerVitesse($distPen,$coureur['t5'],$coureur['t6']), 2, ',', ' ')." km/h) (Δt=" . htmlspecialchars($this->format_duree($coureur['t6']-$coureur['t5'])) . ")",

                $this->format_duree($coureur['t7'])." (".number_format($this->calculerVitesse($dist2T,$coureur['t6'],$coureur['t7']), 2, ',', ' ')." km/h) (Δt=" . htmlspecialchars($this->format_duree($coureur['t7']-$coureur['t6'])) . ")",
                $this->format_duree($coureur['t8']), $coureur['seqTirs3'],
                $this->format_duree($coureur['t9'])." (".number_format($this->calculerVitesse($distPen,$coureur['t8'],$coureur['t9']), 2, ',', ' ')." km/h) (Δt=" . htmlspecialchars($this->format_duree($coureur['t9']-$coureur['t8'])) . ")",

                $this->format_duree($coureur['totalTime'])
            ],';');
        } // foreach
        fclose($fichier);
    } // composeFile

    public function isRaspberryPi(): bool {
        $model = @file_get_contents('/proc/device-tree/model');
        $cpu = @file_get_contents('/proc/cpuinfo');
        return (
            ($model && stripos($model, 'Raspberry Pi') !== false) ||
            ($cpu && stripos($cpu, 'BCM') !== false)
        );
    }

    public function purgerCsv() {
        $dir = __DIR__ . '/res';  // dossier où sont stockés les fichiers CSV
        if ($this->isRaspberryPi())
            shell_exec("sudo chown www-data:www-data -R 755 " . escapeshellarg($dir));
        else
            shell_exec("sudo chown wwwrun:www -R 755 " . escapeshellarg($dir));
        $files = glob($dir . '/*.csv');
        $now = time();
        $jours = 1; // nombre de jours

        foreach ($files as $file) {
            // date de dernière modification
            $fileTime = filemtime($file);
            // si le fichier a plus de 30 jours
            if ($now - $fileTime >= 60 * 60 * 24 * $jours) {
                unlink($file);
                echo "Supprimé : " . basename($file) . "<br>";
            } // if now
        } // foreach
    } // purgeCSV
} // CCsv
$csv = new CCsv();
?>
