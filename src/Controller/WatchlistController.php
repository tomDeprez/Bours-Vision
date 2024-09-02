<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Contracts\HttpClient\HttpClientInterface;
use Symfony\Component\DomCrawler\Crawler;
use Symfony\Component\Cache\Adapter\FilesystemAdapter;
use Symfony\Contracts\Cache\ItemInterface;


class WatchlistController extends AbstractController
{
    private HttpClientInterface $client;

    public function __construct(HttpClientInterface $client)
    {
        $this->client = $client;
    }

    #[Route('/', name: 'app_watchlist')]
    public function index(): Response
    {
        $cache = new FilesystemAdapter();
    
        // URLs des pages pour récupérer les informations de base avec descriptions
        $urlsWithDescriptions = [
            'https://bourse.boursobank.com/cours/NVDA/' => "💎 NVIDIA : Entreprise qui fabrique des puces pour ordinateurs et consoles, surtout pour les jeux vidéo et l'intelligence artificielle.",
            'https://bourse.boursobank.com/bourse/trackers/cours/1rTCW8/' => "💎 ETF qui investit dans les entreprises technologiques en Europe.",
            'https://bourse.boursobank.com/bourse/trackers/cours/1rTLVC/' => "💎 ETF sur les entreprises mondiales qui sont leaders dans leur secteur.",
            'https://bourse.boursobank.com/bourse/trackers/cours/1rTHLT/' => "💎 ETF sur les entreprises de santé qui fabriquent des médicaments et des équipements médicaux.",
            'https://bourse.boursobank.com/bourse/trackers/cours/1rTPANX/' => "💎 ETF sur les entreprises du secteur alimentaire, qui fabriquent la nourriture que nous mangeons tous les jours.",
            #############################################
            'https://bourse.boursobank.com/cours/MSFT/' => "Microsoft : Entreprise qui crée des logiciels comme Windows, et des consoles comme la Xbox.",
            'https://bourse.boursobank.com/cours/GOOGL/' => "Alphabet Inc. : C'est la société qui possède Google, le moteur de recherche que tout le monde utilise.",
            'https://bourse.boursobank.com/cours/AAPL/' => "Apple Inc. : Entreprise connue pour ses iPhones, iPads et MacBooks.",
            'https://bourse.boursobank.com/bourse/trackers/cours/1rTPUST/' => "ETF qui investit dans les entreprises américaines à forte croissance.",
            'https://bourse.boursobank.com/bourse/trackers/cours/1rTESE' => "ETF sur les entreprises européennes qui respectent l'environnement.",
            'https://bourse.boursobank.com/bourse/trackers/cours/1rACSX5/' => "ETF qui investit dans les grandes entreprises de la zone euro.",
            'https://bourse.boursobank.com/bourse/trackers/cours/1rTCACC/' => "ETF sur les entreprises technologiques américaines.",
            'https://bourse.boursobank.com/bourse/trackers/cours/1rTTNOW/' => "ETF qui investit dans les entreprises innovantes du secteur technologique."
        ];
        
    
        $stocks = [];
    
        foreach ($urlsWithDescriptions as $url => $description) {
            $cacheUrlKey = 'stock_data_' . md5($url) . "2";
            $stockData = $cache->get($cacheUrlKey, function (ItemInterface $item) use ($url, $description) {
                $item->expiresAfter(3600); // Expire après une heure
    
                $response = $this->client->request('GET', $url);
                $htmlContent = $response->getContent();
                $crawler = new Crawler($htmlContent);
    
                // Extraction des informations depuis le HTML
                $name = $crawler->filter('.c-faceplate__company-title a')->text();
                $symbol = $crawler->filter('.c-faceplate__company-title a')->attr('href');
                $symbolParts = explode('/', rtrim($symbol, '/'));
                $symbolLastPart = end($symbolParts);
    
                $price = $crawler->filter('.c-faceplate__price .c-instrument--last')->text();
                $currency = $crawler->filter('.c-faceplate__price-currency')->text();
                $variation = $crawler->filter('.c-instrument--variation')->text();
                $isin = $crawler->filter('.c-faceplate__isin')->text();
    
                // Extraire la liste des entreprises de l'ETF
                $companies = [];
                // if (strpos($url, 'trackers') !== false) {
                //     $companies = $crawler->filter('.list-of-companies .company-name')->each(function ($node) {
                //         return $node->text();
                //     });
                // }
    
                // Déterminer si la variation est positive ou négative
                $isPositive = strpos($variation, '+') === 0;
    
                return [
                    'name' => $name,
                    'price' => $price,
                    'currency' => $currency,
                    'variation' => $variation,
                    'isin' => $isin,
                    'isPositive' => $isPositive,
                    'description' => $description, // Ajout de la description ici
                    'companies' => $companies, // Ajout de la liste des entreprises dans l'ETF
                    'details_url' => $this->generateUrl('app_watchlist_details', ['symbol' => $symbolLastPart]),
                ];
            });
    
            $stocks[] = $stockData;
        }
    
        return $this->render('watchlist/index.html.twig', [
            'stocks' => $stocks,
        ]);
    }
    
    
    #[Route('/details/{symbol}', name: 'app_watchlist_details')]
    public function details(string $symbol): Response
    {
        $url = 'https://www.boursorama.com/bourse/action/graph/ws/GetTicksEOD?symbol=' . $symbol . '&length=3650&period=0&guid=';
    
        $response = $this->client->request('GET', $url);
        $data = $response->toArray();
    
        $stockDetails = [];
        $monthlyPerformance = []; // Nouveau tableau pour stocker la performance mensuelle
        $randomNumber = rand(9999, 999999); // Génération d'un nombre aléatoire
    
        if (isset($data['d'])) {
            $quotes = $data['d']['QuoteTab'] ?? [];
            $labels = [];
            $prices = [];
            $volume = [];
            $monthlyFirstPrices = []; // Nouveau tableau pour stocker les premiers prix du mois
            $monthlyLastPrices = [];  // Nouveau tableau pour stocker les derniers prix du mois
            $monthlyVolumes = [];
            $currentMonth = null;
            $previousQuote = null;  // Initialisation de la variable $previousQuote
    
            // Date de référence (ex: 1er janvier 1970)
            $referenceDate = new \DateTime('1970-01-01');
    
            foreach ($quotes as $quote) {
                $date = clone $referenceDate;
                $date->modify('+' . $quote['d'] . ' days');
                $month = $date->format('Y-m'); // Formatage en YYYY-MM
    
                $labels[] = $date->format('Y-m-d'); // Stocker toutes les dates pour le graphique
                $prices[] = $quote['c']; // Stocker tous les prix pour le graphique
                $volume[] = $quote['v']; // Stocker tous les volumes pour le graphique
    
                if ($currentMonth !== $month) {
                    // Si nous passons à un nouveau mois, stocker le dernier prix du mois précédent
                    if ($previousQuote !== null) {
                        $monthlyLastPrices[$currentMonth] = $previousQuote['c'];
                    }
                    // Stocker le premier prix du mois actuel
                    $monthlyFirstPrices[$month] = $quote['c'];
                    $currentMonth = $month;
                }
    
                // Stocker le volume du mois (cumulatif ou dernier, selon votre besoin)
                $monthlyVolumes[$month] = $quote['v'];
    
                // Mémoriser le dernier quote du mois courant pour utilisation dans le prochain tour de boucle
                $previousQuote = $quote;
            }
    
            // Assurer que le dernier mois dans les données a aussi son dernier prix stocké
            if ($currentMonth !== null) {
                $monthlyLastPrices[$currentMonth] = $previousQuote['c'];
            }
    
            // Calcul des gains mensuels en comparant le dernier prix d'un mois avec le premier prix du mois suivant
            $months = array_keys($monthlyFirstPrices);
            for ($i = 0; $i < count($months) - 1; $i++) {
                $currentMonth = $months[$i];
                $nextMonth = $months[$i + 1];
                $gain = (($monthlyFirstPrices[$nextMonth] - $monthlyLastPrices[$currentMonth]) / $monthlyLastPrices[$currentMonth]) * 100;
                $englishMonth = date('F', strtotime($currentMonth . '-01')); // Récupère le mois en anglais
                $formattedMonth = $this->translateMonthToFrench($englishMonth) . ' ' . date('Y', strtotime($currentMonth . '-01')); // Mois en français
                $monthlyPerformance[] = [
                    'month' => $formattedMonth,
                    'gain' => round($gain, 2),
                    'volume' => $monthlyVolumes[$currentMonth] ?? null, // Volume mensuel si nécessaire
                ];
            }
    
            // Récupérer seulement les 12 derniers éléments
            $monthlyPerformance = array_slice($monthlyPerformance, -12);
    
            $stockDetails = [
                'name' => $data['d']['Name'] ?? 'N/A',
                'symbol' => $data['d']['SymbolId'] ?? 'N/A',
                'labels' => $labels,  // Tableau pour les labels (Dates complètes)
                'prices' => $prices,  // Tableau pour les prix
                'volume' => $volume,  // Tableau pour les volumes
            ];
        }
    
        return $this->render('watchlist/details.html.twig', [
            'stockDetails' => $stockDetails,
            'randomNumber' => $randomNumber, // Envoi du chiffre aléatoire à la vue
            'monthlyPerformance' => $monthlyPerformance, // Performance mensuelle envoyée à la vue
        ]);
    }
    
    function translateMonthToFrench(string $month): string
    {
        $months = [
            'January' => 'Janvier',
            'February' => 'Février',
            'March' => 'Mars',
            'April' => 'Avril',
            'May' => 'Mai',
            'June' => 'Juin',
            'July' => 'Juillet',
            'August' => 'Août',
            'September' => 'Septembre',
            'October' => 'Octobre',
            'November' => 'Novembre',
            'December' => 'Décembre',
        ];
    
        return $months[$month] ?? $month; // Retourne le mois traduit ou l'original si non trouvé
    }
    
    
    
    
}
