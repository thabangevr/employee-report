<?php

declare(strict_types=1);

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('submission_flags', function (Blueprint $table) {
            $table->id();
            $table->foreignId('weekly_submission_id')->constrained()->cascadeOnDelete();
            $table->text('risk');
            $table->text('cause');
            $table->text('consequence');
            $table->tinyInteger('sort_order')->default(0);
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('submission_flags');
    }
};
