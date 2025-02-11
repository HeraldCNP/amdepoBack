<?php

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
        Schema::create('municipios', function (Blueprint $table) {
            $table->id();
            $table->string('nombre');
            $table->text('descripcion')->nullable();
            $table->string('slug')->unique();
            $table->string('direccion')->nullable();
            $table->string('telefono')->nullable();
            $table->string('email')->nullable();
            $table->string('sitio_web')->nullable();
            $table->decimal('latitud', 10, 8)->nullable();
            $table->decimal('longitud', 10, 8)->nullable();
            $table->integer('poblacion')->nullable();
            $table->decimal('superficie', 10, 2)->nullable();
            $table->text('historia')->nullable();
            $table->string('gentilicio')->nullable();
            $table->string('alcalde_nombre')->nullable();
            $table->string('alcalde_foto')->nullable();
            $table->text('alcalde_descripcion')->nullable();
            $table->foreignIdFor(User::class)->constrained()->onDelete('cascade');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('municipios');
    }
};
