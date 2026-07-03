<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

final class SiteStatistic extends Model
{
    use HasFactory;

    protected $fillable = [
        'value',
        'label',
        'description',
        'sort_order',
    ];
}
