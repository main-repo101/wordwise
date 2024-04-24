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

import {
    RequestBodyType
} from './model/RequestBodyType.js';

//REM: TODO-HERE; refactor them....
export let userScore = 0;
export let currentQAId = '';
export let username = '';
export let totalItems = 0;
export let currentItemNumber = 0;

export const quizAsap = (questype) => {

    try {
        // let questions;
        // switch (questype) {
        //     case QuestType.PRE:
        //         questions = pre_test;
        //         break;
        //     default:
        //         questions = post_test;
        //         break;
        // }

        fetch(PROJECT_ROOT_DIR + 'src/section-quest.php', {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json'
                },
                body: JSON.stringify({
                    'quest_type': questype,
                    'request_body_type': RequestBodyType.CHECK_SESSION
                })
            })
            .then(resData => resData.json())
            .then(resData => {
                document.getElementById('LBL_QUEST_TYPE').textContent = questype;
                username = resData.username;
                LBL_USERNAME_FOR_SCORING.textContent = username;

                totalItems = resData.quest_info.total_items;
                userScore = resData.score_info.points;
                currentItemNumber = resData.quest_info.current_item_number;
                showQuestions();
                questionCounter();
                headerScore();
                // console.log(resData);
                console.log(`::: ${import.meta.url}: GENERATE QUEST INIT, ${resData.message}`);
            })
            .catch(error => {
                throw error;
            });

        // const exitBtn = document.querySelector('.exit-btn');
        // const continueBtn = document.querySelector('.continue-btn');
        // const quizSection = document.querySelector('.quiz-section');
        const quizBox = document.querySelector('.quiz-box');
        const resultBox = document.querySelector('.result-box');
        const nextBtn = document.querySelector('.next-btn');
        // const goHomeBtn = document.querySelector('.goHome-btn');
        const optionList = document.querySelector('.option-list');
        const LBL_USERNAME_FOR_SCORING = document.getElementById('LBL_USERNAME_FOR_SCORING');
        const TOTAL_ITEMS = document.getElementById('TOTAL_ITEMS');


        nextBtn.onclick = () => {
            if (currentItemNumber <= totalItems) {
                showQuestions(currentItemNumber);
                questionCounter(currentItemNumber);
                nextBtn.classList.remove('active');
            } else {
                showResultBox();
            }

        }

        function showQuestions() {
            // (async () => {
            const questionText = document.querySelector('.question-text');
            let optionTag = '';
            // const questRes = fetch(PROJECT_ROOT_DIR + 'src/section-quest.php', {
            fetch(PROJECT_ROOT_DIR + 'src/section-quest.php', {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json'
                    },
                    body: JSON.stringify({
                        'request_body_type': RequestBodyType.CHECK_QUEST,
                        'quest_type': questype
                    })
                })
                .then(questResData => questResData.json())
                .then(questResData => {
                    // console.log(`::: ${import.meta.url}: showQuestions: `);
                    // console.log(questResData)
                    currentQAId = questResData.quest_info.qa_id;
                    userScore = questResData.score_info.points;
                    totalItems = questResData.quest_info.total_items;
                    currentItemNumber = questResData.quest_info.current_item_number;
                    if (!questResData.had_error) {
                        questionText.textContent = `${questResData.quest_info.current_item_number}. ${questResData.quest_info.question}`;
                        const letters = ['A', 'B', 'C', 'D', 'E', 'F', 'G']; //REM: TODO-HERE; refactor it later...
                        for (let i = 0; i < questResData.quest_info.choices.length; i++) {
                            optionTag += `<div class="option">${letters[i]}. <span class="inline-block">${questResData.quest_info.choices[i]}</span></div>`;
                        }

                    } else /*if (questResData.quest_info.total_items_left <= 0)*/ {
                        document.getElementById('SECTION_QUIZ').classList.remove('active');
                        document.getElementById('SECTION_QUIZ_RESULT').classList.add('active');
                        document.querySelectorAll('.server-msg').forEach(element => {
                            element.innerHTML = `<h3>${questResData.message}</h3>`;
                        });
                        showResultBox();
                    }
                    /*else {
                                           optionTag += `<div class="option"><span class="block">${questResData.had_error}, ${questResData.message}</span></div>`
                                       }*/
                    optionList.innerHTML = optionTag;
                    const option = document.querySelectorAll('.option');
                    for (let i = 0; i < option.length; i++) {
                        option[i].addEventListener('click', async (event) => {
                            await optionSelected(event.target);
                        });
                    }
                    console.log(`::: ${import.meta.url}: showQuestions: ${questResData.had_error}, ${questResData.message}, ${questResData.quest_info.qa_id}`);
                    console.log(`::: ${import.meta.url}: showQuestions: ${questResData.quest_info.qa_id}, ${questResData.quest_info.question}, ${questResData.quest_info.choices.join(' | ')}`);
                })
                .catch(error => {
                    throw error;
                });
        }


        async function optionSelected(answer) {

            // console.log(answer)
            // console.log(answer.children)

            //REM: A quick fix, to a bug on the said selected answer tags 
            let userAnswer = answer.children.length && answer.children[0].textContent || answer.textContent || '';
            // console.log(`3: ${userAnswer}`)

            let optionListLen = optionList.children.length;
            // console.log(`4: ${optionListLen}`)

            await fetch(PROJECT_ROOT_DIR + 'src/section-quest.php', {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json'
                    },
                    body: JSON.stringify({
                        'request_body_type': RequestBodyType.CHECK_ANSWER,
                        'qa_id': currentQAId,
                        'quest_type': questype,
                        'your_answer': [userAnswer]
                    })
                })
                .then(questResData => questResData.json())
                .then(questResData => {
                    totalItems = questResData.quest_info.total_items;
                    currentItemNumber = questResData.quest_info.current_item_number;
                    if (questResData.answer_server.is_correct) {
                        answer.classList.add('correct');
                        answer.classList.add('block'); //REM: TODO-HERE; 
                        userScore = questResData.score_info.points;
                        currentItemNumber = questResData.quest_info.current_item_number;
                        headerScore();
                    } else {
                        answer.classList.add('incorrect');
                        answer.classList.add('block'); //REM: TODO-HERE; 
                        for (let i = 0; i < optionListLen; i++) {
                            if (questResData.answer_server.answer.includes(optionList.children[i].children[0].textContent)) {
                                optionList.children[i].setAttribute('class', 'option correct');
                            }
                        }
                    }
                    for (let i = 0; i < optionListLen; i++)
                        optionList.children[i].classList.add('disabled');

                    nextBtn.classList.add('active');

                    console.log(`::: ${import.meta.url}: optionSelected: ${questResData.had_error}, ${questResData.message}`);
                    console.log(`::: ${import.meta.url}: optionSelected: ${userAnswer}, ${questResData.answer_server.answer}`);
                })
                .catch(error => {
                    throw error;
                });


            // (async () => {
            //     let userAnswer = answer.children[0].textContent;
            //     let optionListLen = optionList.children.length;
            //     const questRes = await fetch(PROJECT_ROOT_DIR + 'src/section-quest.php', {
            //         method: 'POST',
            //         headers: {
            //             'Content-Type': 'application/json'
            //         },
            //         body: JSON.stringify({
            //             'request_body_type': 'check_answer',
            //             'qa_id': currentQAId,
            //             'quest_type': questype,
            //             'your_answer': userAnswer
            //         })
            //     });
            //     const questResData = await questRes.json();
            //     console.log(`::: ${import.meta.url}: ${questResData.quest_info.current_item_number}`);
            //     console.log(`::: ${import.meta.url}: ${questResData.message}, ${questResData.quest_info.qa_id}`);
            //     console.log(`::: ${import.meta.url}: ${userAnswer}, ${questResData.answer_server.is_correct}`); //REM: TODO-HERE; something wrong with this one
            //     console.log(`::: ${import.meta.url}: ${userAnswer}, ${questResData.answer_server.answer}`);
            //     if (!questResData.had_error && questResData.answer_server.is_correct) {
            //         answer.classList.add('correct');
            //         userScore += 1;
            //         headerScore();
            //     } else {
            //         answer.classList.add('incorrect');
            //         for (let i = 0; i < optionListLen; i++) {
            //             if (questResData.answer_server.answer.includes(optionList.children[i].children[0].textContent)) {
            //                 optionList.children[i].setAttribute('class', 'option correct');
            //             }
            //         }
            //     }

            //     for (let i = 0; i < optionListLen; i++)
            //         optionList.children[i].classList.add('disabled');

            //     nextBtn.classList.add('active');
            // })();
        }

        function questionCounter() {
            const questionTotal = document.querySelector('.question-total');
            questionTotal.textContent = `${currentItemNumber} of ${totalItems} Questions`;
        }

        function headerScore() {
            const headerScoreText = document.querySelector('.header-score');
            headerScoreText.textContent = `Score: ${userScore} / ${totalItems}`;
        }

        function showResultBox() {
            //REM: Assuming quizBox, resultBox, userScore, and questions are defined elsewhere
            quizBox.classList.remove('active');
            resultBox.classList.add('active');
            const scoreText = document.querySelector('.score-text');
            scoreText.textContent = `Your Score ${userScore} out of ${totalItems}`;

            const circularProgress = document.querySelector('.circular-progress');
            const progressValue = document.querySelector('.progress-value');
            let progressStartValue = -1;
            let progressEndValue = (userScore / totalItems) * 100;
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

    } catch (error) {
        console.log(`::: ${import.meta.url}: ${error}`);
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