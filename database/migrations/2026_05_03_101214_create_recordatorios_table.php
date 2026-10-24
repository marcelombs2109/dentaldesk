<?php
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('recordatorios', function (Blueprint $table) {
            $table->uuid('id')->primary();
            $table->foreignUuid('cita_id')->constrained('citas');
            $table->timestamp('enviado_en')->nullable();
            $table->enum('estado', ['enviado', 'confirmado', 'rechazado', 'sin_respuesta'])
                  ->default('enviado');
            $table->string('respuesta_wa')->nullable();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('recordatorios');
    }
};