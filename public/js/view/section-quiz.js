//REM: I Don't know if the 'async' works here in a proper way? For now it is working, 
//REM: I will study it in deep later on...

import {
    PROJECT_ROOT_DIR,
    sleep,
    bgmPlay
} from '../module-info.js';

import {
    quizAsap
} from '../section-quiz-asap-v2.js';

import {
    QuestType
} from './QuestType.js';

import {
    UrlPathname
} from '../model/UrlPathname.js'

console.log(`::: ${import.meta.url}: INIT`);

export const sectionQuizInit = async () => {
    try {
        const URL_PATHNAME = window.location.pathname.toString().trim();

        if (URL_PATHNAME === UrlPathname.PRE_TEST || URL_PATHNAME === UrlPathname.POST_TEST) {

            await sleep(150); //REM: TODO-HERE; this is temporary fix/implementation;

            console.log(`::: ${import.meta.url}: ${UrlPathname.PRE_TEST}`);

            switch (URL_PATHNAME) {
                case UrlPathname.PRE_TEST:
                    quizAsap(QuestType.PRE);
                    break;
                default:
                    quizAsap(QuestType.POST);
            }
        }
    } catch (error) {
        console.log(`::: ${import.meta.url}: ${error}`);
    }
};