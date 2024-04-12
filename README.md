# Projet Forum en Symfony - Live Campus
* Antoine Masia
* Alan Hilarion
* Leblond Tristan

### Description
Projet fictif réalisé à l'école Live Campus dans le cadre d'un exercice consistant à recréer un formulaire simpliste permettant de créer des catégories, d'y lier des boards, de lier des sujets à ces boards et d'envoyer des messages dans les différents sujets, avec possibilité d'y ajouter des pièces jointes.

### Diagramme de la base de données :
Lien vers le [Diagramme](https://dbdiagram.io/d/Copy-of-forum_livecampus-661900ac03593b6b61d5a94f).

### Technologies utilisées
* Framework PHP Symfony 7
* Framework Tailwind CSS pour le design responsive du site et des interfaces.
* Base de données MySQL distante

### Commandes à effectuer après importation du projet
* composer install
* npm i
* npm build
* Modifier le .env.example avec vos informations de connexion, et le renommer en .env
* symfony console doctrine:migrations:migrate

### Informations supplémentaires
Système de hachage des mots de passe et d'authentification réalisés par nos soins.