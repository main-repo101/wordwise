import {
    handleLocation,
    route
} from './route/experimental/route-001.js';

import {
    PROJECT_RESOURCE_DIR,
    PROJECT_ROOT_DIR,
    bgmPlay
} from './module-info.js';

import {
    DEV,
    debugIt
} from './Dev.js';

import {
    sectionHomeInit
} from './view/section-home.js';

import {
    sectionSelectionInit
} from './view/section-selection.js';
import {
    sectionQuizInit
} from './view/section-quiz.js';

console.log(`::: ${import.meta.url}: INIT`);


//REM: TODO-HERE; Refactor it....
const dbQuestAndAnsInit = () => {

    (async () => {
        const ERROR_MSG = document.getElementById('STATUS_MSG');
        const AUDIO = document.getElementById('BGM_001');
        const SECTION_CONTENT = document.getElementById('SECTION_CONTENT');
        let txtHtml;

        try {
            let response;
            if (!DEV.isInDebugMode()) {
                //REM: Ignore...
                // response = await fetch(PROJECT_ROOT_DIR + 'src/quest-and-answer-handler-init.php', {
                //     method: 'POST'
                // });
                // const jsonRes = await response.json();
                // if (jsonRes.had_error)
                //     throw new Error(jsonRes.message);
            } else {
                response = await fetch(PROJECT_ROOT_DIR + 'src/debug/dev.php');
                const txtRes = await response.text();
                console.log(txtRes);
                txtHtml = `<h3>${txtRes}<h3>`;

                ERROR_MSG.classList.remove('hidden');
                SECTION_CONTENT.classList.remove('bg-home-img');
                AUDIO.pause(); //REM: ??? don't really know if this is legit nor if it is working?
                AUDIO.currentTime = 0; //REM: ??? don't really know if this is legit nor if it is working?
            }
        } catch (error) {
            if (DEV.isInDebugMode()) {}
            console.log(`::: ${import.meta.url}: ${error}`);
            txtHtml = `<h3>${error}</h3>`;
        }
        ERROR_MSG.innerHTML = txtHtml;
    })();
}

const init = () => {
    sectionSelectionInit();
    sectionHomeInit();
    sectionQuizInit();
    dbQuestAndAnsInit();
    // debugIt();

};
window.onload = init;

document.addEventListener('DOMContentLoaded', () => {
    handleLocation();
    bgmPlay();
});

window.onpopstate = handleLocation;
window.route = route;
//REM??? I don't get it yet why does it works as intened, but sometimes nope...
// document.getElementById('SECTION_CONTENT').addEventListener('load', init()); //REM??? I don't get it yet why does it works as intened, but sometimes nope...


// //REM??? I don't get it yet why does it works as intened, but sometimes nope...
// window.addEventListener('load', () => {
//     debugIt();
//     sectionHomeInit();
//     sectionSelectionInit();
// });