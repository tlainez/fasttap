<!DOCTYPE html>
<html lang="en">
	<head>
		<meta charset="UTF-8">
		<title>FastTap</title>
		<link rel="preload" href="{{ asset('css/led_scoreboard.ttf') }}" as="font" type="font/ttf" crossorigin="anonymous">
		<link rel="stylesheet" href="{{ asset('css/app.css') }}">
		<link rel="stylesheet" href="{{ asset('css/scale.css') }}">
		<link rel="stylesheet" href="{{ asset('css/bootstrap.min.css') }}">
		<link href="{{ asset('css/fontawesome/fontawesome.min.css') }}" rel="stylesheet">
		<link href="{{ asset('css/fontawesome/solid.min.css') }}" rel="stylesheet">
		<link href="{{ asset('css/fontawesome/regular.min.css') }}" rel="stylesheet">
		<meta name="viewport" content="width=device-width, initial-scale=1.0">
		<meta name="csrf-token" content="{{ csrf_token() }}">
	</head>
	
	<body>
		
		<!--x-topbar /-->
		
		@if($errors->any())
		<!-- Toast para el mensaje de error -->
		<div id="errorToast" class="toast position-fixed top-0 p-2 z-3" role="alert" aria-live="assertive" aria-atomic="true">
			<div class="toast-body">
				<i class="fa-solid fa-triangle-exclamation text-danger"></i>
				{{ $errors->first('message') }}
			</div>
		</div>
		@endif
		
		<div class="display-border">
			<div class="display-shine"></div>
			<div class="display">
				<span id="hiscore-text" class="led-text flex-column">
					<div id ="table-title" class="led-text text-center">FASTTAP Hi-Scores</div>
					
					<table>
						<thead class="text-center">
							<tr>
								<th class="text-center">Rank</th>
								<th class="text-center">Score</th>
								<th>Name</th>
							</tr>
							<tr>
								<th colspan="3"></th>
							</tr>
						</thead>
						<tbody>
							@if (!empty($hiscores) && $hiscores->count())
							@foreach ($hiscores as $rank => $hiscore)
							<tr data-rank="{{ $rank + 1 }}">
								<td class="text-center">{{ $rank + 1 }}{{ getOrdinalSuffix($rank + 1) }}</td>
								<td class="text-center">{{ $hiscore->score }}</td>
								<td>{{ $hiscore->user_name }}</td>
							</tr>
							@endforeach
							@else
							<tr>
								<td colspan="3" class="text-center">No data found!</td>
							</tr>
							@endif
						</tbody>
					</table>
					
					<div class="led-text highlight" id="press-to-play">Press to play</div>
				</span>
				
				<div id="game-info" class="led-text d-flex d-none">
					Timer: <span id="timer">10.00</span>
					<span id="end-game-msg" class="led-text d-none">GAME OVER</div>
				</div>				
				
			</div>
		</div>
		
		<div class="modal fade" id="modal-message" tabindex="-1" aria-labelledby="modal-message-label" aria-hidden="true" data-bs-backdrop="static">
			<div class="modal-dialog modal-dialog-centered">
				<div class="modal-content bg-gradient">
					<!-- Encabezado -->
					<div class="modal-header border-0 text-center position-relative">
						<button type="button" class="btn-close position-absolute top-0 end-0 m-2" data-bs-dismiss="modal" aria-label="Close"></button>
						<div class="w-100">
							<i id="modal-icon" class="fa-solid fa-star mb-4"></i>                
							<h4 class="fw-bold" id="modal-message-label"></h4>
						</div>
					</div>
					<!-- Cuerpo -->
					<div class="modal-body text-center">
						<p class="fs-5" id="modal-message-body"></p>
						<form id="frm-modal-message" action="" method="POST" class="d-flex flex-column align-items-center">
							@csrf
							@method('PUT')
							<input type="text" name="user-name" id="user-name" 
							class="form-control rounded-pill mb-3" 
							placeholder="Your name" 
							style="max-width: 300px;" 
							maxlength="15"
							oninput="checkUserName()">							
							<div class="d-flex justify-content-center gap-3">
								<button id="modal-action-btn" type="submit" class="btn bg-primary rounded-pill px-4 text-white" disabled>Save</button>
								<!--button id="modal-close-btn" type="button" class="btn bg-secondary rounded-pill px-4 text-white" data-bs-dismiss="modal">Close</button-->
							</div>
						</form>
					</div>
				</div>
			</div>
		</div>
		
		<!-- Sol y nuves-->
		<div class="sun"></div>
		<div class="clouds">
			<img src="img/cloud.png" alt="Nube 1" class="cloud" style="--size: 500px; --speed: 75s; --start: 0%; --top: 10%; --rotation: 0deg;">
			<img src="img/cloud.png" alt="Nube 2" class="cloud" style="--size: 400px; --speed: 65s; --start: -50%; --top: 20%; --rotation: 0deg;">
			<img src="img/cloud.png" alt="Nube 3" class="cloud" style="--size: 500px; --speed: 85s; --start: -70%; --top: 5%; --rotation: 180deg;">
		</div>
		
		
		<div class="strength-game-wrapper">
			<div class="strength-game">
				<!-- Círculo superior -->
				<div class="button-wrapper scale-top">	
					<div class="score-circle"><span class="text-white">100</span><i class="fa-solid fa-star"></i></div>
				</div>
				<!-- Regla con escala -->
				<div class="scale-wrapper">
					<div class="scale-container">
						<div id="scale-cover"><span class="invisible" id="score"></span></div>
						<div class="scale"></div>
					</div>
				</div>
				
				<!-- Base -->
				<div class="pedestal">
					<button id="start-btn">Push</button>
				</div>
			</div>			
		</div>
		
        <!-- Suelo -->
        <div class="ground"></div>		
		
		<script src="{{ asset('js/app.js') }}"></script>
		<script src="{{ asset('js/game.js') }}"></script>
		<script src="{{ asset('js/bootstrap.bundle.min.js') }}"></script>
	</body>
</html>
