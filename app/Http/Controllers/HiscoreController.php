<?php
	
namespace App\Http\Controllers;

use App\Models\Hiscore;
use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;


class HiscoreController extends Controller
{
	const HISCORE_LIMIT = 15;
	
	public function index($limit = self::HISCORE_LIMIT)
	{	
		$hiscores = Hiscore::whereNotNull('user_name') // Solo hiscores con nombre de usuario
		->orderBy('score', 'desc')           // Ordenar por puntaje descendente
		->take($limit)                       // Limitar al número indicado
		->get();
		
		// Calcular cuántas filas faltan para llegar al límite
		$missing = $limit - $hiscores->count();
		
		// Rellenar con filas vacías si es necesario
		for ($i = 0; $i < $missing; $i++) {
			$hiscores->push((object) [
			'user_name' => '',    // Nombre vacío
			'score' => 0          // Puntaje cero
			]);
		}
		
		return $hiscores;
	}
	
	public function store(Request $request)
	{
		// Eliminar registros temporales creados hace más de 10 minutos
		Hiscore::whereNull('user_name')
		->where('created_at', '<', now()->subMinutes(10))
		->delete();
		
		// Obtener datos de la nueva puntuación
		$score = $request->input('score');
		$game_name = $request->input('game_name');
		
		// Verificar si es un hiscore
		$hiscores = Hiscore::where('game_name', $game_name)
		->orderBy('score', 'desc')
		->take(self::HISCORE_LIMIT)
		->get();
		
		if ($hiscores->count() < self::HISCORE_LIMIT || $score > $hiscores->last()->score) {
			// Insertar nueva puntuación temporal
			$hiscore = Hiscore::create([
			'game_name' => $game_name,
			'score' => $score,
			'user_name' => null, // Temporalmente vacío
			]);
			
			// Retornar el ID para manejar la vista de edición
			return response()->json(['isHighscore' => true, 'rank' => $hiscores->count() + 1, 'id' => $hiscore->id]);
		}
		
		// No es un hiscore
		return response()->json(['isHighscore' => false]);
	}
	
	public function updateName(Request $request, $id)
	{
		//Log::info('Dentro de HiscoreController.updateName:'.$id);
		
		try {
			// Validar el nombre del usuario
			$validated = $request->validate([
			'user-name' => 'required|string|max:50',
			]);
			
			$userName = $validated['user-name'];
			
			// Buscar el highscore temporal
			$hiscore = Hiscore::findOrFail($id);
			
			if ($hiscore->user_name !== null) {
				// Si ya tiene un nombre, no debería actualizarse de nuevo
				return redirect()->route('home')
				->withErrors(['message' => 'Este highscore ya ha sido actualizado.']);
			}
			
			// Comprobar si el usuario ya tiene un highscore registrado para este juego
			$existingHiscore = Hiscore::where('game_name', $hiscore->game_name)
			->where('user_name', $userName)
			->first();
			
			if ($existingHiscore) {
				if ($hiscore->score > $existingHiscore->score) {
					// Actualizar la puntuación existente si la nueva es mayor
					$existingHiscore->update([
					'score' => $hiscore->score,
					'created_at' => now(), // Actualizar la fecha para reflejar el cambio
					]);
					
					// Eliminar el registro temporal
					$hiscore->delete();
					
					return redirect()->route('home')
					->with('success', 'High score actualizado correctamente.');
					} else {
					// No actualizar si la puntuación existente es mayor o igual
					$hiscore->delete(); // Limpiar el registro temporal
					return redirect()->route('home')
					->withErrors(['message' => "Ya hay highscore mayor registrado para '{$userName}'"]);
				}
			}
			
			// Si no existe un registro previo, actualizar el highscore temporal
			$hiscore->update(['user_name' => $userName]);
			
			return redirect()->route('home')
			->with('success', 'High score actualizado correctamente.');
			} catch (\Exception $e) {
			Log::error('Error en updateName: ' . $e->getMessage());
			return redirect()->route('home')
			->withErrors(['message' => 'Error al actualizar el high score. Por favor, inténtalo de nuevo.']);
		}
	}
}	