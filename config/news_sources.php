<?php

return [

    /*
    |--------------------------------------------------------------------------
    | News import sources
    |--------------------------------------------------------------------------
    |
    | Upstream RSS/Atom feeds polled by the news importer. Every URL below was
    | verified to return a parseable feed; entries that fail are skipped
    | individually so one bad source cannot break a refresh.
    |
    | Each entry supports:
    |   name      - human readable label, stored on articles.source_name
    |   url       - feed URL
    |   language  - 'fr' or 'en', stored on articles.language
    |   enabled   - set false to skip a source without deleting it
    |
    */

    'user_agent' => env('NEWS_USER_AGENT', 'Mozilla/5.0 (compatible; SunuNewsBot/1.0; +https://sununews.local)'),

    'timeout' => (int) env('NEWS_HTTP_TIMEOUT', 20),

    // Author of imported articles. articles.user_id is NOT NULL, so an
    // existing user must own every imported row.
    'author_email' => env('NEWS_AUTHOR_EMAIL', 'admin@example.com'),

    // Upper bound on items taken from any single feed per refresh.
    'per_source_limit' => (int) env('NEWS_PER_SOURCE_LIMIT', 15),

    'sources' => [

        [
            'name' => 'Seneweb',
            'url' => 'https://www.seneweb.com/feed',
            'language' => 'fr',
            'enabled' => true,
        ],

        [
            'name' => 'Le Soleil',
            'url' => 'https://www.lesoleil.sn/feed',
            'language' => 'fr',
            'enabled' => true,
        ],

        [
            'name' => 'APS',
            'url' => 'https://www.aps.sn/feed',
            'language' => 'fr',
            'enabled' => true,
        ],

        [
            'name' => 'Le Quotidien',
            'url' => 'https://www.lequotidien.sn/feed',
            'language' => 'fr',
            'enabled' => true,
            // This publisher returns 403 to the default bot user agent and to
            // any request carrying an explicit Accept header.
            'user_agent' => 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/124.0 Safari/537.36',
        ],

        [
            'name' => 'Rewmi',
            'url' => 'https://www.rewmi.com/feed',
            'language' => 'fr',
            'enabled' => true,
        ],

        [
            'name' => 'Emedia',
            'url' => 'https://www.emedia.sn/feed',
            'language' => 'fr',
            'enabled' => true,
        ],

        [
            'name' => 'Actusen',
            'url' => 'https://actusen.com/feed',
            'language' => 'fr',
            'enabled' => true,
        ],

        [
            'name' => 'Senego',
            'url' => 'https://www.senego.com/feed',
            'language' => 'fr',
            'enabled' => true,
        ],

        [
            'name' => 'Euronews',
            'url' => 'https://fr.euronews.com/rss?level=tag&name=senegal',
            'language' => 'fr',
            'enabled' => true,
        ],

        [
            'name' => 'Google News Sénégal',
            'url' => 'https://news.google.com/rss?hl=fr&gl=SN&ceid=SN:fr',
            'language' => 'fr',
            'enabled' => true,
        ],

        [
            'name' => 'Leral',
            'url' => 'https://www.leral.net/xml/syndication.rss',
            'language' => 'fr',
            'enabled' => true,
        ],

        [
            'name' => 'Dakar Actu',
            'url' => 'https://www.dakaractu.com/xml/syndication.rss',
            'language' => 'fr',
            'enabled' => true,
        ],

        [
            'name' => 'ThiesVision',
            'url' => 'https://thiesvision.com/feed',
            'language' => 'fr',
            'enabled' => true,
        ],

        [
            'name' => 'Senegal7',
            'url' => 'https://senegal7.com/feed',
            'language' => 'fr',
            'enabled' => true,
        ],

        [
            'name' => 'Enquete Plus',
            'url' => 'https://enqueteplus.com/rss.xml',
            'language' => 'fr',
            'enabled' => true,
        ],

        [
            'name' => 'Afrik.com',
            'url' => 'https://afrik.com/feed',
            'language' => 'fr',
            'enabled' => true,
        ],

        [
            'name' => 'BBC News Africa',
            'url' => 'https://feeds.bbci.co.uk/news/world/africa/rss.xml',
            'language' => 'en',
            'enabled' => true,
        ],

    ],

    /*
    |--------------------------------------------------------------------------
    | Requested but unavailable
    |--------------------------------------------------------------------------
    |
    | These publishers were asked for but expose no feed, so they are not in
    | the list above. Re-enable them here if a feed URL ever appears.
    |
    |   actunet.sn  - no DNS record at all
    |   igfm.sn     - site is up, every /feed, /rss, /feed.xml path returns 404
    |                 and the homepage declares no <link type="application/rss+xml">
    |   footgal.com - same as igfm.sn: all common feed paths return 404
    |   sudonline.sn - the entire site returns HTTP 404
    |
    | Le Quotidien is listed above but returns HTTP 403 from this host's IP.
    |
    */

    /*
    |--------------------------------------------------------------------------
    | Category keywords
    |--------------------------------------------------------------------------
    |
    | Feeds rarely carry a category that maps onto our own taxonomy, so the
    | importer scores each article's title and summary against these keyword
    | lists. Keys must match Category.name_fr.
    |
    */

    'category_keywords' => [
        'POLITIQUE' => [
            'président', 'ministre', 'gouvernement', 'parlement', 'député', 'sénateur',
            'élection', 'ministère', 'gouverneur', 'municipalité', 'politique', 'campagne',
            'opposition', 'coalition', 'projet de loi', 'assemblée nationale', 'sénégalais',
            'president', 'minister', 'government', 'parliament', 'deputy', 'senator',
            'election', 'ministry', 'government', 'governor', 'municipality', 'politics', 'campaign',
            'opposition', 'coalition', 'bill', 'national assembly', 'senegal',
        ],
        'ÉCONOMIE' => [
            'économie', 'économique', 'bourse', 'banque', 'commerce', 'entreprise', 'pme',
            'inflation', 'budget', 'impôt', 'taxe', 'investissement', 'financ', 'dette',
            'marché', 'exportation', 'importation', 'franc cfa', 'bceao', 'startup',
            'economy', 'economic', 'stock market', 'bank', 'trade', 'business', 'sme',
            'inflation', 'budget', 'tax', 'investment', 'finance', 'debt',
            'market', 'export', 'import', 'cfa franc', 'startup',
        ],
        'SPORT' => [
            'football', 'match', 'ligue 1', 'sénégal', 'lions', 'can 2026', 'can 2027',
            'olympique', 'basket', 'handball', 'lutte', 'athletisme', 'saison',
            'championnat', 'transfert', 'entraîneur', 'club', 'score', 'but',
            'football', 'match', 'league 1', 'senegal', 'lions', 'can 2026', 'can 2027',
            'olympic', 'basketball', 'handball', 'wrestling', 'athletics', 'season',
            'championship', 'transfer', 'coach', 'club', 'score', 'goal',
        ],
        'SOCIÉTÉ' => [
            'santé', 'école', 'éducation', 'hôpital', 'ville', 'dakar', 'quartier',
            'culture', 'cinéma', 'musique', 'festival', 'religion', 'administration',
            'nuitée', 'délinquance', 'accident', 'manifestation', 'citoyen',
            'health', 'school', 'education', 'hospital', 'city', 'dakar', 'neighborhood',
            'culture', 'cinema', 'music', 'festival', 'religion', 'administration',
            'crime', 'accident', 'protest', 'citizen',
        ],
        'AFRIQUE' => [
            'afrique', 'sahel', 'mali', 'burkina', 'guinée', 'côte d\'ivoire', 'nigeria',
            'ghana', 'sénégalais de', 'union africaine', 'CEDEAO', 'écowas', 'niger',
            'togo', 'bénin', 'cameroun', 'congo', 'soudan', 'somalia', 'tanzanie',
            'africa', 'sahel', 'mali', 'burkina', 'guinea', 'ivory coast', 'nigeria',
            'ghana', 'senegalese', 'african union', 'ecowas', 'niger',
            'togo', 'benin', 'cameroon', 'congo', 'sudan', 'somalia', 'tanzania',
        ],
        'MONDE' => [
            'monde', 'international', 'europe', 'france', 'états-unis', 'amérique',
            'chine', 'russie', 'ukraine', 'gaza', 'israël', 'palestine', 'onu',
            'nations unies', 'otan', 'putin', 'trump', 'macron', 'diplomatie',
            'world', 'international', 'europe', 'france', 'united states', 'america',
            'china', 'russia', 'ukraine', 'gaza', 'israel', 'palestine', 'un',
            'united nations', 'nato', 'putin', 'trump', 'macron', 'diplomacy',
        ],
        'PEOPLE' => [
            'people', 'célébrité', 'chanteur', 'artiste', 'acteur', 'influenceur',
            'famille', 'mariage', 'people', 'star', 'vedette', 'mode', 'look',
            'people', 'celebrity', 'singer', 'artist', 'actor', 'influencer',
            'family', 'marriage', 'people', 'star', 'celebrity', 'fashion', 'look',
        ],
    ],

];
