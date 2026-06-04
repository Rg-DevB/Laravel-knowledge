<?php

namespace Database\Seeders;

use App\Models\Badge;
use Illuminate\Database\Seeder;

class BadgeSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $badges = [
            // Badges de participation
            [
                'name' => 'Premier problème',
                'slug' => 'first-problem',
                'description' => 'A publié son premier problème',
                'icon' => 'fas fa-question-circle',
                'type' => 'bronze',
                'requirement' => 1,
                'active' => true,
            ],
            [
                'name' => 'Première solution',
                'slug' => 'first-solution',
                'description' => 'A publié sa première solution',
                'icon' => 'fas fa-code',
                'type' => 'bronze',
                'requirement' => 1,
                'active' => true,
            ],
            [
                'name' => 'Première meilleure solution',
                'slug' => 'first-best-solution',
                'description' => 'A obtenu sa première solution marquée comme meilleure',
                'icon' => 'fas fa-check-double',
                'type' => 'silver',
                'requirement' => 1,
                'active' => true,
            ],
            [
                'name' => 'Contributeur',
                'slug' => 'contributor',
                'description' => 'A atteint 100 points de réputation',
                'icon' => 'fas fa-star',
                'type' => 'bronze',
                'requirement' => 100,
                'active' => true,
            ],
            
            // Badges intermédiaires
            [
                'name' => 'Créateur de problèmes',
                'slug' => 'problem-creator',
                'description' => 'A publié 10 problèmes',
                'icon' => 'fas fa-lightbulb',
                'type' => 'silver',
                'requirement' => 10,
                'active' => true,
            ],
            [
                'name' => 'Résolveur',
                'slug' => 'solver',
                'description' => 'A publié 25 solutions',
                'icon' => 'fas fa-check-circle',
                'type' => 'silver',
                'requirement' => 25,
                'active' => true,
            ],
            [
                'name' => 'Membre actif',
                'slug' => 'active-member',
                'description' => 'A atteint 1000 points de réputation',
                'icon' => 'fas fa-medal',
                'type' => 'silver',
                'requirement' => 1000,
                'active' => true,
            ],
            
            // Badges avancés
            [
                'name' => 'Maître des problèmes',
                'slug' => 'problem-master',
                'description' => 'A publié 50 problèmes',
                'icon' => 'fas fa-crown',
                'type' => 'gold',
                'requirement' => 50,
                'active' => true,
            ],
            [
                'name' => 'Expert en solutions',
                'slug' => 'solution-expert',
                'description' => 'A publié 100 solutions',
                'icon' => 'fas fa-trophy',
                'type' => 'gold',
                'requirement' => 100,
                'active' => true,
            ],
            [
                'name' => 'Expert',
                'slug' => 'expert',
                'description' => 'A atteint 5000 points de réputation',
                'icon' => 'fas fa-gem',
                'type' => 'gold',
                'requirement' => 5000,
                'active' => true,
            ],
            
            // Badge ultime
            [
                'name' => 'Légende',
                'slug' => 'legend',
                'description' => 'A atteint 10000 points de réputation',
                'icon' => 'fas fa-infinity',
                'type' => 'platinum',
                'requirement' => 10000,
                'active' => true,
            ],
        ];

        foreach ($badges as $badge) {
            Badge::updateOrCreate(
                ['slug' => $badge['slug']],
                $badge
            );
        }
    }
}
