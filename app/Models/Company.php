<?php

namespace App\Models;

use Illuminate\Support\Facades\Cache;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Company extends Model
{
    use HasFactory;

    protected $fillable = [
        'symbol',
        'name',
    ];

    public static function list(): array
    {
        return Cache::remember('companies', now()->addDay(), function () {
            return self::all()->pluck('name', 'symbol')->toArray();
        });
    }
}
