<?php

namespace App\Livewire\Tips;

use Livewire\Component;
use Livewire\Attributes\{Computed, Url};

class TipsPage extends Component
{
    #[Url(as: 'cat')]
    public string $category = 'all';

    #[Url(as: 'diff')]
    public string $difficulty = 'all';

    #[Url(as: 'q')]
    public string $search = '';

    public array $readTips = [];      // IDs des tips lus (stockés en session)
    public ?int  $openTip  = null;    // ID du tip actuellement ouvert

    public function mount(): void
    {
        // Restaurer les tips lus depuis la session
        $this->readTips = session('read_tips', []);
    }

    public function toggleTip(int $id): void
    {
        $this->openTip = ($this->openTip === $id) ? null : $id;
    }

    public function markRead(int $id): void
    {
        if (!in_array($id, $this->readTips)) {
            $this->readTips[] = $id;
            session(['read_tips' => $this->readTips]);
        }
    }

    public function markUnread(int $id): void
    {
        $this->readTips = array_values(array_filter($this->readTips, fn($t) => $t !== $id));
        session(['read_tips' => $this->readTips]);
    }

    #[Computed]
    public function tips(): array
    {
        return collect($this->allTips())
            ->when($this->category !== 'all', fn($c) => $c->where('category', $this->category))
            ->when($this->difficulty !== 'all', fn($c) => $c->where('difficulty', $this->difficulty))
            ->when($this->search, fn($c) => $c->filter(fn($t) =>
                str_contains(strtolower($t['title']), strtolower($this->search)) ||
                str_contains(strtolower($t['why']), strtolower($this->search))
            ))
            ->values()
            ->toArray();
    }

    #[Computed]
    public function readCount(): int
    {
        return count($this->readTips);
    }

    #[Computed]
    public function totalCount(): int
    {
        return count($this->allTips());
    }

    #[Computed]
    public function progressPercent(): int
    {
        if ($this->totalCount === 0) return 0;
        return (int) round(($this->readCount / $this->totalCount) * 100);
    }

    #[Computed]
    public function progressBadge(): string
    {
        return match (true) {
            $this->readCount >= 20 => '🏆 Tip Master',
            $this->readCount >= 10 => '🧠 Senior Mindset',
            $this->readCount >= 5  => '📈 En progression',
            default                => '🆕 Newcomer',
        };
    }

    #[Computed]
    public function categories(): array
    {
        return [
            'all'          => ['label' => 'Tous',         'color' => 'zinc'],
            'eloquent'     => ['label' => 'Eloquent',      'color' => 'orange'],
            'performance'  => ['label' => 'Performance',   'color' => 'amber'],
            'security'     => ['label' => 'Security',      'color' => 'blue'],
            'livewire'     => ['label' => 'Livewire',      'color' => 'violet'],
            'architecture' => ['label' => 'Architecture',  'color' => 'emerald'],
            'blade'        => ['label' => 'Blade',         'color' => 'red'],
            'testing'      => ['label' => 'Testing',       'color' => 'green'],
        ];
    }

    // ── All tips data ─────────────────────────────────────────

    private function allTips(): array
    {
        return [
            [
                'id'         => 1,
                'category'   => 'eloquent',
                'title'      => 'N+1 Query — Eager Loading avec with()',
                'difficulty' => 'easy',
                'why'        => 'Sans eager loading, Laravel exécute 1 requête par relation pour chaque enregistrement. Avec 100 posts, tu passes de 1 requête à 101. En production, c\'est la principale cause de lenteur.',
                'junior'     => [
                    'label'   => 'Chaque accès déclenche une requête',
                    'lang'    => 'php',
                    'code'    => <<<'CODE'
// ❌ N+1 : 1 + N requêtes SQL
$posts = Post::all();

foreach ($posts as $post) {
    // 1 requête SQL PAR post !
    echo $post->user->name;
    echo $post->category->title;
}
CODE,
                ],
                'senior'     => [
                    'label'   => '2 requêtes quelle que soit la taille',
                    'lang'    => 'php',
                    'code'    => <<<'CODE'
// ✅ Eager loading : 2 requêtes total
$posts = Post::with(['user', 'category'])
    ->get();

foreach ($posts as $post) {
    // Aucune requête supplémentaire
    echo $post->user->name;
    echo $post->category->title;
}
CODE,
                ],
                'pros'       => [
                    ['type' => 'bad',  'text' => '101 requêtes pour 100 posts'],
                    ['type' => 'good', 'text' => '2 requêtes peu importe le volume'],
                    ['type' => 'bad',  'text' => 'Timeout en prod dès 500+ records'],
                    ['type' => 'good', 'text' => 'Utilise withCount() pour les compteurs'],
                ],
            ],
            [
                'id'         => 2,
                'category'   => 'security',
                'title'      => 'Mass Assignment — Fillable vs Guarded',
                'difficulty' => 'medium',
                'why'        => 'Sans protection, un attaquant peut envoyer role=admin dans un formulaire et devenir admin. C\'est une faille critique très courante.',
                'junior'     => [
                    'label' => 'Tous les champs sont accessibles',
                    'lang'  => 'php',
                    'code'  => <<<'CODE'
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
                ],
                'senior'     => [
                    'label' => 'Whitelist explicite des champs',
                    'lang'  => 'php',
                    'code'  => <<<'CODE'
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
                ],
                'pros'       => [],
            ],
            [
                'id'         => 3,
                'category'   => 'eloquent',
                'title'      => 'Local Scopes — Requêtes nommées et réutilisables',
                'difficulty' => 'easy',
                'why'        => 'Copier-coller des conditions WHERE dans chaque controller crée de la duplication difficile à maintenir. Un scope = une seule source de vérité, testable, chainable.',
                'junior'     => [
                    'label' => 'Conditions répétées partout',
                    'lang'  => 'php',
                    'code'  => <<<'CODE'
// ❌ Copié dans chaque controller
$posts = Post::where('published', true)
    ->where('published_at', '<=', now())
    ->get();

// Encore dans un autre fichier...
$featured = Post::where('published', true)
    ->where('is_featured', true)
    ->get();
CODE,
                ],
                'senior'     => [
                    'label' => 'Scopes nommés et chainables',
                    'lang'  => 'php',
                    'code'  => <<<'CODE'
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
                ],
                'pros'       => [],
            ],
            [
                'id'         => 4,
                'category'   => 'livewire',
                'title'      => 'Computed Properties vs Méthodes directes',
                'difficulty' => 'hard',
                'why'        => 'Sans #[Computed], chaque accès à $this->results dans la vue re-exécute la requête SQL. Avec #[Computed], la valeur est calculée une seule fois et mise en cache pour le render.',
                'junior'     => [
                    'label' => 'Requête SQL à chaque accès dans la vue',
                    'lang'  => 'php',
                    'code'  => <<<'CODE'
// ❌ Méthode normale = recalcul
class SearchResults extends Component
{
    public string $query = '';

    public function getResults()
    {
        return Post::search($this->query)
            ->get(); // Appelé N fois !
    }
}
// Dans la vue : $this->getResults()
// à chaque affichage = N requêtes
CODE,
                ],
                'senior'     => [
                    'label' => 'Calculé une fois, mis en cache',
                    'lang'  => 'php',
                    'code'  => <<<'CODE'
// ✅ Computed = 1 seul calcul/render
class SearchResults extends Component
{
    public string $query = '';

    #[Computed]
    public function results()
    {
        return Post::search($this->query)
            ->get();
    }
}
// Dans la vue : $this->results
// calculé une seule fois par render
CODE,
                ],
                'pros'       => [],
            ],
            [
                'id'         => 5,
                'category'   => 'architecture',
                'title'      => 'Service Classes — Sortir la logique des Controllers',
                'difficulty' => 'hard',
                'why'        => 'Un controller "fat" est impossible à tester, difficile à relire, et brise le principe SRP. Une Service Class est testable unitairement et réutilisable depuis n\'importe où.',
                'junior'     => [
                    'label' => 'Logique métier dans le Controller',
                    'lang'  => 'php',
                    'code'  => <<<'CODE'
// ❌ Controller de 200 lignes
public function store(Request $request)
{
    $user = User::create([...]);
    // Upload avatar
    // Envoyer email de bienvenue
    // Créer la subscription
    // Log analytics
    // Notifier les admins
    // ...
    return redirect('/');
}
CODE,
                ],
                'senior'     => [
                    'label' => 'Controller mince + Service dédiée',
                    'lang'  => 'php',
                    'code'  => <<<'CODE'
// ✅ Controller = orchestrateur
public function store(
    StoreUserRequest $request,
    UserRegistrationService $service
) {
    $user = $service->register(
        $request->validated()
    );

    return redirect(route('dashboard'));
}
// UserRegistrationService est testable !
CODE,
                ],
                'pros'       => [],
            ],
            [
                'id'         => 6,
                'category'   => 'security',
                'title'      => 'Authorization — Policies vs if($user->id)',
                'difficulty' => 'medium',
                'why'        => 'Vérifier les permissions directement dans les controllers ou les vues avec des conditions manuelles est fragile, non centralisé, et impossible à auditer.',
                'junior'     => [
                    'label' => 'Vérification manuelle dispersée',
                    'lang'  => 'php',
                    'code'  => <<<'CODE'
// ❌ Conditions dispersées partout
public function edit(Post $post)
{
    if (auth()->id() !== $post->user_id) {
        abort(403);
    }
    // ...
}

// Et dans la vue Blade :
@if(auth()->id() === $post->user_id)
    <a href="edit">Éditer</a>
@endif
CODE,
                ],
                'senior'     => [
                    'label' => 'Policy centralisée et réutilisable',
                    'lang'  => 'php',
                    'code'  => <<<'CODE'
// ✅ PostPolicy::update()
public function update(User $u, Post $p)
{
    return $u->id === $p->user_id
        || $u->isAdmin();
}

// Controller :
$this->authorize('update', $post);

// Blade :
@can('update', $post)
    <a href="edit">Éditer</a>
@endcan
CODE,
                ],
                'pros'       => [],
            ],
            [
                'id'         => 7,
                'category'   => 'performance',
                'title'      => 'select() — Ne récupère que ce dont tu as besoin',
                'difficulty' => 'easy',
                'why'        => 'SELECT * charge tous les champs en mémoire, y compris les blobs, les textes longs, et les colonnes inutiles. Un select() ciblé réduit l\'usage mémoire et accélère la sérialisation.',
                'junior'     => [
                    'label' => 'Tous les champs chargés inutilement',
                    'lang'  => 'php',
                    'code'  => <<<'CODE'
// ❌ SELECT * — tout en mémoire
// (inclut body, metadata, etc.)
$users = User::all();

foreach ($users as $user) {
    // On utilise seulement le nom
    // mais on a chargé 20 colonnes !
    echo $user->name;
}
CODE,
                ],
                'senior'     => [
                    'label' => 'Seulement les colonnes nécessaires',
                    'lang'  => 'php',
                    'code'  => <<<'CODE'
// ✅ SELECT ciblé
$users = User::select('id', 'name', 'email')
    ->get();

// Pour les listes/dropdowns :
$users = User::pluck('name', 'id');
// ['1' => 'Alice', '2' => 'Bob']

// Pour les counts :
$count = User::count(); // pas de SELECT *
CODE,
                ],
                'pros'       => [],
            ],
            [
                'id'         => 8,
                'category'   => 'blade',
                'title'      => 'Composants Blade — Évite les @include répétitifs',
                'difficulty' => 'easy',
                'why'        => 'Les @include avec des arrays de données sont lourds à lire et cassent quand les données changent. Les composants Blade ont une API claire avec des props typées et autocompletées.',
                'junior'     => [
                    'label' => '@include avec array de données',
                    'lang'  => 'php',
                    'code'  => <<<'CODE'
{{-- ❌ @include avec données --}}
@include('partials.alert', [
    'type'    => 'success',
    'message' => 'Sauvegardé !',
    'icon'    => true,
])

{{-- Aucune validation des props --}}
{{-- Pas d'autocomplétion IDE --}}
CODE,
                ],
                'senior'     => [
                    'label' => 'Composant Blade avec props claires',
                    'lang'  => 'php',
                    'code'  => <<<'CODE'
{{-- ✅ Composant Blade --}}
<x-alert
    type="success"
    message="Sauvegardé !"
    :icon="true"
/>

{{-- resources/views/components/alert.blade.php --}}
@props(['type', 'message', 'icon' => false])
<div class="alert alert-{{ $type }}">
    @if($icon) <x-icon /> @endif
    {{ $message }}
</div>
CODE,
                ],
                'pros'       => [],
            ],
            [
                'id'         => 9,
                'category'   => 'testing',
                'title'      => 'Tests — Feature Tests vs Tests manuels Postman',
                'difficulty' => 'medium',
                'why'        => 'Tester manuellement chaque endpoint après chaque changement est lent, oubliable, et ne scale pas. Les Feature Tests Laravel s\'exécutent en secondes et protègent les régressions.',
                'junior'     => [
                    'label' => 'Tests manuels dans Postman',
                    'lang'  => 'php',
                    'code'  => <<<'CODE'
// ❌ "Je teste manuellement
// avec Postman après chaque modif"

// Problèmes :
// - Oublié quand on est pressé
// - Pas reproductible par un collègue
// - Ne couvre pas les edge cases
// - Aucune régression détectée
// - 0 documentation du comportement
CODE,
                ],
                'senior'     => [
                    'label' => 'Feature Tests automatisés',
                    'lang'  => 'php',
                    'code'  => <<<'CODE'
// ✅ Test reproductible et documenté
public function test_user_can_create_post()
{
    $user = User::factory()->create();

    $response = $this->actingAs($user)
        ->postJson('/api/posts', [
            'title'   => 'Mon tip Laravel',
            'content' => 'Contenu...',
        ]);

    $response->assertCreated()
        ->assertJsonPath('data.title',
            'Mon tip Laravel');

    $this->assertDatabaseHas('posts',
        ['user_id' => $user->id]);
}
CODE,
                ],
                'pros'       => [],
            ],
            [
                'id'         => 10,
                'category'   => 'architecture',
                'title'      => 'Form Requests — Valide hors du Controller',
                'difficulty' => 'easy',
                'why'        => 'La validation inline dans les controllers alourdit le code, n\'est pas réutilisable, et mélange la logique de validation avec la logique métier.',
                'junior'     => [
                    'label' => 'Validation inline dans le Controller',
                    'lang'  => 'php',
                    'code'  => <<<'CODE'
// ❌ Validation mélangée au controller
public function store(Request $request)
{
    $validated = $request->validate([
        'title'    => 'required|min:5|max:200',
        'body'     => 'required|min:20',
        'category' => 'required|exists:cats,id',
        'tags'     => 'array|max:5',
        // 10 autres règles...
    ]);

    Post::create($validated);
}
CODE,
                ],
                'senior'     => [
                    'label' => 'Form Request dédié et réutilisable',
                    'lang'  => 'php',
                    'code'  => <<<'CODE'
// ✅ php artisan make:request StorePostRequest
class StorePostRequest extends FormRequest
{
    public function rules(): array
    {
        return [
            'title'    => 'required|min:5|max:200',
            'body'     => 'required|min:20',
            'category' => 'required|exists:cats,id',
        ];
    }
}

// Controller ultra-lisible :
public function store(StorePostRequest $req)
{
    Post::create($req->validated());
}
CODE,
                ],
                'pros'       => [],
            ],
        ];
    }

    public function render()
    {
        return view('livewire.tips.tips-page')
            ->layout('layouts.app', ['title' => 'Tips Junior → Senior — LaravelKnow']);
    }
}
