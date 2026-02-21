Auteur
    - Nom : GNOUKABITI
    - Prénom : Charôn
    - Filière : Génie Logiciel
    - Date :2021-03-25

I-Présentation du projet :
    Ce projet constitue le premier travail pratique pour l'unité d'enseignement Développement Web. L'objectif est de mettre en place l'architecture MVC 
d'une plateforme d'orientation scolaire nommée SchoolPrepar en utilisant le framework Symfony.

II-Spécifications techniques:
    - Framework Symfony 7.4
    - PHP 8.3.14
    - Base de données: PostgreSQL (configuré via PDO_PGSQL)
    - Architecture: Modèle-Vue-Contrôleur (MVC)

III-Installation et Configuration

1- Cloner ou récupérer le dépôt git :
    Exécuter la commande :
        git clone [https://github.com/Charon-TTV/SchollpreparProjetSymfony.git]

2- Installer les dépendances :
    Exécuter la commande :
        composer install

3- Configuration de la base de données :
    Modifier le fichier.env avec vos identifiants PostgreSQL :
DATABASE_URL="postgresql://nom_utilisateur:votre_mot_de_passe@127.0.0.1:5432/schoolprepar?serverVersion=16&charset=utf8"

4- Créer la base de données :
    Exécuter la commande :
        php bin/console doctrine:database:create

