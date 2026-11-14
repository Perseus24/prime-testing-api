<?php

namespace App\Models;

use Illuminate\Foundation\Auth\User as Authenticatable;
use Laravel\Sanctum\HasApiTokens;

class Tester extends Authenticatable
{
    use HasApiTokens;
    protected $fillable = [
        'code',
        'name',
        'role',
        'lgu',
        'access_code'
    ];

    protected $hidden = ['access_code'];
    protected function casts(): array
    {
        return [
            'access_code' => 'hashed', 
            'submitted_at' => 'datetime'
            ];
    }

    public function results() {
        return $this->hasMany(TestResult::class);
    }
}
