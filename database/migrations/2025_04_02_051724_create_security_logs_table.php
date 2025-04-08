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
        Schema::create('security_logs', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')
                  ->nullable()
                  ->constrained('users')
                  ->onDelete('set null');  // Relación con usuarios
            $table->string('email')->nullable();  // Para registrar intentos fallidos
            $table->string('ip_address', 45);  // Almacena IPv4/IPv6
            $table->text('user_agent')->nullable();  // Navegador/dispositivo
            $table->string('event');  // Ej: login_success, login_failed, logout
            $table->timestamp('created_at')->useCurrent();  // Fecha exacta del evento
            
            // Índices para búsquedas eficientes
            $table->index('user_id');
            $table->index('event');
            $table->index('created_at');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('security_logs');
    }
};