<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class ServiceOrder extends Model
{
    use SoftDeletes;

    protected $fillable = [
        'code',
        'title',
        'location',
        'observation',
        'due_date',
        'is_urgent',
        'status',
        'secretariat_id',
        'category_id'
    ];

    public function secretariat(): BelongsTo
    {
        return $this->belongsTo(Secretariat::class);
    }
    public function category(): BelongsTo
    {
        return $this->belongsTo(Category::class);
    }

    /**
     * Gera o código sequencial ODS-001, ODS-002...
     */
    public static function generateCode(): string
    {
        $lastId = self::withTrashed()->max('id') ?? 0;
        return 'ODS-' . str_pad($lastId + 1, 3, '0', STR_PAD_LEFT);
    }
}
