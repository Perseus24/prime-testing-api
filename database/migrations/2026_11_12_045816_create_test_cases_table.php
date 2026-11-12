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
        Schema::create('test_cases', function (Blueprint $table) {
            $table->id();
            $table->string('code')->unique();          // TC-04
            $table->string('title');
            $table->string('module')->nullable();
            $table->enum('platform', ['web', 'mobile']);
            $table->enum('priority', ['low', 'medium', 'high'])->default('medium');
            $table->json('preconditions');
            $table->json('steps');
            $table->json('expected');
            $table->unsignedSmallInteger('sort')->default(0);
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('test_cases');
    }
};
