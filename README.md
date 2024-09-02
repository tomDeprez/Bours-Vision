# Watchlist Project

Application web Symfony pour suivre et afficher des actions et ETF avec des données en temps réel issues de Boursorama Banque. L'application récupère, met en cache, et présente des informations financières avec des descriptions claires, et inclut des vues détaillées avec graphiques interactifs pour analyser les performances mensuelles.

## Installation

1. Clonez le répertoire du projet :
    ```bash
    git clone https://github.com/votre-utilisateur/votre-repo.git
    cd votre-repo
    ```

2. Installez les dépendances PHP :
    ```bash
    composer install
    ```

3. Configurez vos variables d'environnement en copiant le fichier `.env` :
    ```bash
    cp .env .env.local
    ```

4. Configurez les informations de connexion à la base de données dans le fichier `.env.local`.

5. Lancez les migrations pour créer les tables nécessaires :
    ```bash
    php bin/console doctrine:migrations:migrate
    ```

6. Démarrez le serveur Symfony :
    ```bash
    symfony server:start
    ```

## Utilisation

1. Ouvrez votre navigateur et accédez à l'URL suivante :
    ```
    http://localhost:8000/
    ```

2. Pour ajouter des actions ou ETF à suivre, modifiez la liste des URLs dans le contrôleur `WatchlistController.php`. Remplacez les URLs existantes par celles de Boursorama Banque uniquement. Exemple :
    ```php
    $urlsWithDescriptions = [
        'https://bourse.boursobank.com/cours/NVDA/' => "💎 NVIDIA : Entreprise qui fabrique des puces pour ordinateurs et consoles, surtout pour les jeux vidéo et l'intelligence artificielle.",
        // Ajoutez ici d'autres URLs de Boursorama Banque...
    ];
    ```

3. L'application affichera les informations et descriptions des actions/ETF que vous avez configurées.

## Screenshot

![Screenshot de l'application](public/img/image.png)

