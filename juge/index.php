<?php
// index.php (juge)
require '../cpage.php';
$titre = 'BIATHLON VERIFICATION JUGE';
$foot = 'Biathlon Supervision System';
$page->entete($titre);
?>
    <script>
        function checkStatus() {
            $.ajax({
                url: '/biathlon/check_state.php',
                method: 'GET',
                success: function(response) {
                    if (response.trim() == 3) {
                        $('#status').text("Course en cours...");
                        clearInterval(interval);
                        window.location.href = 'juge3.php';
                    }

                    if (response.trim()==1 || response.trim()==2) {
                        $('#status').text("Autorisation du master");
                        clearInterval(interval);
                        window.location.href = 'juge2.php';
                    }
                },
                error: function() {
                    console.error('Erreur lors de la récupération des données.');
                }
            });
        }
        let interval = setInterval(checkStatus, 1000); // Vérifie toutes les secondes
    </script>
<?php $page->finHeadBody();?>
<?php $page->header($titre);?>
     <div id="status">Attente autorisation du master</div>
<?php $page->footer($foot);?>
