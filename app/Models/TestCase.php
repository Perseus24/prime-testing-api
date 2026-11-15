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

    public function results()
    {
        return $this->hasMany(TestResult::class);
    }

    public function numberOfSteps () {
        return count($this->steps);
    }
}
