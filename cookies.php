<?php
// Démarrer la session (si nécessaire pour autre chose)
//session_start();

// Vérifier si le cookie client_token existe
if (!isset($_COOKIE['client_token'])) {
    // Le client n'a pas encore de token : on en crée un
    $token = bin2hex(random_bytes(16));

    // Stocker dans la base de données
    $stmt = $pdo->prepare("INSERT INTO clients (token, nom) VALUES (:token, :nom)");
    $stmt->execute([
        'token' => $token,
        'nom' => 'Client inconnu', // ou récupérer depuis un formulaire si disponible
    ]);

    // Définir le cookie pour 30 jours
    setcookie('client_token', $token, time() + (86400 * 30), "/");

    echo "Nouveau client enregistré. Token : $token";
} else {
    // Le client revient : retrouver dans la base
    $token = $_COOKIE['client_token'];

    $stmt = $pdo->prepare("SELECT * FROM clients WHERE token = :token");
    $stmt->execute(['token' => $token]);
    $client = $stmt->fetch();

    if ($client) {
        echo "Bienvenue de retour, " . htmlspecialchars($client['nom']);
    } else {
        // Le token ne correspond à personne : créer un nouveau client
        $stmt = $pdo->prepare("INSERT INTO clients (token, nom) VALUES (:token, :nom)");
        $stmt->execute([
            'token' => $token,
            'nom' => 'Client mystère',
        ]);
        echo "Client ajouté malgré token inconnu. (cas rare)";
    }
}
?>
