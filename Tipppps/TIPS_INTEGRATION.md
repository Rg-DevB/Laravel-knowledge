# Intégration de la page Tips dans LaravelKnow

## 1. Ajouter la route dans routes/web.php

```php
// Dans le groupe public :
Route::get('/tips', \App\Livewire\Tips\TipsPage::class)->name('tips.index');

// Dans le groupe auth :
Route::get('/tips/create', \App\Livewire\Tips\CreateTip::class)->name('tips.create');
```

---

## 2. Mettre à jour la sidebar dans layouts/app.blade.php

Trouve le bloc `@php $navItems = [...]` et ajoute l'entrée Tips :

```php
@php
$navItems = [
    ['route' => 'home',           'label' => 'Home',            'icon' => 'home'],
    ['route' => 'problems.index', 'label' => 'Problems',        'icon' => 'bug'],
    ['route' => 'tips.index',     'label' => 'Tips & Upgrades', 'icon' => 'bolt', 'badge' => '24'],
    ['route' => 'problems.create','label' => 'New Issue',       'icon' => 'plus-circle', 'auth' => true],
    ['route' => 'dashboard',      'label' => 'Dashboard',       'icon' => 'chart', 'auth' => true],
];
@endphp
```

Et dans le template du nav-item, ajoute le support du badge :

```blade
<a href="{{ route($item['route']) }}"
   wire:navigate
   @class([...])>
    <x-icon :name="$item['icon']" class="w-4 h-4 flex-shrink-0" />
    {{ $item['label'] }}
    @if(!empty($item['badge']))
    <span class="ml-auto px-1.5 py-0.5 rounded-full text-[9px] font-bold bg-rose-500 text-white">
        {{ $item['badge'] }}
    </span>
    @endif
</a>
```

---

## 3. Placer les fichiers

```
TipsPage.php        → app/Livewire/Tips/TipsPage.php
tips-page.blade.php → resources/views/livewire/tips/tips-page.blade.php
```

---

## 4. Créer le dossier

```bash
mkdir -p app/Livewire/Tips
mkdir -p resources/views/livewire/tips
```

---

## 5. Lancer la découverte Livewire

```bash
php artisan livewire:discover
```

---

## ✅ C'est tout ! Visite /tips pour voir la page.
