<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Category;

class CategorySeeder extends Seeder
{
    public function run(): void
    {
        $categories = [
            ['name' => 'Eloquent',           'slug' => 'eloquent',        'color' => '#f97316', 'icon' => 'database'],
            ['name' => 'Livewire',           'slug' => 'livewire',        'color' => '#8b5cf6', 'icon' => 'bolt'],
            ['name' => 'Blade',              'slug' => 'blade',           'color' => '#ef4444', 'icon' => 'code'],
            ['name' => 'Routing',            'slug' => 'routing',         'color' => '#3b82f6', 'icon' => 'arrows-right-left'],
            ['name' => 'Middleware',         'slug' => 'middleware',      'color' => '#a78bfa', 'icon' => 'shield'],
            ['name' => 'Queues & Jobs',      'slug' => 'queues',          'color' => '#06b6d4', 'icon' => 'queue-list'],
            ['name' => 'Notifications',      'slug' => 'notifications',   'color' => '#ec4899', 'icon' => 'bell'],
            ['name' => 'Broadcasting',       'slug' => 'broadcasting',    'color' => '#6366f1', 'icon' => 'signal'],
            ['name' => 'Sanctum',            'slug' => 'sanctum',         'color' => '#10b981', 'icon' => 'key'],
            ['name' => 'Passport',           'slug' => 'passport',        'color' => '#14b8a6', 'icon' => 'identification'],
            ['name' => 'API Resources',      'slug' => 'api-resources',   'color' => '#22c55e', 'icon' => 'server'],
            ['name' => 'Policies & Gates',   'slug' => 'policies',        'color' => '#f59e0b', 'icon' => 'lock-closed'],
            ['name' => 'Caching',            'slug' => 'caching',         'color' => '#84cc16', 'icon' => 'cpu-chip'],
            ['name' => 'Redis',              'slug' => 'redis',           'color' => '#ef4444', 'icon' => 'circle-stack'],
            ['name' => 'Horizon',            'slug' => 'horizon',         'color' => '#f97316', 'icon' => 'chart-bar'],
            ['name' => 'Deployment',         'slug' => 'deployment',      'color' => '#6366f1', 'icon' => 'rocket-launch'],
            ['name' => 'Testing',            'slug' => 'testing',         'color' => '#10b981', 'icon' => 'beaker'],
            ['name' => 'File Storage',       'slug' => 'storage',         'color' => '#8b5cf6', 'icon' => 'folder'],
        ];

        foreach ($categories as $i => $cat) {
            Category::create([...$cat, 'sort_order' => $i]);
        }
    }
}
