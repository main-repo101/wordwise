//REM: I Don't know if the 'async' works here in a proper way? For now it is working, 
//REM: I will study it in deep later on...

import {
    PROJECT_ROOT_DIR,
    sleep,
    bgmPlay
} from '../module-info.js';


console.log(`::: ${import.meta.url} INIT`);

export const sectionHomeInit = async () => {
    await sleep(150); //REM: TODO-HERE; this is temporary fix/implementation;
    // const AUDIO = document.getElementById("BGM_001");
    const SECTION_HOME = document.getElementById("SECTION_HOME");
    // const BTN_NAV_HOME = document.getElementById("BTN_NAV_HOME");
    const BTN_START_QUIZ = document.getElementById("BTN_START_QUIZ");
    const SECTION_POPUP_INSTRUCTION = document.getElementById("SECTION_POPUP_INSTRUCTION");
    // const BTN_CONTINUE = document.getElementById("BTN_CONTINUE");
    const BTN_EXIT = document.getElementById("BTN_EXIT");
    const PNL_LOG_OUT = document.getElementById("PNL_LOG_OUT");
    const PNL_PROFILE = document.getElementById("PNL_PROFILE"); //REM: TODO-HERE; make it later...
    const BTN_PROFILE_CANCEL = document.querySelector("#BTN_PROFILE .cancel");
    const BTN_PROFILE_LOG_OUT = document.querySelector("#BTN_PROFILE .log-out");
    try {
        BTN_PROFILE_CANCEL.onclick = (event) => {
            event.preventDefault();
            PNL_LOG_OUT.classList.toggle('hidden')
            PNL_PROFILE.classList.toggle('hidden')
        }
        BTN_PROFILE_LOG_OUT.onclick = async (event) => {
            window.location.href = `/`;
            const response = await fetch(PROJECT_ROOT_DIR + 'src/section-selection-sign-out.php', {
                method: 'POST'
            });
        }
        BTN_START_QUIZ.onclick = (event) => {
            bgmPlay();
            SECTION_HOME.classList.toggle('hidden');
            SECTION_POPUP_INSTRUCTION.classList.toggle('hidden');
        };
        BTN_EXIT.onclick = (event) => {
            event.preventDefault();
            SECTION_HOME.classList.toggle('hidden');
            SECTION_POPUP_INSTRUCTION.classList.toggle('hidden');
        };
    } catch (error) {
        console.log(`::: ${import.meta.url}: ${error}`);
    }
};