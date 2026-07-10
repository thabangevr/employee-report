<?php

declare(strict_types=1);

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('submission_cross_team_actions', function (Blueprint $table) {
            $table->id();
            $table->foreignId('weekly_submission_id')->constrained()->cascadeOnDelete();
            $table->string('owner_name');
            $table->text('ask');
            $table->tinyInteger('sort_order')->default(0);
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('submission_cross_team_actions');
    }
};
