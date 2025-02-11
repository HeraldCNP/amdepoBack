<?php

use App\Models\Municipio;
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
        Schema::create('imagenes_turisticas', function (Blueprint $table) {
            $table->id();
            $table->foreignIdFor(Municipio::class)->constrained()->onDelete('cascade');
            $table->string('ruta_imagen');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('imagenes_turisticas');
    }
};
