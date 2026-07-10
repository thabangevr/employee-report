<?php

declare(strict_types=1);

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('submission_areas', function (Blueprint $table) {
            $table->id();
            $table->foreignId('weekly_submission_id')->constrained()->cascadeOnDelete();
            $table->foreignId('manager_area_id')->nullable()->constrained()->nullOnDelete();
            $table->string('name');
            $table->string('status')->nullable();
            $table->text('status_justification')->nullable();
            $table->tinyInteger('sort_order')->default(0);
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('submission_areas');
    }
};
