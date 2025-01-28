# Projet Symfony - Retro Car Rent

## Description
Ce projet est un site web dédié à **la location de véhicules vintage et rétro**. Il s'adresse aux passionnés de voitures classiques, aux amateurs de design d'époque, et à ceux qui recherchent une expérience unique pour des occasions spéciales comme des mariages, des événements ou des séances photo.

L'objectif principal est de rendre accessible un catalogue de véhicules authentiques tout en offrant une navigation simple, intuitive et élégante, fidèle à l'esprit vintage du concept. Le site a été développé avec Symfony pour garantir une structure robuste, modulable et sécurisée.

## Prérequis
Voici les outils et versions nécessaires pour exécuter ce projet :

- **PHP** 8.1 ou supérieur
- **Composer** (gestionnaire de dépendances PHP)
- **Symfony CLI** (outil en ligne de commande pour Symfony)
- **Base de données** : MySQL ou PostgreSQL
- **Node.js** et **npm** (pour lancer les commandes TailwindCSS)


## Installation

1. **Cloner le dépôt :**
   ```bash
   git clone git clone https://github.com/Sofianedevv/Retro-car-rent.git
   cd Recro-car-rent
   ```

2. **Installer les dépendances PHP :**
   ```bash
   composer install
   ```

3. **Installer les dépendances JavaScript :**
Une fois le dépôt cloné, naviguez dans le répertoire du projet et installez les dépendances Node.js nécessaires :
   ```bash
   cd public/assets/...
   npm install
   ```
Cela installera TailwindCSS et Swiper. Vous pouvez ensuite lancer les commandes suivantes :
* **Mode développement :** Pour démarrer le processus de développement et surveiller les changements dans les fichiers CSS :
    ```bash
   npm run watch
   ```
* **Mode production :** Pour générer les fichiers CSS minifiés et optimisés pour la production :
    ```bash
    npm run build
   ```
4. **Configurer l'environnement :**  
   Dupliquer le fichier `.env` en `.env.local` et renseigner les informations suivantes :
   ```
   DATABASE_URL="mysql://[username]:[password]@[host]:[port]/[database_name]"
   MAILER_DSN="[mailer_configuration]"
   ```

5. **Créer la base de données :**
   ```bash
   php bin/console doctrine:database:create
   php bin/console doctrine:migrations:migrate
   ```

6. **Ajouter les fixtures :**
   ```bash
   php bin/console hautelook:fixtures:load
   ```

7. **Lancer le serveur local :**
   ```bash
   symfony server:start
   ```

## Comptes de tests

| Rôle       | Email                | Mot de passe           |
|------------|----------------------|------------------------|
| Admin      | admin@example.com    | jesuisunadministrateur |
| Utilisateur| user@example.com     | password               |

## Fonctionnalités principales
- Gestion des [entités principales du projet].
- Authentification avec rôles (`ROLE_ADMIN`, `ROLE_USER`, ...).
- Espace administrateur.
- Intégration avec une API externe ([préciser laquelle]).
- Envoi d'emails ([exemple d'usage]).
- [Autres fonctionnalités].

## Structure de la base de données
![Schéma UML](chemin/vers/schema.png)  
Ou une description textuelle des principales entités et relations.

## Processus de validation
Décrivez comment valider des données ou des processus si applicable (exemple : workflow d'une commande, réservation, etc.).

## Tests
1. **Tests unitaires :**
   ```bash
   php bin/phpunit
   ```

## Déploiement
Expliquez brièvement comment le projet a été déployé (ou est prêt à être déployé) :
- URL de démo (si applicable).
- Configuration CI/CD utilisée (exemple : GitHub Actions, GitLab CI).

## Fonctionnalités bonus (si présentes)
- [Exemple de fonctionnalités bonus, comme des tâches asynchrones ou des WebSockets.]

---
