<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateHiscoresTable extends Migration
{
    /**
     * Run the migrations.
     */
    public function up()
    {
        Schema::create('hiscores', function (Blueprint $table) {
            $table->id(); // Crea un campo BIGINT UNSIGNED autoincremental llamado "id"
            $table->string('user_name', 50); // Nombre del usuario (hasta 50 caracteres)
            $table->string('game_name', 100); // Nombre del juego (hasta 100 caracteres)
            $table->unsignedInteger('score'); // Puntuación del usuario (valor positivo)            
            $table->timestamps(); // Campos "created_at" y "updated_at"
			
			// Restricción de unicidad
			$table->unique(['user_name', 'game_name', 'score'], 'unique_user_game_score');			
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down()
    {
        Schema::dropIfExists('hiscores');
    }
}
