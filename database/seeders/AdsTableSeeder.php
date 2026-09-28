<?php

namespace Database\Seeders;

use App\Models\Ad;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class AdsTableSeeder extends Seeder
{
    use WithoutModelEvents;

    public function run(): void
    {
        $ads = [
            [
                'title' => 'SunuBank - Votre Banque Numérique',
                'description' => 'Ouvrez votre compte en ligne en quelques clics',
                'image' => 'ads/sunubank-header.jpg',
                'html_snippet' => null,
                'target_url' => 'https://sunubank.sn',
                'zone' => 'header',
                'is_active' => true,
                'order' => 1,
            ],
            [
                'title' => 'Offre Spéciale - Téléphonie Orange',
                'description' => 'Forfaits illimités à prix réduit',
                'image' => 'ads/orange-sidebar.jpg',
                'html_snippet' => null,
                'target_url' => 'https://orange.sn',
                'zone' => 'sidebar',
                'is_active' => true,
                'order' => 1,
            ],
            [
                'title' => 'Immobilier - Trouvez votre maison',
                'description' => 'Annonces immobilières au Sénégal',
                'image' => null,
                'html_snippet' => '<div class="inline-ad"><h3>Immobilier</h3><p>Trouvez la maison de vos rêves</p></div>',
                'target_url' => 'https://immobilier.sn',
                'zone' => 'inline',
                'is_active' => true,
                'order' => 2,
            ],
            [
                'title' => 'SunuNews Partenaire Officiel',
                'description' => 'Merci à nos partenaires institutionnels',
                'image' => 'ads/partner-footer.jpg',
                'html_snippet' => null,
                'target_url' => 'https://partenaire.sn',
                'zone' => 'footer',
                'is_active' => true,
                'order' => 1,
            ],
            [
                'title' => 'Auto Garage - Vente et Réparation',
                'description' => 'Services automobiles professionnels',
                'image' => 'ads/garage-sidebar.jpg',
                'html_snippet' => null,
                'target_url' => 'https://garage.sn',
                'zone' => 'sidebar',
                'is_active' => false,
                'order' => 2,
            ],
        ];

        foreach ($ads as $adData) {
            Ad::create($adData);
        }
    }
}
