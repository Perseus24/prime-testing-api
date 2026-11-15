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
        'position',
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

    public static function generateAccessCode(int $length = 8): string
    {
        $alphabet = 'ABCDEFGHJKLMNPQRSTUVWXYZ23456789'; // no 0/O, 1/I
        $max = strlen($alphabet) - 1;

        $code = '';
        for ($i = 0; $i < $length; $i++) {
            $code .= $alphabet[random_int(0, $max)];
        }

        return $code;
    }
}
