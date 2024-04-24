import {
    questions
} from './questions.js';

import {
    PROJECT_ROOT_DIR,
    PROJECT_RESOURCE_DIR
} from './module-info.js';

import {
    Dev
} from './Dev.js';

const DEV = Dev.getInstance();
DEV.setIsDebugMode(true);

let questionCount = 0;
let questionNumb = 1;
let userScore = 0;


function bgmPlay() {
    const audio = document.getElementById('BGM_001');
    if (audio) {
        audio.play()
            .then(() => console.log("Audio playback started"))
            .catch(error => console.error("Error playing audio:", error));
    } else {
        console.error("Audio element not found");
    }
}


bgmPlay();


document.addEventListener('DOMContentLoaded', () => {


    const nextBtn = document.querySelector('.next-btn');

    const startBtn = document.querySelector('.start-btn');
    const popupInfo = document.querySelector('.popup-info');
    const exitBtn = document.querySelector('.exit-btn');
    const classMain = document.querySelector('.main');
    const continueBtn = document.querySelector('.continue-btn');
    const quizSection = document.querySelector('.quiz-section');
    const quizBox = document.querySelector('.quiz-box');
    const resultBox = document.querySelector('.result-box');
    const optionList = document.querySelector('.option-list');
    const classHome = document.querySelector('.home');

    if (classHome) classHome.classList.add('active');
    if (classMain) classMain.classList.toggle('active');

    if (startBtn) {
        startBtn.onclick = () => {

            bgmPlay();
            //REM: TODO-HERE; Make a quick sign-up and sign-in form.
            //REM: And then try implement a session in back-end side again using PHP
            //REM: maybe named them; 'sign-up.php' and 'sign-in.php'
            //REM: If we have session/cookies implemented and we already sign-in
            //REM: then no need to log-in again unless whe're logged out.
            //REM: Again use Fetch.
            //REM: send and recieve data in json format.

            // REM: add if neede; ${PROJECT_ROOT_DIR}
            fetch(`src/sign-in.php`)
                .then(response => response.text())
                .then(data => {
                    //REM: TODO-HERE; make it work only in DEV/DEBUG MODE
                    if (DEV.isInDebugMode())
                        console.log(data);
                })
                .catch(error => {
                    console.log(error);
                });

            //REM: if successfully sigin-in then update the DOM/HTML in here,
            //REM: especially the profile btn make it visible.


            popupInfo.classList.add('active');
            classMain.classList.toggle('active');

        }
    }

    //REM: make the said profile btn a 'onlick eventListener' here,
    //REM: then upon clicking show the said profile section in a 
    //REM: hover/sticky way and relative to the said btn.
    //REM: Again use Fetch. Maybe named the php file 'retrieve-participant.php'
    //REM: send and recieve data in json format.

    if (exitBtn) {
        exitBtn.onclick = () => {
            popupInfo.classList.remove('active');
            classMain.classList.toggle('active');
            classHome.classList.add('active');
        }
    }

    if (continueBtn) {
        continueBtn.onclick = () => {
            popupInfo.classList.remove('active');
            quizSection.classList.add('active');
            quizBox.classList.add('active');
            classHome.classList.remove('active');
            classMain.classList.toggle('active');
            showQuestions(0);
            questionCounter(1);
            headerScore();
        }
    }

    if (nextBtn) {
        nextBtn.onclick = () => {

            if (questionCount < questions.length - 1) {
                questionCount++;
                showQuestions(questionCount);

                questionNumb++;
                questionCounter(questionNumb);

                nextBtn.classList.remove('active');
            }

            console.log("questionCount: '", questionCount, "'");
            console.log("questionNumb: '", questionNumb, "'");
            console.log("userScore: '", userScore, "'");
            /*else {
                    showResultBox();
            }*/

        }

    }

});

// getting questions and options from array
function showQuestions(index) {
    const questionText = document.querySelector('.question-text');
    questionText.textContent = `${questions[index].numb}. ${questions[index].question}`;

    // let optionTag = `<div class="option"><span>${questions[index].options[0]}</span></div>
    //         <div class="option"><span>${questions[index].options[1]}</span></div>
    //         <div class="option"><span>${questions[index].options[2]}</span></div>
    //         <div class="option"><span>${questions[index].options[3]}</span></div>`;
    const optionSize = questions[index].options.length;
    let optionTag = '';
    for (let i = 0; i < optionSize; ++i)
        optionTag += `<div class="option"><span>${questions[index].options[i]}</span></div>`;


    optionList.innerHTML = optionTag;

    const option = document.querySelectorAll('.option');
    for (let i = 0; i < option.length; i++) {
        option[i].addEventListener('click', (event) => {
            optionSelected(event.target);
        });
    }
}

export function optionSelected(answer) {
    let userAnswer = answer.textContent;
    let correctAnswer = questions[questionCount].answer;
    let allOptions = optionList.children.length;

    if (userAnswer == correctAnswer) {
        answer.classList.add('correct');
        userScore += 1;
        headerScore();
    } else {
        answer.classList.add('incorrect');

        for (let i = 0; i < allOptions; i++) {
            if (optionList.children[i].textContent == correctAnswer) {
                optionList.children[i].setAttribute('class', 'option correct');
            }
        }
    }

    //REM: TODO-HERE; update participant points; not the main dbParticipant table,
    //REM: instead do the log dbparticipant table 
    //REM: only modify the main dbParticipant table if the current score/point is > the highest score achieve
    //REM: ofcourse do all of these in back-end, maybe named the said php file 'update-participant.php'
    //REM: while in here do fetching, do POST or GET
    //REM: send and recieve data in json format.

    for (let i = 0; i < allOptions; i++) {
        optionList.children[i].classList.add('disabled');
    }

    nextBtn.classList.add('active');
}

function questionCounter(index) {
    const questionTotal = document.querySelector('.question-total');
    questionTotal.textContent = `${index} of ${questions.length} Questions`;
}

function headerScore() {
    const headerScoreText = document.querySelector('.header-score');
    headerScoreText.textContent = `Score: ${userScore} / ${questions.length}`;
}


/*function showResultBox() { 
        quizBox.classList.remove('active');
        resultBox.classList.add('active');

        const scoreText = document.querySelector('.score-text');
        scoreText.textContent = `Your Score ${userScore} out of ${question.length}`;

        const circularProgress = document.querySelector('.circular-progress');
        const progressValue = document.querySelector('.progress-value');
        let progressStartValue = -1;
        let progressEndvalue = (userScore / questions.length) *100;
        let speed = 20;

        let progress = setInterval( () => {
                progressStartValue++;

                progressValue.textContent = `${progressStartValue}%`;
                circularProgress.style.background = `conic-gradient(#c40094 ${progressStartValue * 3.6}deg, rgba(255, 255, 255, .1) 0deg );`;
                
                if (progressStartValue == progressEndvalue) {
                    clearInterval(progress);
                }
                
        }, speed);
} */


//REM: AUDIO; BG_MUSIC
// window.onload = function() {
//         var audio = document.getElementById("BG_AUDIO");
//         audio.onplay;
// };