<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

final class SiteStatistic extends Model
{
    use HasFactory;

    public const MAX_ITEMS = 4;

    protected $fillable = [
        'value',
        'label',
        'sort_order',
    ];

    protected $casts = [
        'sort_order' => 'integer',
    ];
}
