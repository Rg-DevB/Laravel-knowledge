<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class CodeSnippet extends Model
{
    //
     protected $fillable = ['solution_id', 'language', 'label', 'code', 'sort_order'];
 
    public function solution(): BelongsTo
    {
        return $this->belongsTo(Solution::class);
    }
 
    public function getHighlightedCodeAttribute(): string
    {
        // Integrate with highlight.php or use JS-side Prism.js/Shiki
        return $this->code;
    }
 
    // Supported languages with display labels
    public static function languages(): array
    {
        return [
            'php'        => 'PHP',
            'blade'      => 'Blade',
            'livewire'   => 'Livewire',
            'javascript' => 'JavaScript',
            'sql'        => 'SQL',
            'bash'       => 'Bash / Shell',
            'json'       => 'JSON',
            'yaml'       => 'YAML',
            'env'        => '.env',
        ];
    }
}
