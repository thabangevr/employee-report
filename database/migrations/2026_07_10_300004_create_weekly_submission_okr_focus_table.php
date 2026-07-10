<?php

declare(strict_types=1);

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('weekly_submission_okr_focus', function (Blueprint $table) {
            $table->id();
            $table->foreignId('weekly_submission_id')->constrained()->cascadeOnDelete();
            $table->foreignId('okr_id')->constrained()->cascadeOnDelete();

            $table->unique(['weekly_submission_id', 'okr_id']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('weekly_submission_okr_focus');
    }
};
