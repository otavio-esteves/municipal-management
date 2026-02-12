<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Secretariat extends Model
{
    use SoftDeletes;
    
    protected $fillable = ['name', 'slug', 'description'];

    public function categories(): HasMany
    {
        return $this->hasMany(Category::class);
    }
}
