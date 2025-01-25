var gameName = "fasttap";
var score = -1; // to store the current score
var duration = 10; // 10 seconds
var startTime; // start time
var ended = false; // boolean indicating if game is ended
var timerTxt;
var scoreTxt;
var startBtn;
var hiscoreId; // to store the hiscoreID if the user reach one
var clouds;
var endGameMsg;

document.addEventListener('DOMContentLoaded',function() {
	// we get DOM References for some HTML elements	
	scoreTxt = document.querySelector('#score');
	startBtn = document.querySelector('#start-btn');
	scaleCover = document.querySelector('#scale-cover');
	clouds = document.querySelectorAll('.cloud');
	endGameMsg = document.querySelector('#end-game-msg');
	
	startBtn.addEventListener("click", function() {
		if (!ended) {
			if (score === -1) {
				startGame();
			}
			score++;
			scoreTxt.textContent = score;
			scaleCover.style.height = (100 - score) + '%'; // Disminuye la altura para mostrar más de la escala
		}
	});
	
});

// we define two functions for showing or hiding a HTML element
var show = function (elem) {
	elem.style.display = "inline";
};

var hide = function (elem) {
	elem.style.display = "none";
};

// Method called when the game starts
function startGame() {
	
	// Stop cloud animation
    clouds.forEach(cloud => {
        cloud.classList.add('paused');
	});
	scoreTxt.classList.toggle('invisible');
	
	// we get start time
    const timerTxt = document.querySelector('#timer');
    const startTime = Date.now();
	
    // Inicia el temporizador
    timerId = setInterval(() => {
        const elapsed = (Date.now() - startTime) / 1000; // Tiempo transcurrido en segundos
        const remaining = duration - elapsed; // Tiempo restante
		
        if (remaining > 0) {
            // Actualiza el texto con el tiempo restante
            timerTxt.textContent = remaining.toFixed(2);
			} else {
            // Fin del juego
            ended = true;
            clearInterval(timerId); // Limpia el intervalo
            timerTxt.textContent = "0.00"; // Asegura que muestra exactamente 0 al finalizar
            endGame(); // Llama a la función de finalización
		}
	}, 10); // Intervalo de 10ms
}

// end game method
function endGame() {
	// Restart cloud animation
    clouds.forEach(cloud => {
        cloud.classList.remove('paused');
	}); 
	checkScore();
}

function checkScore() {
	fetchWithCsrf("/hiscores", {
		method: "POST",
		body: JSON.stringify({
			game_name: gameName,
			score: score
		}),
		headers: {
			'Content-Type': 'application/json',
		}
	})
    .then(response => {
		let nextAction;
		endGameMsg.classList.toggle('d-none');
		
        if (response.isHighscore) {
            // Si la puntuación está en el top 10
            hiscoreId = response.id;
			endGameMsg.textContent = "HIGH SCORE!!";
			endGameMsg.classList.add('highlight');
			
			nextAction = () => newHiscore(score, hiscoreId);
			
			} else {
			endGameMsg.classList.add('text-white');
			endGameMsg.textContent = "GAME OVER";

            nextAction = () => window.location.href = "/";			
		}
		setTimeout(nextAction, 3000);
	})
    .catch(error => {
        console.error(error);
        alert("Hubo un error al enviar la puntuación.");
	});
}


