<?php

namespace Database\Seeders;

use App\Models\Prestation;
use App\Models\Vertical;
use Illuminate\Database\Seeder;

class VerticalsTableSeeder extends Seeder
{
    public function run(): void
    {
        // ── 1. Créer la verticale beauty_salon ──
        $vertical = Vertical::create([
            'slug'                 => 'beauty_salon',
            'nom'                  => 'Institut Gladstone',
            'ville'                => 'Douala',
            'ouverture'            => '09:30:00',
            'fermeture'            => '20:00:00',
            'capacites_par_categorie' => [
                1 => 2, // Coiffure Haut de Gamme
                2 => 2, // Esthétique & Soins
                3 => 2, // Onglerie
                4 => 2, // Spa & Bien-Être
            ],
            'categories' => [
                1 => 'Coiffure Haut de Gamme',
                2 => 'Esthétique & Soins',
                3 => 'Onglerie',
                4 => 'Spa & Bien-Être',
            ],
            'devise'               => 'FCFA',
            'booking_api_secret'   => 'gladstone-beauty-2026-secret-key',
            'escalation_phone'     => '+237 6XX XXX XXX',
            'actif'                => true,
        ]);

        // ── 2. Catalogue des prestations ──
        // Format : [nom, prix, duree_minutes]
        // Les durées sont estimées — ajuste selon la réalité du salon

        $catalogue = [

            // ═══ CATÉGORIE 1 : Coiffure Haut de Gamme ═══
            ['Botox Vegan', '50 000', 60],
            ['Botox indien', '50 000', 60],
            ['Botox tanin', '50 000', 60],
            ['Botox à la Kératine', '50 000', 60],
            ['Soin hydratant', '15 000', 30],
            ['Soins restructurant ciblé', '35 000', 45],
            ['Soin detox dermo-capillaire', '5 000', 20],
            ['Soin personnel', '5 000', 20],
            ['Coupe européenne', '8 000 / 10 000', 30],
            ['Coiffure homme', '5 000', 20],
            ['Coiffure mariage', 'à partir de 50 000', 90],
            ['Chignon', '5 000 / 8 000 / 10 000', 45],
            ['Boucles anglaises', '10 000 / 15 000', 45],
            ['Brushing cheveux court', '8 000 / 10 000', 20],
            ['Brushing cheveux mi-long', '15 000 / 20 000', 30],
            ['Brushing cheveux long', '25 000', 40],
            ['Brushing + Lissage', '15 000 / 20 000 / 25 000', 45],
            ['Lissage brésilien', 'à partir de 70 000', 120],
            ['Lissage tanin', 'à partir de 70 000', 120],
            ['Lissage indien', 'à partir de 70 000', 120],
            ['Lissage vapeur cheveux court', '5 000', 30],
            ['Lissage vapeur cheveux long', '10 000', 45],
            ['Lissage vapeur sans shampooing', '5 000', 30],
            ['Lissage sans vapeur cheveux long', '5 000', 30],
            ['Lissage sans vapeur sans shampooing', '3 000', 20],
            ['Défrisage Jus For Me', '15 000', 60],
            ['Défrisage personnel', '10 000', 45],
            ['Défrisage Dark & Lovely', '25 000', 60],
            ['Défrisage Mizani', '35 000', 60],
            ['Défrisage Afirmé', '35 000', 60],
            ['Défrisage SJR', '40 000', 75],
            ['Coloration clients', 'à partir de 5 000', 45],
            ['Coloration permanente', '10 000 / 20 000', 60],
            ['Coloration temporaire', '20 000', 45],
            ['Coloration Blush', '20 000', 45],
            ['Décoloration', '10 000 / 20 000', 60],
            ['Shampoing classique', 'à partir de 3 000', 15],
            ['Shampoing spécifique', '5 000', 15],
            ['Shampoing colorant', '5 000', 20],
            ['Shampoing spécifique ciblé', '10 000', 20],
            ['Nattes simple', '2 000', 45],
            ['Nattes collées', '2 000', 60],
            ['Knotless', 'à partir de 15 000', 120],
            ['Tresses Boho', 'à partir de 15 000', 120],
            ['Tresses Boho Braids', '50 000', 180],
            ['Tresses Shont Boho Bob', '30 000', 120],
            ['Tresse Tarzan', '25 000', 90],
            ['Tresse coupe carré', '25 000', 90],
            ['Tresses Enfants', 'à partir de 4 000 / 5 000', 60],
            ['Crochet Braids', '25 000', 90],
            ['Defait Tresses', 'à partir de 2 000 / 3 000', 30],
            ['Retrait de tresses', '2 000', 20],
            ['Rasta gros', 'à partir de 10 000', 120],
            ['Rasta moyen', 'à partir de 20 000', 150],
            ['Rasta petit', 'à partir de 30 000', 180],
            ['Twist gros (Cheveux Naturels)', '10 000', 60],
            ['Twist gros sur cheveux naturels', 'à partir de 10 000', 60],
            ['Twist moyen', 'à partir de 10 000', 90],
            ['Twist petit', 'à partir de 30 000', 120],
            ['Twist avec meche Naturels', '20 000', 90],
            ['Tissage closure', '15 000', 60],
            ['Tissage frontal', '15 000', 60],
            ['Tissage fermé', '15 000 / 20 000', 60],
            ['Tissage ouvert', '10 000 / 15 000', 45],
            ['Tissage à la colle', '10 000', 45],
            ['Confection perruque closure', '30 000', 120],
            ['Confection perruque frontale', '40 000', 120],
            ['Pose perruque frontale', '15 000', 30],
            ['Passe-mèche américaine', 'à partir de 5 000', 30],
            ['Passe-mèche gros', '5 000', 20],
            ['Passe-mèche moyen', '10 000', 30],
            ['Passe-mèche petit', '15 000', 30],
                        // ═══ CATÉGORIE 2 : Esthétique & Soins ═══
            ['Coup d\'éclat', '25 000', 45],
            ['Soin du visage ciblé', '35 000', 45],
            ['Soin du visage approfondi ciblé', '40 000 / 50 000', 60],
            ['Gommage simple', '20 000', 30],
            ['Gommage éclat', '25 000', 30],
            ['Peel éclat', '40 000', 45],
            ['Peel anti-acne', '40 000 / 50 000', 45],
            ['Peel anti-age', '40 000 / 50 000', 45],
            ['Peel hyperpigmentation', '40 000 / 50 000', 45],
            ['Peel profond', '120 000', 60],
            ['Carbon peel', '35 000 / 40 000 / 50 000', 30],
            ['Carbon peel complet', '60 000 / 70 000', 45],
            ['Hydro clean', '50 000', 45],
            ['Hydro peel', '60 000 / 80 000', 45],
            ['Hydro need', '80 000', 60],
            ['Micro ciblé', '40 000', 30],
            ['Micro électroporation', '80 000', 45],
            ['Epilation sourcils à la cire', '4 000', 10],
            ['Epilation sourcils à la lame', '1 000', 5],
            ['Epilation sourcils à la pince', '2 000', 10],
            ['Coloration du sourcils', '10 000', 15],
            ['Epilation duvet', '3 000', 10],
            ['Epilation aisselles', '6 000', 15],
            ['Epilation menthon', '5 000', 15],
            ['Epilation nez', '7 000', 10],
            ['Epilation visage', '10 000', 20],
            ['Epilation demi-jambes', '10 000 / 15 000', 30],
            ['Epilation des jambes complète', '25 000 / 30 000', 45],
            ['Epilation bikini', '15 000 / 20 000', 20],
            ['Epilation maillot', '20 000 / 30 000', 30],
            ['Epilation ventre', '10 000 / 15 000', 15],
            ['Epilation torse', '10 000 / 15 000', 20],
            ['Epilation dos', '15 000', 20],
            ['Epilation Yona', '25 000', 30],
            ['Epilation corps', 'à partir de 100 000', 90],
            ['Pose cils classique', '10 000', 45],
            ['Pose cils classique privée', '5 000', 30],
            ['Extension cils à cils naturel', '25 000', 60],
            ['Extension cils hybride', '30 000', 60],
            ['Extension cils volume', '35 000', 75],
            ['Extension cils volume russe', '40 000', 75],
            ['Remplissage cils à cils', '20 000', 45],
            ['Dépose cils à cils', '5 000', 15],

            // ═══ CATÉGORIE 3 : Onglerie ═══
            ['Manucure sèche', '3 000', 15],
            ['Manucure', '5 000', 30],
            ['Hydra mains', '10 000', 30],
            ['Pédicure sèche', '8 000', 30],
            ['Pédicure', '10 000 / 15 000', 45],
            ['Pédicure vip + Manucure', '20 000', 60],
            ['Soin hydrapieds', '10 000', 30],
            ['Callus peeling (pedicure inclus)', '20 000 / 25 000', 45],
            ['Pose vernis classique OPI', '3 000', 20],
            ['Pose vernis classique OPI Infinite Shine', '4 000', 20],
            ['Pose vernis permanent', '8 000', 30],
            ['Pose vernis médical', '11 000', 30],
            ['Pose gel sur capsule classique', '22 000', 45],
            ['Pose gel sur capsule effet chrome', '25 000', 45],
            ['Pose gel sur capsule effet paillette', '26 000', 45],
            ['Pose gel sur capsule effet baby boomer', '26 000', 45],
            ['Pose gel sur capsule french manucure', '26 000', 45],
            ['Construction gel (chablon)', '26 000', 45],
            ['Remplissage gel classique', '21 000', 30],
            ['Gainage sur ongle court', '16 000', 30],
            ['Gainage sur ongle long', '16 000 / 18 000', 30],
            ['Effet french manucure', '3 000', 10],
            ['Effet chrome', '3 000', 10],
            ['Effet babyboomer', '3 000', 10],
            ['Effet mabre', '3 000', 10],
            ['Effet foil', '3 000', 10],
            ['Effet feuille d\'or', '3 000', 10],
            ['Effet cat eye', '3 000', 10],
            ['Effet matte', '3 000', 10],
            ['Effet strass', '3 000', 10],
            ['Effet hydro', '3 000', 10],
            ['Effet perle', '3 000', 10],

            // ═══ CATÉGORIE 4 : Spa & Bien-Être ═══
            ['Hammam simple', '10 000', 30],
            ['Hammam gommage + Gant', '35 000', 45],
            ['Hammam gommage au savon noir', '25 000', 45],
            ['Hammam gommage éclat intense', '30 000', 45],
            ['Hammam gommage enveloppement', '45 000', 60],
            ['Massage cible', '10 000', 20],
            ['Massage relaxant', '25 000', 45],
            ['Massage aux bougies', '30 000', 45],
            ['Massage thérapeutique', '30 000', 45],
            ['Massage aux Pierres Chaudes', '35 000', 60],
            ['Massage à quatre mains', '50 000', 60],
        ];

        // ── 3. Insérer les prestations ──
        foreach ($catalogue as $index => [$nom, $prix, $duree]) {
            // Déterminer la catégorie selon l'index
            $categorieId = match (true) {
                $index < 76  => 1, // Coiffure Haut de Gamme (76 items)
                $index < 122 => 2, // Esthétique & Soins (46 items)
                $index < 163 => 3, // Onglerie (41 items)
                default      => 4, // Spa & Bien-Être (11 items)
            };

            Prestation::create([
                'vertical_id'  => $vertical->id,
                'nom'          => $nom,
                'categorie_id' => $categorieId,
                'prix'         => $prix,
                'duree_minutes' => $duree,
            ]);
        }

        $this->command->info('Verticale beauty_salon créée avec ' . count($catalogue) . ' prestations.');
    }
}