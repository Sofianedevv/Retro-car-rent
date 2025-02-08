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
- Dupliquez le fichier `.env` en `.env.local`.
- Renseignez les informations suivantes dans le fichier `.env.local` :

   ```dotenv
   # Configuration de l'environnement
   APP_ENV=dev
   APP_SECRET=your_app_secret

   # Configuration de la base de données
   DATABASE_URL="mysql://[username]:[password]@[host]:[port]/[database_name]?serverVersion=8.0.40&charset=utf8mb4"

   # Configuration du Mailer
   MAILER_DSN="smtp://%MAILER_USERNAME%:%MAILER_PASSWORD%@sandbox.smtp.mailtrap.io:2525"

   # Configuration de Stripe
   STRIPE_SECRET_KEY="your_stripe_secret_key"
   STRIPE_PUBLIC_KEY="your_stripe_public_key"
   STRIPE_ENDPOINT_SECRET="your_stripe_endpoint_secret"

   # Configuration Google Maps API Key
   GOOGLE_MAPS_API_KEY="your_google_maps_api_key"

   # Configuration de la file de messages
   MESSENGER_TRANSPORT_DSN="doctrine://default?auto_setup=0"
   ```

4. **Vérification** :
   Après avoir mis à jour le fichier `.env.local`, vous pouvez vérifier que la configuration fonctionne correctement en exécutant :

   ```bash
   symfony console doctrine:database:create
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
| Utilisateur| joesph.quitzon@yahoo.com    | bonjour                |

## Fonctionnalités principales
### 1. Fonctionnalités pour l'Utilisateur (Client) :
- **Système d’inscription** avec confirmation par e-mail et réinitialisation de mot de passe.
- **Catalogue des véhicules** organisé par type (voitures, motos, vans) avec fiches détaillées.
- **Recherche et filtres avancés** par marque, prix, type de véhicule, etc.
- **Système de réservation** avec sélection des dates, véhicules et options supplémentaires.
- **Confirmation de réservation** par e-mail.
- **Visualisation et téléchargement des factures** en format PDF.
- **Système de commentaires et avis** sur les véhicules avec notation et commentaires détaillés.
- **Ajout aux favoris** pour enregistrer des véhicules préférés.
- **Notifications** sur les réservations, disponibilités et favoris.

### 2. Fonctionnalités pour l’Administrateur :
- **Tableau de bord** centralisé pour suivre les véhicules, utilisateurs et réservations.
- **Gestion des véhicules** : ajout, lecture, mise à jour, suppression.
- **Gestion des utilisateurs** : modification des rôles et suppression.
- **Gestion des réservations** : visualisation, modification du statut et suppression.
- **Gestion des catégories de véhicules** : ajout de nouvelles catégories et consultation des existantes.

### 3. Fonctionnalités secondaires implémentées :
- **Système de recommandations** de véhicules similaires.
- **Accès pour les visiteurs non connectés** pour consulter les véhicules sans pouvoir réserver.

### 4. Fonctionnalités futures envisagées implémentées :
- **Paiement sécurisé via Stripe** avec carte bancaire et portefeuilles électroniques.

Vous pouvez vous référer au cahier des charges[ \`CDC_Retro-Car-Rent.pdf\`](./CDC_Retro-Car-Rent.pdf) dans ce dépôt pour plus de détails.

## Structure de la base de données
Vous trouverez ci-dessous le schéma de la base de données utilisée pour ce projet :
![Schéma UML](./DB_Schema.png)  
Ou consultez le fichier [DB-schema-graphviz.dot](./DB-schema-graphvis.dot) pour le code source du schéma.

## Entités
Voici les entités principales utilisées dans ce projet :

1. **Car**
2. **Category**
3. **Contact**
4. **Doctrine Migration Versions**
5. **Favorite**
6. **Favorite Vehicle**
7. **Invoice**
8. **Messenger Messages**
9. **Motorcycle**
10. **Motorcycle Type**
11. **Notification**
12. **Payment**
13. **Reservation**
14. **Reservation Vehicle Option**
15. **Review**
16. **User**
17. **Van**
18. **Vehicle**
19. **Vehicle Category**
20. **Vehicle Image**
21. **Vehicle Option**
22. **Vehicle Vehicle Option**

### Les relations entre les entités :

#### 1. **One-to-One Relationships**:
- **Car** → **Vehicle**
- **Motorcycle** → **Motorcycle Type**
- **Motorcycle** → **Vehicle**
- **Van** → **Vehicle**
- **Invoice** → **Reservation**
- **Payment** → **Reservation**

#### 2. **Many-to-One Relationships**:
- **Favorite** → **User**
- **Favorite Vehicle** → **Favorite**
- **Favorite Vehicle** → **Vehicle**
- **Notification** → **User**
- **Reservation** → **User**
- **Reservation** → **Vehicle**
- **Reservation Vehicle Option** → **Reservation**
- **Reservation Vehicle Option** → **Vehicle Option**
- **Review** → **User**
- **Review** → **Vehicle**
- **Vehicle Category** → **Category**
- **Vehicle Category** → **Vehicle**
- **Vehicle Image** → **Vehicle**
- **Vehicle Vehicle Option** → **Vehicle**
- **Vehicle Vehicle Option** → **Vehicle Option**

#### 3. **Many-to-Many Relationships**:
- **Vehicle Vehicle Option** → **Vehicle Option** (Many-to-Many, via the `Vehicle Vehicle Option` table)
- **Favorite Vehicle** → **Vehicle** (Many-to-Many, via the `Favorite Vehicle` table)

#### 4. **Self-Referencing Relationships**:
- **Review** → **Review** (Self-Referencing Parent-Child Relationship)

## Processus de validation
Décrivez comment valider des données ou des processus si applicable (exemple : workflow d'une commande, réservation, etc.).

## Tests
1. **Tests unitaires :**

**Fonctionnalités testées** :
   - Filtres avancés pour véhicules selon plusieurs critères.
   - Calcul de durée pour les réservations.

**Services testés** :
   - `VehicleFiltersService` (Gestion des filtres de véhicules).
   - `ReservationService` (Gestion des réservations).
  
**Lancer les tests**
Assurez-vous que PHPUnit est installé :
   ```bash
   composer require --dev phpunit/phpunit
   ```

**Exécuter les tests unitaires**
- **VehicleFiltersServiceTest** :
  ```bash
  php bin/phpunit tests/unit/service/VehicleFiltersServiceTest.php
  ```
- **ReservationServiceTest** :
  ```bash
  php bin/phpunit tests/unit/service/ReservationServiceTest.php
  ```
2. **Tests fonctionnels :**

## Sécurité
### 3.1 Authentification sécurisée
- Le projet met en place une **authentification sécurisée** pour les utilisateurs grâce à un système de connexion avec **email** et **mot de passe crypté**.
- Le mot de passe est stocké de manière sécurisée à l'aide de l'algorithme **bcrypt** pour garantir la sécurité des informations personnelles.
- L'authentification des utilisateurs se fait via un système de session sécurisé, avec la possibilité de maintenir la session active grâce à la fonctionnalité "Se souvenir de moi" pour une durée de 7 jours.

### 3.2 Voters personnalisés
Dans ce projet, deux **voters** sont définis :
1. **UserBannedVoter** : Vérifie si l'utilisateur est banni via le rôle `ROLE_BANNED` et empêche l'accès aux actions pour les utilisateurs bannis.
2. **VehicleVoter** : Gère l'accès aux actions liées aux véhicules (création, édition, consultation) en fonction des rôles. Seuls les administrateurs (`ROLE_ADMIN`) peuvent créer ou modifier des véhicules. Tous les utilisateurs peuvent les consulter.

### 3.3 Gestion des rôles et permissions
Le projet implémente **trois rôles différents** avec des permissions spécifiques :
- **Rôle Utilisateur** : Accès limité à la consultation des véhicules, à la réservation et à la gestion des factures.
- **Rôle Administrateur** : Accès complet à la gestion des utilisateurs, des véhicules, des réservations, ainsi qu'à la gestion des catégories de véhicules et à la visualisation des statistiques.
- **Rôle Banni** : Ce rôle est attribué aux utilisateurs bannis. Les utilisateurs avec ce rôle ne peuvent pas effectuer d'actions spécifiques sur le site, grâce au **UserBannedVoter**.

## APIs

- **Controller dédié pour une API** : Le projet inclut un controller spécifiquement conçu pour gérer une API, avec des fonctionnalités de normalisation et de dénormalisation des données en format JSON.
- **Envoi de mail** : L'application utilise **Symfony Mailer** avec **Mailtrap** comme service de messagerie pour tester l'envoi d'e-mails en environnement de développement. Lorsqu'un utilisateur soumet le formulaire de contact, son message est sauvegardé en base de données, puis un e-mail est généré et envoyé à une adresse Mailtrap pour simulation et vérification.
- **Accès à une API externe** : Ce projet intègre Stripe comme solution de paiement pour gérer les réservations de véhicules.

**Fonctionnalités principales** :

- _Création de session Stripe Checkout :_ Lorsqu'un utilisateur effectue une réservation, une session de paiement Stripe est générée.

- _Confirmation de paiement :_ Une fois le paiement réussi, le statut de la réservation passe à "Confirmé" et une facture est générée.

- _Annulation de paiement :_ En cas d'échec ou d'annulation, la réservation est marquée comme annulée.
## Requêtes personnalisées avec Query Builder

Des requêtes personnalisées ont été implémentées dans les repositories en utilisant **Query Builder**. Voici quelques exemples de ces requêtes :

- **`findBestRated`** : Recherche des véhicules (voitures, motos, vans) ayant les meilleures évaluations.
- **`findOverlappingReservations`** : Trouve les réservations qui se chevauchent pour une période donnée.
- **`getAverageRating`** : Calcule la note moyenne d'un véhicule.
- **`findCarsByFilters`**, **`findMotorcyclesByFilters`**, **`findVansByFilters`** : Récupère des véhicules filtrés par différents critères (comme les caractéristiques et les véhicules réservés) pour chaque type de véhicule (voitures, motos, vans).

## Fonctionnalités bonus
Voici les fonctionnalités bonus ajoutées au projet :

- **Génération de PDF de factures** : Utilisation du service `PdfService` pour générer des factures au format PDF.
- **Commandes personnalisées** : Des commandes Symfony personnalisées ont été ajoutées pour automatiser des processus spécifiques ...

