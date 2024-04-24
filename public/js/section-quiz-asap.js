import {
    PROJECT_RESOURCE_DIR,
    PROJECT_ROOT_DIR,
    sleep
} from './module-info.js';

import {
    pre_test
} from './temp-pre-test-asap.js';

import {
    post_test
} from './temp-post-test-asap.js';

import {
    QuestType
} from './view/QuestType.js';

export let questionCount = 0;
export let questionNumb = 1;
export let userScore = 0;

export const quizAsap = (quizType) => {

    try {


        let questions;
        switch (quizType) {
            case QuestType.PRE:
                questions = pre_test;
                break;
            default:
                questions = post_test;
                break;
        }

        const exitBtn = document.querySelector('.exit-btn');
        const continueBtn = document.querySelector('.continue-btn');
        const quizSection = document.querySelector('.quiz-section');
        const quizBox = document.querySelector('.quiz-box');
        const resultBox = document.querySelector('.result-box');
        const nextBtn = document.querySelector('.next-btn');
        const goHomeBtn = document.querySelector('.goHome-btn');
        const optionList = document.querySelector('.option-list');

        showQuestions(0);
        questionCounter(1);
        headerScore();

        nextBtn.onclick = () => {
            if (questionCount < questions.length - 1) {
                questionCount++;
                showQuestions(questionCount);

                questionNumb++;
                questionCounter(questionNumb);

                nextBtn.classList.remove('active');
            } else {
                showResultBox();
            }

        }

        function showQuestions(index) {

            const questionText = document.querySelector('.question-text');
            let optionTag;

            questionText.textContent = `${questions[index].numb}. ${questions[index].question}`;

            optionTag = `<div class="option"><span>${questions[index].options[0]}</span></div>
                                <div class="option"><span>${questions[index].options[1]}</span></div>
                                <div class="option"><span>${questions[index].options[2]}</span></div>
                                <div class="option"><span>${questions[index].options[3]}</span></div>`;


            optionList.innerHTML = optionTag;

            const option = document.querySelectorAll('.option');
            for (let i = 0; i < option.length; i++) {
                option[i].addEventListener('click', (event) => {
                    optionSelected(event.target);
                });
            }
        }

        function questionCounter(index) {
            const questionTotal = document.querySelector('.question-total');
            questionTotal.textContent = `${index} of ${questions.length} Questions`;
        }

        function headerScore() {
            const headerScoreText = document.querySelector('.header-score');
            headerScoreText.textContent = `Score: ${userScore} / ${questions.length}`;
        }

        function showResultBox() {
            //REM: Assuming quizBox, resultBox, userScore, and questions are defined elsewhere
            quizBox.classList.remove('active');
            resultBox.classList.add('active');

            const scoreText = document.querySelector('.score-text');
            scoreText.textContent = `Your Score ${userScore} out of ${questions.length}`;

            const circularProgress = document.querySelector('.circular-progress');
            const progressValue = document.querySelector('.progress-value');
            let progressStartValue = -1;
            let progressEndValue = (userScore / questions.length) * 100;
            let speed = 20;

            let progress = setInterval(() => {
                progressStartValue++;

                progressValue.textContent = `${progressStartValue}%`;
                circularProgress.style.background = `conic-gradient(#c40094 ${progressStartValue * 3.6}deg, rgba(255, 255, 255, .1) 0deg);`;

                if (progressStartValue === progressEndValue) {
                    clearInterval(progress);
                }
            }, speed);
        }

        function optionSelected(answer) {

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

            for (let i = 0; i < allOptions; i++)
                optionList.children[i].classList.add('disabled');

            nextBtn.classList.add('active');
        }

    } catch (error) {
        console.log(`111::: ${import.meta.url}: ${error}`);
    }

};


let myAudio = new Audio();
myAudio.src = PROJECT_RESOURCE_DIR + 'audio/bgm-001.mp3';

function loopAudio() {
    myAudio.currentTime = 0;
    myAudio.play();
}

// myAudio.addEventListener('ended', loopAudio);
myAudio.addEventListener('play', loopAudio);