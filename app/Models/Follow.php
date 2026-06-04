<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Follow extends Model
{
    protected $fillable = ['user_id', 'followable_id', 'followable_type'];

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function followable()
    {
        return $this->morphTo();
    }
}
