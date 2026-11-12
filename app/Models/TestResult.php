<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class TestResult extends Model
{
    protected $fillable = [
        'tester_id',
        'test_case_id',
        'result',
        'notes',
        'severity',
        'frequency',
        'device',
        'screenshot_path'
    ];
}
