<?php

declare(strict_types=1);

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('okrs', function (Blueprint $table) {
            $table->text('objective_description')->nullable()->after('title');
            $table->text('measure_of_success')->nullable()->after('objective_description');
            $table->unsignedTinyInteger('weight')->default(0)->after('measure_of_success');
        });
    }

    public function down(): void
    {
        Schema::table('okrs', function (Blueprint $table) {
            $table->dropColumn(['objective_description', 'measure_of_success', 'weight']);
        });
    }
};
