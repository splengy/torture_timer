// Filename: timer.js
// Path: /htdocs/timer.js
// Date Edited: 2024-04-23
// Revision Number: 1.0

document.addEventListener('DOMContentLoaded', function() {
    // Definitions and initial setup
    let exercises = [
        { name: "Exercise 1", workoutTime: 120, restTime: 60, description: "Description here", howTo: "How-to here" },
        // Add more exercises as needed
    ];
    let currentExercise = 0;
    let isWorkout = true;
    let timerSeconds = exercises[0].workoutTime;

    // Start the first exercise automatically
    startNextExercise();

    // Element references
    const exerciseNameElement = document.getElementById("exerciseName");
    const timerElement = document.getElementById("timer");

    function startNextExercise() {
        let exercise = exercises[currentExercise];
        exerciseNameElement.innerText = exercise.name;
        exerciseNameElement.style.cursor = 'pointer';
        exerciseNameElement.onclick = function() { openDetailWindow(exercise); };
        timerSeconds = isWorkout ? exercise.workoutTime : exercise.restTime;
        updateTimerDisplay();
    }

    function openDetailWindow(exercise) {
        const detailWindow = window.open("", "_blank", "width=600,height=400");
        detailWindow.document.write(`
            <html>
            <head>
                <title>${exercise.name} Details</title>
                <style>
                    body { font-family: Arial, sans-serif; }
                    #close { float: right; cursor: pointer; }
                    #mainArea { overflow-y: scroll; height: 300px; margin-bottom: 20px; padding: 10px; border: 1px solid #ccc; }
                    .button { padding: 10px 20px; width: 100%; font-size: 18px; }
                </style>
            </head>
            <body>
                <div id="header">
                    <h1>${exercise.name}</h1>
                    <span id="close" onclick="window.close()">Close</span>
                </div>
                <div id="mainArea">
                    <p>${exercise.description || 'No description available.'}</p>
                    <p>${exercise.howTo || 'No specific instructions.'}</p>
                </div>
                <button class="button" onclick="window.close()">Close</button>
            </body>
            </html>
        `);
    }

    function updateTimerDisplay() {
        let minutes = Math.floor(timerSeconds / 60);
        let seconds = timerSeconds % 60;
        timerElement.innerText = `${minutes}:${seconds.toString().padStart(2, '0')}`;
        timerElement.className = isWorkout ? 'workout' : 'rest';
    }

    function toggleTimer() {
        isPaused = !isPaused;
        if (isPaused) {
            clearInterval(timerInterval);
            timerElement.classList.add('blink');
        } else {
            timerElement.classList.remove('blink');
            countdown();
        }
    }

    function countdown() {
        timerInterval = setInterval(function() {
            if (timerSeconds > 0) {
                timerSeconds--;
                updateTimerDisplay();
            } else {
                clearInterval(timerInterval);
                switchToNextPhase();
            }
        }, 1000);
    }

    function playBellSound() {
        let audio = new Audio('bell_sound.mp3');
        audio.play();
    }

    function switchToNextPhase() {
        if (isWorkout) {
            isWorkout = false;
            timerSeconds = exercises[currentExercise].restTime;
        } else {
            isWorkout = true;
            if (currentExercise < exercises.length - 1) {
                currentExercise++;
            } else {
                currentExercise = 0; // Start over or finish the routine
            }
            timerSeconds = exercises[currentExercise].workoutTime;
        }
        startNextExercise();
    }
});
