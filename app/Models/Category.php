<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\SoftDeletes;

class Category extends Model
{
    use HasFactory;
    use SoftDeletes;

    protected $fillable = ['secretariat_id', 'name', 'slug', 'description'];

    public function secretariat(): BelongsTo
    {
        return $this->belongsTo(Secretariat::class);
    }

    public function serviceOrders(): HasMany
    {
        return $this->hasMany(ServiceOrder::class);
    }

    public function scopeSearch(Builder $query, string $search): Builder
    {
        $term = mb_strtolower(trim($search));

        if ($term === '') {
            return $query;
        }

        return $query->whereRaw('LOWER(name) LIKE ?', ["%{$term}%"]);
    }
}
