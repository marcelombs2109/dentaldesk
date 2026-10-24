<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * SDD-01 R3, R4: cada usuario tiene un rol fijo y el odontólogo
     * queda vinculado al doctor de la tabla dentistas.
     */
    public function up(): void
    {
        Schema::table('users', function (Blueprint $table) {
            $table->string('rol', 20)->default('odontologo')->after('password');
            $table->foreignUuid('dentista_id')
                ->nullable()
                ->after('rol')
                ->constrained('dentistas')
                ->nullOnDelete();
        });
    }

    public function down(): void
    {
        Schema::table('users', function (Blueprint $table) {
            $table->dropConstrainedForeignId('dentista_id');
            $table->dropColumn('rol');
        });
    }
};
