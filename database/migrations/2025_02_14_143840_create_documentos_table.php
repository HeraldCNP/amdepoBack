<?php

use App\Models\Municipio;
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
        Schema::create('documentos', function (Blueprint $table) {
            $table->id();
            $table->string('titulo');
            $table->text('descripcion')->nullable();
            $table->string('ruta_archivo'); // Ruta al archivo en el sistema de archivos
            $table->string('tipo_archivo'); // Tipo de archivo (pdf, docx, etc.)
            $table->year('gestion'); // Añade el campo anio_gestion de tipo YEAR
            $table->foreignIdFor(Municipio::class)->constrained()->onDelete('cascade');
            $table->foreignIdFor(User::class)->constrained()->onDelete('cascade');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('documentos');
    }
};
