<?php

use App\Models\Categoria;
use App\Models\User;
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
        Schema::create('noticias', function (Blueprint $table) {
            $table->id();
            $table->string('titulo')->unique(); // Título de la noticia, debe ser único
            $table->string('slug')->unique();   // Slug automático y único para URL amigables
            $table->longText('texto');          // Contenido principal de la noticia
            $table->string('video_url')->nullable(); // URL opcional para videos de YouTube/Facebook/TikTok
            $table->foreignId('categoria_id')->constrained('categorias')->onDelete('cascade');
            $table->foreignId('user_id')->constrained('users')->onDelete('cascade'); // Relación con la tabla users
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('noticias');
    }
};
