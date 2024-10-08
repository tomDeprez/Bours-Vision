# Mes actions PEA

Je suis ravi de partager avec vous mon projet de suivi d'actions, dans lequel je présente mes investissements personnels et les actions que je considère parmi les meilleures du moment. Ce projet est le fruit de mon analyse des marchés financiers, et j'espère qu'il vous sera utile pour affiner vos propres stratégies d'investissement. 

bientôt disponible sur mon site.

# Watchlist Project

Application web Symfony pour suivre et afficher des actions et ETF avec des données en temps réel issues de Boursorama Banque. L'application récupère, met en cache, et présente des informations financières avec des descriptions claires, et inclut des vues détaillées avec graphiques interactifs pour analyser les performances mensuelles.

## Installation

1. Clonez le répertoire du projet :
    ```bash
    git clone https://github.com/tomDeprez/Bours-Vision.git
    cd Bours-Vision

    ```

2. Installez les dépendances PHP :
    ```bash
    composer install
    npm install
    ```

3. Configurez vos variables d'environnement en copiant le fichier `.env` :
    ```bash
    cp .env .env.local
    POSTGRES_DB=boursVision
    POSTGRES_USER=boursVision
    POSTGRES_PASSWORD=mot de passe
    POSTGRES_VERSION=16
    docker-compose.exe up -d
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

![Screenshot de l'application](public/img/image2.png)

