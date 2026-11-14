<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('testers', function (Blueprint $table) {
            $table->id();
            $table->string('code')->unique();       
            $table->string('name');
            $table->string('lgu');
            $table->enum('role', ['admin', 'tester'])->default('tester');
            $table->string('access_code');        
            $table->timestamp('submitted_at')->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('testers');
    }
};
