# 🏢 Projet LiveCampus.
Ce projet a été réalisé dans le cadre du cursus scolaire pour **LiveCampus**.

## 🗺️ Description
**LiveCampus** est un réseau social d'entreprise conçu pour faciliter la communication, la collaboration au sein des équipes d'une entreprise, et accroître la productivité. Inspiré des forums en ligne, il permet aux utilisateurs de partager des informations, des messages et des fichiers dans un environnement sécurisé et convivial.

## ⚙️ Fonctionnalités
- **🔐 Authentication :**
    - Inscription de nouveau compte avec attribution automatique de rôle en fonction du domaine de l'adresse e-mail.
    - Connexion et déconnexion sécurisées.
- **📚 Profil :**
    - Affichage des informations du compte.
    - Possibilité de modifier les informations du compte.
    - Affichage des messages envoyés par l'utilisateur.
    - Modification de la photo de profil.
- **✍️ Rédaction :**
    - Organisation des discussions par catégories.
    - Création de "boards" au sein des catégories.
    - Ouverture de sujets de discussion dans les "boards".
    - Envoi de messages dans les sujets avec possibilité de joindre des fichiers.
- **🔒 Autorisation :**
    - Gestion des permissions pour limiter l'accès aux contenus en fonction des rôles.
    - Administration des utilisateurs avec possibilité de modifier les rôles, de bloquer/débloquer et de supprimer des utilisateurs.

## 💻 Stack Technologique
- Symfony 7.0
- PHP
- Doctrine ORM
- Twig
- MySQL
- HTML/CSS (Tailwind CSS)
- JavaScript 

## ⬇️ Installation
1. Il faut tout d'abord cloner ce répertoire vers votre machine locale.
2. Les dépendances sont à installer avec Composer. Pour cela, exécutez `composer install`.
3. Configurez votre base de données dans le fichier `.env`.
4. Lancer les migrations pour créer les tables avec `php bin/console doctrine:migrations:migrate`.
5. Enfin, démarrez le serveur Symfony avec `symfony server:start`.
6. L'accès à l'application est réalisable dans votre navigateur à l'adresse diffusée par Symfony.

## 🧪 Pour tester
1. Aller sur la page démo de l'application : [Démo](https://forum.luwa.fr).
2. Créer un compte avec une adresse e-mail de domaine :
   - `@admin.fr' pour obtenir le rôle administrateur.
   - `@insider.fr' pour obtenir le rôle d'insider.
   - `@external.fr' pour obtenir le rôle d'external.
   - `@collaborator.fr' pour obtenir le rôle de collaborator.
   - ou autre pour obtenir le rôle de user.
3. Connectez-vous avec le compte créé.
4. Vous pouvez maintenant tester les fonctionnalités de l'application.

## 👥 Contributeurs
- Antoine Masia - [@MasiaAntoine](https://github.com/MasiaAntoine)
- Tristan Leblond - [@TristanLBD](https://github.com/TristanLBD)
- Alan Hilarion - [@ahilarion](https://github.com/ahilarion)
