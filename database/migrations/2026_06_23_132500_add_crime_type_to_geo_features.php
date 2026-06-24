<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('points', function (Blueprint $table) {
            $table->string('crime_type')->nullable()->after('description');
        });
        Schema::table('polylines', function (Blueprint $table) {
            $table->string('crime_type')->nullable()->after('description');
        });
        Schema::table('polygons', function (Blueprint $table) {
            $table->string('crime_type')->nullable()->after('description');
        });
    }

    public function down(): void
    {
        Schema::table('points', fn (Blueprint $t) => $t->dropColumn('crime_type'));
        Schema::table('polylines', fn (Blueprint $t) => $t->dropColumn('crime_type'));
        Schema::table('polygons', fn (Blueprint $t) => $t->dropColumn('crime_type'));
    }
};
