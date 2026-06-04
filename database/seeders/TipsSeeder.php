<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Tip;
use App\Models\User;

class TipsSeeder extends Seeder
{
    public function run(): void
    {
        $admin = User::where('role', 'admin')->first() ?? User::factory()->create(['role' => 'admin', 'name' => 'Admin']);

        $tips = [
            [
                'category'   => 'eloquent',
                'title'      => 'N+1 Query — Eager Loading avec with()',
                'difficulty' => 'easy',
                'why'        => 'Sans eager loading, Laravel exécute 1 requête par relation pour chaque enregistrement. Avec 100 posts, tu passes de 1 requête à 101. En production, c\'est la principale cause de lenteur.',
                'junior_label' => 'Chaque accès déclenche une requête',
                'junior_code'  => <<<'CODE'
// ❌ N+1 : 1 + N requêtes SQL
$posts = Post::all();

foreach ($posts as $post) {
    // 1 requête SQL PAR post !
    echo $post->user->name;
    echo $post->category->title;
}
CODE,
                'senior_label' => '2 requêtes quelle que soit la taille',
                'senior_code'  => <<<'CODE'
// ✅ Eager loading : 2 requêtes total
$posts = Post::with(['user', 'category'])
    ->get();

foreach ($posts as $post) {
    // Aucune requête supplémentaire
    echo $post->user->name;
    echo $post->category->title;
}
CODE,
                'pros' => [
                    ['type' => 'bad',  'text' => '101 requêtes pour 100 posts'],
                    ['type' => 'good', 'text' => '2 requêtes peu importe le volume'],
                    ['type' => 'bad',  'text' => 'Timeout en prod dès 500+ records'],
                    ['type' => 'good', 'text' => 'Utilise withCount() pour les compteurs'],
                ],
            ],
            [
                'category'   => 'security',
                'title'      => 'Mass Assignment — Fillable vs Guarded',
                'difficulty' => 'medium',
                'why'        => 'Sans protection, un attaquant peut envoyer role=admin dans un formulaire et devenir admin. C\'est une faille critique très courante.',
                'junior_label' => 'Tous les champs sont accessibles',
                'junior_code'  => <<<'CODE'
// ❌ Dangereux : aucune protection
class User extends Model
{
    // Pas de $fillable défini
    // N'importe quel champ peut
    // être écrit via create()
}

// En controller :
User::create($request->all());
// role=admin passera !
CODE,
                'senior_label' => 'Whitelist explicite des champs',
                'senior_code'  => <<<'CODE'
// ✅ Whitelist explicite
class User extends Model
{
    protected $fillable = [
        'name', 'email', 'password',
    ]; // 'role' jamais inclus !
}

// En controller :
$request->validate([...]);
User::create($request->validated());
CODE,
                'pros' => [],
            ],
            [
                'category'   => 'eloquent',
                'title'      => 'Local Scopes — Requêtes nommées et réutilisables',
                'difficulty' => 'easy',
                'why'        => 'Copier-coller des conditions WHERE dans chaque controller crée de la duplication difficile à maintenir. Un scope = une seule source de vérité, testable, chainable.',
                'junior_label' => 'Conditions répétées partout',
                'junior_code'  => <<<'CODE'
// ❌ Copié dans chaque controller
$posts = Post::where('published', true)
    ->where('published_at', '<=', now())
    ->get();

// Encore dans un autre fichier...
$featured = Post::where('published', true)
    ->where('is_featured', true)
    ->get();
CODE,
                'senior_label' => 'Scopes nommés et chainables',
                'senior_code'  => <<<'CODE'
// ✅ Dans le Model Post
public function scopePublished($query)
{
    return $query
        ->where('published', true)
        ->where('published_at', '<=', now());
}

// Utilisation limpide partout :
Post::published()->get();
Post::published()->featured()->get();
CODE,
                'pros' => [],
            ],
        ];

        foreach ($tips as $tipData) {
            Tip::create(array_merge($tipData, [
                'user_id' => $admin->id,
                'is_approved' => true,
            ]));
        }
    }
}
