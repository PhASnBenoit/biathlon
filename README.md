# biathlon
Développement d'une course suivie informatiquement
Ces sources gèrent la partie supervision WEB pour le master, les juges et le public.

Paramétrage du serveur WEB Apache2 : Ajouter les alias suivants :
Alias /biathlon/ "/srv/www/htdocs/biathlon/"
Alias /master/ "/srv/www/htdocs/biathlon/master/"
Alias /juge/ "/srv/www/htdocs/biathlon/juge/"
Alias /public/ "/srv/www/htdocs/biathlon/public/"
Autoriser les droits des fichiers pour que le serveur WEB puisse écrire dans le dossier /biathlon/res.

Firewall :
Autoriser le port HTTP et HTTPS

AP WIFI :
Autoriser la connexion WIFI sur le SSID biathlon.
Mot de passe WIFI : biathlon

Version des sources :
v1.7
identification de la carte RPI plutôt qu'une machine standard. Les chemins de sauvegarde des res sont adaptés. Les droits également.
Le profil master (anciennement arbitre) est revu pour n'accepter qu'une connexion.

v1.8
Déplacement des résultats sur la page de connexion du master.
Ajout des distances préremplies pour les calculs de vitesse.

v1.85 @PhA - 23/05/25
Sauvegarde des vitesses et delta T dans le fichier csv

v1.9  @PhA - 24/05/25
Modifier le code master.

v2.0  @PhA - 25/05/25
Réinitialisation de la course si impossible de se connecter en tant que master.

v2.1  @PhA - 26/05/25
Corrections mineures pour la sécurité des pages master et juges.

v2.2  @PhA - 27/05/25
Meilleure gestion du cookie juge.
