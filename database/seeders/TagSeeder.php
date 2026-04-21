<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Tag;
use Illuminate\Support\Str;

class TagSeeder extends Seeder
{
    public function run(): void
    {
        $tags = [
            ['name' => 'n+1',           'color' => '#f97316'],
            ['name' => 'migration',     'color' => '#3b82f6'],
            ['name' => 'auth',          'color' => '#10b981'],
            ['name' => 'cors',          'color' => '#ef4444'],
            ['name' => 'config',        'color' => '#8b5cf6'],
            ['name' => 'performance',   'color' => '#f59e0b'],
            ['name' => 'testing',       'color' => '#84cc16'],
            ['name' => 'docker',        'color' => '#06b6d4'],
            ['name' => 'forge',         'color' => '#6366f1'],
            ['name' => 'vapor',         'color' => '#a78bfa'],
            ['name' => 'relationships', 'color' => '#f97316'],
            ['name' => 'scopes',        'color' => '#3b82f6'],
            ['name' => 'validation',    'color' => '#ec4899'],
            ['name' => 'rate-limiting', 'color' => '#f43f5e'],
            ['name' => 'pagination',    'color' => '#14b8a6'],
        ];

        foreach ($tags as $tag) {
            Tag::create([
                'name' => $tag['name'],
                'slug' => Str::slug($tag['name']),
                'color' => $tag['color'],
                'usage_count' => 0,
            ]);
        }
    }
}
