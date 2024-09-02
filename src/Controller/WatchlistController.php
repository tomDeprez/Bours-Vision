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
            [
                'url' => 'https://bourse.boursobank.com/cours/NVDA/',
                'description' => "💎 NVIDIA : Entreprise qui fabrique des puces pour ordinateurs et consoles, surtout pour les jeux vidéo et l'intelligence artificielle.",
                'companies' => [
                    'NVIDIA Corporation'
                ]
            ],
            [
                'url' => 'https://bourse.boursobank.com/bourse/trackers/cours/1rTCW8/',
                'description' => "💎 ETF qui investit dans les entreprises technologiques en Europe.",
                'companies' => [
                    'ASML Holding NV',
                    'SAP SE',
                    'Siemens AG'
                ]
            ],
            [
                'url' => 'https://bourse.boursobank.com/bourse/trackers/cours/1rTLVC/',
                'description' => "💎 ETF sur les entreprises mondiales qui sont leaders dans leur secteur.",
                'companies' => [
                    'Apple Inc.',
                    'Microsoft Corp',
                    'Amazon.com Inc.'
                ]
            ],
            [
                'url' => 'https://bourse.boursobank.com/bourse/trackers/cours/1rTHLT/',
                'description' => "💎 ETF sur les entreprises européennes du secteur de la santé, notamment celles qui fabriquent des médicaments et des équipements médicaux.",
                'companies' => [
                    'AstraZeneca PLC',
                    'Sanofi SA',
                    'Novartis AG'
                ]
            ],
            [
                'url' => 'https://bourse.boursobank.com/bourse/trackers/cours/1rTPANX/',
                'description' => "💎 ETF sur les entreprises technologiques américaines respectant des critères ESG.",
                'companies' => [
                    'Apple Inc.',
                    'Microsoft Corp',
                    'Alphabet Inc.'
                ]
            ],
            [
                'url' => 'https://bourse.boursobank.com/cours/MSFT/',
                'description' => "Microsoft : Entreprise qui crée des logiciels comme Windows, et des consoles comme la Xbox.",
                'companies' => [
                    'Microsoft Corp'
                ]
            ],
            [
                'url' => 'https://bourse.boursobank.com/cours/GOOGL/',
                'description' => "Alphabet Inc. : C'est la société qui possède Google, le moteur de recherche que tout le monde utilise.",
                'companies' => [
                    'Alphabet Inc.'
                ]
            ],
            [
                'url' => 'https://bourse.boursobank.com/cours/AAPL/',
                'description' => "Apple Inc. : Entreprise connue pour ses iPhones, iPads et MacBooks.",
                'companies' => [
                    'Apple Inc.'
                ]
            ],
            [
                'url' => 'https://bourse.boursobank.com/bourse/trackers/cours/1rTPUST/',
                'description' => "ETF qui investit dans les entreprises américaines à forte croissance, notamment dans les secteurs technologique et de consommation.",
                'companies' => [
                    'Tesla Inc.',
                    'NVIDIA Corporation',
                    'Amazon.com Inc.',
                    'Apple Inc.',
                    'Meta Platforms Inc.',
                    'Microsoft Corporation'
                ]
            ],
            [
                'url' => 'https://bourse.boursobank.com/bourse/trackers/cours/1rTESE',
                'description' => "ETF sur les entreprises européennes qui respectent l'environnement, en mettant l'accent sur les leaders du développement durable.",
                'companies' => [
                    'Iberdrola SA',
                    'Orsted A/S',
                    'Enel SpA',
                    'Siemens AG',
                    'Schneider Electric SE',
                    'EDP Renováveis SA'
                ]
            ],
            [
                'url' => 'https://bourse.boursobank.com/bourse/trackers/cours/1rACSX5/',
                'description' => "ETF qui investit dans les grandes entreprises de la zone euro, offrant une exposition aux leaders du marché.",
                'companies' => [
                    'LVMH Moët Hennessy Louis Vuitton SE',
                    'SAP SE',
                    'Siemens AG',
                    'Allianz SE',
                    'TotalEnergies SE',
                    'Sanofi SA',
                    'ASML Holding NV',
                    'BASF SE',
                    'Volkswagen AG',
                    'BNP Paribas SA'
                ]
            ],
            [
                'url' => 'https://bourse.boursobank.com/bourse/trackers/cours/1rTCACC/',
                'description' => "ETF qui réplique la performance de l'indice CAC 40, offrant une exposition aux 40 plus grandes entreprises françaises.",
                'companies' => [
                    'LVMH Moët Hennessy Louis Vuitton SE',
                    'TotalEnergies SE',
                    'Sanofi',
                    'L\'Oréal SA',
                    'Schneider Electric SE',
                    'Airbus SE',
                    'Kering SA',
                    'BNP Paribas SA',
                    'AXA SA',
                    'Vinci SA'
                ]
            ],
            [
                'url' => 'https://bourse.boursobank.com/bourse/trackers/cours/1rTTNOW/',
                'description' => "ETF qui réplique la performance de l'indice MSCI World Information Technology, offrant une exposition aux entreprises du secteur technologique à travers le monde.",
                'companies' => [
                    'Apple Inc.',
                    'Microsoft Corporation',
                    'NVIDIA Corporation',
                    'Visa Inc. (Class A)',
                    'Samsung Electronics Co., Ltd.',
                    'Mastercard Incorporated (Class A)',
                    'ASML Holding N.V.',
                    'Broadcom Inc.',
                    'Adobe Inc.',
                    'Salesforce, Inc.',
                    'Oracle Corporation',
                    'Taiwan Semiconductor Manufacturing Company Limited',
                    'Intel Corporation',
                    'Cisco Systems, Inc.',
                    'Accenture plc (Class A)',
                    'Texas Instruments Incorporated',
                    'SAP SE',
                    'Qualcomm Incorporated',
                    'IBM Corporation',
                    'ServiceNow, Inc.'
                ]
            ],
            [
                'url' => 'https://boursorama.com/bourse/trackers/cours/1rTPSP5/',
                'description' => "ETF qui réplique la performance de l'indice S&P 500, offrant une exposition aux 500 plus grandes entreprises cotées aux États-Unis.",
                'companies' => [
                    'Apple Inc.',
                    'Microsoft Corporation',
                    'Amazon.com, Inc.',
                    'NVIDIA Corporation',
                    'Alphabet Inc. (Class A)',
                    'Alphabet Inc. (Class C)',
                    'Meta Platforms, Inc.',
                    'Tesla, Inc.',
                    'Berkshire Hathaway Inc. (Class B)',
                    'Johnson & Johnson',
                    'Visa Inc. (Class A)',
                    'Procter & Gamble Co.',
                    'UnitedHealth Group Incorporated',
                    'JPMorgan Chase & Co.',
                    'Mastercard Incorporated (Class A)',
                    'Exxon Mobil Corporation',
                    'Chevron Corporation',
                    'Home Depot, Inc.',
                    'Pfizer Inc.',
                    'Bank of America Corporation'
                ]
            ],
            [
                'url' => 'https://www.boursorama.com/bourse/trackers/cours/1rTWLD/',
                'description' => "ETF qui réplique la performance de l'indice MSCI World, offrant une exposition globale aux actions des grandes et moyennes capitalisations des marchés développés.",
                'companies' => [
                    'Apple Inc.',
                    'Microsoft Corporation',
                    'Amazon.com, Inc.',
                    'NVIDIA Corporation',
                    'Alphabet Inc. (Class A)',
                    'Alphabet Inc. (Class C)',
                    'Meta Platforms, Inc.',
                    'Tesla, Inc.',
                    'Berkshire Hathaway Inc. (Class B)',
                    'Johnson & Johnson',
                    'Visa Inc. (Class A)',
                    'Procter & Gamble Co.',
                    'UnitedHealth Group Incorporated',
                    'JPMorgan Chase & Co.',
                    'Mastercard Incorporated (Class A)',
                    'Exxon Mobil Corporation',
                    'Chevron Corporation',
                    'Home Depot, Inc.',
                    'Nestlé S.A.',
                    'Samsung Electronics Co., Ltd.'
                ]
            ],
            [
                'url' => 'https://www.boursorama.com/bourse/trackers/cours/1rTCL2/',
                'description' => "ETF qui réplique la performance de l'indice MSCI USA avec un effet de levier x2.",
                'companies' => [
                    'Apple Inc.',
                    'Microsoft Corporation',
                    'Amazon.com, Inc.',
                    'NVIDIA Corporation',
                    'Alphabet Inc. (Class A)',
                    'Alphabet Inc. (Class C)',
                    'Meta Platforms, Inc.',
                    'Tesla, Inc.',
                    'Berkshire Hathaway Inc. (Class B)',
                    'Visa Inc. (Class A)',
                    'Johnson & Johnson',
                    'UnitedHealth Group Incorporated',
                    'JPMorgan Chase & Co.',
                    'Procter & Gamble Co.',
                    'Mastercard Incorporated (Class A)',
                    'Exxon Mobil Corporation',
                    'Chevron Corporation',
                    'Home Depot, Inc.',
                    'Pfizer Inc.',
                    'Bank of America Corporation'
                ]
            ],
            [
                'url' => 'https://www.boursorama.com/bourse/trackers/cours/1rTLQQ/',
                'description' => "ETF qui investit dans les entreprises innovantes du secteur technologique.",
                'companies' => [
                    'Apple Inc.',
                    'Microsoft Corporation',
                    'Amazon.com, Inc.',
                    'NVIDIA Corporation',
                    'Alphabet Inc. (Class A)',
                    'Alphabet Inc. (Class C)',
                    'Meta Platforms, Inc.',
                    'Tesla, Inc.',
                    'Broadcom Inc.',
                    'PepsiCo, Inc.',
                    'Costco Wholesale Corporation',
                    'Adobe Inc.',
                    'Advanced Micro Devices, Inc.',
                    'Comcast Corporation (Class A)',
                    'Cisco Systems, Inc.',
                    'Texas Instruments Incorporated',
                    'Netflix, Inc.',
                    'Intel Corporation',
                    'Honeywell International Inc.',
                    'QUALCOMM Incorporated',
                    'PayPal Holdings, Inc.',
                    'Intuit Inc.',
                    'Charter Communications, Inc. (Class A)',
                    'Starbucks Corporation',
                    'Analog Devices, Inc.',
                    'AMD',
                    'Booking Holdings Inc.',
                    'Marvell Technology, Inc.',
                    'Lam Research Corporation',
                    'Micron Technology, Inc.',
                    'Applied Materials, Inc.',
                    'Automatic Data Processing, Inc.',
                    'Palo Alto Networks, Inc.',
                    'Mondelez International, Inc. (Class A)',
                    'The Kraft Heinz Company',
                    'Advanced Micro Devices, Inc.',
                    'JD.com, Inc.',
                    'Illumina, Inc.',
                    'Modern Inc.',
                    'Gilead Sciences, Inc.',
                    'Amgen Inc.',
                    'CSX Corporation',
                    'eBay Inc.',
                    'Lululemon Athletica Inc.',
                    'Xilinx, Inc.',
                    'NetEase, Inc.',
                    'DocuSign, Inc.',
                    'NXP Semiconductors N.V.',
                    'KLA Corporation',
                    'Cognizant Technology Solutions Corporation (Class A)',
                    'Workday, Inc. (Class A)',
                    'Align Technology, Inc.',
                    'Regeneron Pharmaceuticals, Inc.',
                    'Okta, Inc.',
                    'Seagen Inc.',
                    'DexCom, Inc.',
                    'Biogen Inc.',
                    'CoStar Group, Inc.',
                    'Zscaler, Inc.',
                    'Datadog, Inc. (Class A)',
                    'Zoom Video Communications, Inc. (Class A)',
                    'Verisk Analytics, Inc.',
                    'Splunk Inc.',
                    'Match Group, Inc.',
                    'Lucid Group, Inc.',
                    'Atlassian Corporation Plc (Class A)',
                    'Fortinet, Inc.',
                    'ASML Holding N.V.',
                    'Rivian Automotive, Inc. (Class A)',
                    'DocuSign, Inc.',
                    'Illumina, Inc.',
                    'Twilio Inc. (Class A)',
                    'MercadoLibre, Inc.',
                    'NortonLifeLock Inc.',
                    'Fiserv, Inc.',
                    'ZoomInfo Technologies Inc. (Class A)',
                    'Pinterest, Inc. (Class A)',
                    'Cognizant Technology Solutions Corporation (Class A)',
                    'Fastly, Inc. (Class A)',
                    'Pinduoduo Inc.',
                    'Warner Bros. Discovery, Inc. (Class A)',
                    'The Trade Desk, Inc. (Class A)',
                    'Marriott International, Inc. (Class A)',
                    'Copart, Inc.',
                    'O\'Reilly Automotive, Inc.',
                    'Synopsys, Inc.',
                    'Analog Devices, Inc.',
                    'Xilinx, Inc.',
                    'Monster Beverage Corporation',
                    'Old Dominion Freight Line, Inc.',
                    'Electronic Arts Inc.',
                    'Cadence Design Systems, Inc.',
                    'Skyworks Solutions, Inc.',
                    'Paycom Software, Inc.',
                    'Expedia Group, Inc.',
                    'Trip.com Group Limited',
                    'PayPal Holdings, Inc.',
                    'Verisign, Inc.',
                    'NortonLifeLock Inc.',
                    'Mettler-Toledo International Inc.',
                    'Align Technology, Inc.',
                    'Incyte Corporation',
                    'Vertex Pharmaceuticals Incorporated',
                    'Biogen Inc.',
                    'Illumina, Inc.',
                    'IDEXX Laboratories, Inc.',
                    'Pacific Biosciences of California, Inc.',
                    'Keurig Dr Pepper Inc.',
                    'Mondelez International, Inc.',
                    'Sirius XM Holdings Inc.',
                    'Dish Network Corporation (Class A)',
                    'Liberty Global plc (Class A)',
                    'Liberty Global plc (Class C)',
                    'Liberty Broadband Corporation (Class A)',
                    'Liberty Broadband Corporation (Class C)',
                    'T-Mobile US, Inc.',
                    'Charter Communications, Inc. (Class A)',
                    'Dish Network Corporation (Class A)',
                    'Netflix, Inc.',
                    'Comcast Corporation (Class A)',
                    'The Kraft Heinz Company',
                    'Marriott International, Inc. (Class A)',
                    'Meta Platforms, Inc.',
                    'Facebook, Inc.',
                    'Alphabet Inc. (Class A)',
                    'Alphabet Inc. (Class C)',
                    'Zebra Technologies Corporation (Class A)',
                    'Tyler Technologies, Inc.',
                    'Gartner, Inc.',
                    'Trimble Inc.',
                    'Autodesk, Inc.',
                    'Cerner Corporation',
                    'Electronic Arts Inc.',
                    'Take-Two Interactive Software, Inc.',
                    'Activision Blizzard, Inc.',
                    'Roku, Inc.',
                    'KLA Corporation',
                    'Intel Corporation',
                    'Texas Instruments Incorporated',
                    'NVIDIA Corporation',
                    'AMD',
                    'Micron Technology, Inc.',
                    'Applied Materials, Inc.',
                    'Analog Devices, Inc.',
                    'Skyworks Solutions, Inc.',
                    'Broadcom Inc.',
                    'Qualcomm Incorporated',
                    'Lattice Semiconductor Corporation',
                    'Marvell Technology, Inc.',
                    'Advanced Micro Devices, Inc.',
                    'Lam Research Corporation',
                    'ASML Holding N.V.',
                    'ON Semiconductor Corporation',
                    'NXP Semiconductors N.V.',
                    'NVIDIA Corporation',
                    'AMD',
                    'Monolithic Power Systems, Inc.',
                    'Teradyne, Inc.',
                    'Microchip Technology Incorporated',
                    'MaxLinear, Inc.',
                    'Qualys, Inc.',
                    'Docusign, Inc.',
                    'Verisign, Inc.',
                    'PayPal Holdings, Inc.',
                    'Block, Inc.',
                    'MercadoLibre, Inc.',
                    'Shopify Inc.',
                    'Visa Inc. (Class A)',
                    'MasterCard Incorporated (Class A)',
                    'American Express Company',
                    'Discover Financial Services',
                    'Intuit Inc.',
                    'Paycom Software, Inc.',
                    'Fiserv, Inc.',
                    'Global Payments Inc.',
                    'Square, Inc.',
                    'Adyen N.V.',
                    'Ally Financial Inc.',
                    'Square, Inc.',
                    'PayPal Holdings, Inc.',
                    'Intuit Inc.',
                    'Paycom Software, Inc.',
                    'Ceridian HCM Holding Inc.',
                    'Bill.com Holdings, Inc.',
                    'Shopify Inc.',
                    'MercadoLibre, Inc.',
                    'Monzo Bank Ltd.',
                    'N26 GmbH',
                    'Stripe, Inc.',
                    'Revolut Ltd.',
                    'Adyen N.V.',
                    'Square, Inc.',
                    'PayPal Holdings, Inc.',
                    'Ally Financial Inc.',
                    'Goldman Sachs Group, Inc.',
                    'Morgan Stanley',
                    'JPMorgan Chase & Co.',
                    'Bank of America Corporation',
                    'Citigroup Inc.',
                    'Wells Fargo & Company',
                    'PNC Financial Services Group, Inc.',
                    'U.S. Bancorp',
                    'Truist Financial Corporation',
                    'Citizens Financial Group, Inc.',
                    'First Republic Bank',
                    'SVB Financial Group',
                    'Regions Financial Corporation',
                    'M&T Bank Corporation',
                    'Fifth Third Bancorp',
                    'Huntington Bancshares Incorporated',
                    'Comerica Incorporated',
                    'Zions Bancorporation, N.A.',
                    'KeyCorp',
                    'Associated Banc-Corp',
                    'Synovus Financial Corp.',
                    'Wintrust Financial Corporation',
                    'BOK Financial Corporation',
                    'First Horizon Corporation',
                    'PacWest Bancorp',
                    'Western Alliance Bancorporation',
                    'Valley National Bancorp',
                    'Pinnacle Financial Partners, Inc.',
                    'SouthState Corporation',
                    'Bank of Hawaii Corporation',
                    'Community Bank System, Inc.',
                    'Pinnacle Financial Partners, Inc.',
                    'SouthState Corporation',
                    'Bank of Hawaii Corporation',
                    'Community Bank System, Inc.',
                    'Great Western Bancorp, Inc.',
                    'Hancock Whitney Corporation',
                    'Atlantic Union Bankshares Corporation',
                    'BancorpSouth Bank',
                    'Prosperity Bancshares, Inc.',
                    'IBERIABANK Corporation',
                    'First Interstate BancSystem, Inc.',
                    'Simmons First National Corporation',
                    'Old National Bancorp',
                    'United Community Banks, Inc.',
                    'Hancock Whitney Corporation',
                    'Atlantic Union Bankshares Corporation',
                    'BancorpSouth Bank',
                    'Prosperity Bancshares, Inc.',
                    'IBERIABANK Corporation',
                    'First Interstate BancSystem, Inc.',
                    'Simmons First National Corporation',
                    'Old National Bancorp',
                    'United Community Banks, Inc.',
                    'Hancock Whitney Corporation',
                    'Atlantic Union Bankshares Corporation',
                    'BancorpSouth Bank',
                    'Prosperity Bancshares, Inc.',
                    'IBERIABANK Corporation',
                    'First Interstate BancSystem, Inc.',
                    'Simmons First National Corporation',
                    'Old National Bancorp',
                    'United Community Banks, Inc.',
                    'Regions Financial Corporation',
                    'M&T Bank Corporation',
                    'Fifth Third Bancorp',
                    'Huntington Bancshares Incorporated',
                    'Comerica Incorporated',
                    'Zions Bancorporation, N.A.',
                    'KeyCorp',
                    'Associated Banc-Corp',
                    'Synovus Financial Corp.',
                    'Wintrust Financial Corporation',
                    'BOK Financial Corporation',
                    'First Horizon Corporation',
                    'PacWest Bancorp',
                    'Western Alliance Bancorporation',
                    'Valley National Bancorp',
                    'Pinnacle Financial Partners, Inc.',
                    'SouthState Corporation',
                    'Bank of Hawaii Corporation',
                    'Community Bank System, Inc.',
                    'Great Western Bancorp, Inc.',
                    'Hancock Whitney Corporation',
                    'Atlantic Union Bankshares Corporation',
                    'BancorpSouth Bank',
                    'Prosperity Bancshares, Inc.',
                    'IBERIABANK Corporation',
                    'First Interstate BancSystem, Inc.',
                    'Simmons First National Corporation',
                    'Old National Bancorp',
                    'United Community Banks, Inc.'
                ]
            ]
        ];

        $stocks = [];

        foreach ($urlsWithDescriptions as $entry) {
            $url = $entry['url'];
            $description = $entry['description'];
            $companies = $entry['companies'];
        
            $cacheUrlKey = 'stock_data_' . md5($url) . "8";
            $stockData = $cache->get($cacheUrlKey, function (ItemInterface $item) use ($url, $description, $companies) {
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
                $risque = 0;
                try {
                    $risque = $crawler->filter('.c-gauge')->attr('data-gauge-current-step');
                } catch (\Throwable $th) {
                }
                

        
                // Déterminer si la variation est positive ou négative
                $isPositive = strpos($variation, '+') === 0;
        
                return [
                    'name' => $name,
                    'price' => $price,
                    'currency' => $currency,
                    'variation' => $variation,
                    'isin' => $isin,
                    'risque' => $risque, // Ajout de la note de risque dans le tableau de retour
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
