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
        Schema::create('concejales', function (Blueprint $table) {
            $table->id();
            $table->string('nombre');
            $table->string('foto')->nullable();
            $table->text('descripcion')->nullable();
            $table->foreignIdFor(Municipio::class)->constrained()->onDelete('cascade');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('concejales');
    }
};
