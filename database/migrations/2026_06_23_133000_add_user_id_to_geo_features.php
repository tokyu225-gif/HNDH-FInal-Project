<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('points', fn (Blueprint $t) => $t->foreignId('user_id')->nullable()->after('id')->constrained()->nullOnDelete());
        Schema::table('polylines', fn (Blueprint $t) => $t->foreignId('user_id')->nullable()->after('id')->constrained()->nullOnDelete());
        Schema::table('polygons', fn (Blueprint $t) => $t->foreignId('user_id')->nullable()->after('id')->constrained()->nullOnDelete());
    }

    public function down(): void
    {
        Schema::table('points', fn (Blueprint $t) => $t->dropConstrainedForeignId('user_id'));
        Schema::table('polylines', fn (Blueprint $t) => $t->dropConstrainedForeignId('user_id'));
        Schema::table('polygons', fn (Blueprint $t) => $t->dropConstrainedForeignId('user_id'));
    }
};
