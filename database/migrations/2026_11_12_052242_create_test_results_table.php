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
        Schema::create('test_results', function (Blueprint $table) {
            $table->id();
            $table->foreignId('tester_id')->constrained()->cascadeOnDelete();
            $table->foreignId('test_case_id')->constrained()->cascadeOnDelete();
            $table->enum('result', ['pass', 'fail', 'blocked']);
            $table->text('notes')->nullable();
            $table->enum('severity', ['minor', 'major', 'critical'])->nullable();
            $table->enum('frequency', ['every', 'sometimes', 'once'])->nullable();
            $table->string('device')->nullable();
            $table->string('screenshot_path')->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('test_results');
    }
};
