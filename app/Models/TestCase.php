<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class TestCase extends Model
{
    protected $fillable = [
        'code',
        'title',
        'module',
        'platform',
        'priority',
        'preconditions',
        'steps',
        'expected',
        'sort',
    ];

    protected function casts(): array
    {
        return [
            'preconditions' => 'array', 
            'steps' => 'array', 
            'expected' => 'array'
        ];
    }
}
