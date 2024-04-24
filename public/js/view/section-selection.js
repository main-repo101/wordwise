import {
    PROJECT_ROOT_DIR,
    sleep,
    bgmPlay
} from '../module-info.js';

console.log(`::: ${import.meta.url}: INIT`);

function debounce(func, delay) {
    let timeoutId;
    return function() {
        const context = this;
        const args = arguments;
        clearTimeout(timeoutId);
        timeoutId = setTimeout(function() {
            func.apply(context, args);
        }, delay);
    };
}

const LOADING = document.getElementById('LOADING');
LOADING.classList.toggle('hidden');

//REM: I Don't know if the 'async' works here in a proper way? For now it is working, 
//REM: I will study it in deep later on...
export const sectionSelectionInit = async () => {
    await sleep(150); //REM: TODO-HERE; this is temporary fix/implementation;
    // const AUDIO = document.getElementById("BGM_001");
    const SECTION_SELECTION = document.getElementById("SECTION_SELECTION");
    // const IMG_PRE_TEST = document.getElementById("IMG_PRE_TEST");
    // const IMG_POST_TEST = document.getElementById("IMG_POST_TEST");
    const SECTION_VERIFY = document.getElementById("SECTION_VERIFY");
    // const PANEL_USERNAME = document.getElementById("PANEL_USERNAME");
    const MSG_VERIFIER = document.getElementById("MSG_VERIFIER");
    const VERIFY_INDICATOR = document.getElementById("VERIFY_INDICATOR");
    // const IMG_VERIFY_INDICATOR = document.getElementById("IMG_VERIFY_INDICATOR");
    const FORM_VERIFY = document.getElementById("FORM_VERIFY");
    const TXT_USERNAME = document.getElementById("USERNAME");
    const BTN_TXT_SUBMIT = document.getElementById("SUBMIT");
    const BTN_NAV_PROFILE = document.getElementById("BTN_NAV_PROFILE");
    const PNL_PROFILE_NAME = document.getElementById("PNL_PROFILE_NAME");
    const PNL_LOG_OUT = document.getElementById("PNL_LOG_OUT");
    const PNL_PROFILE = document.getElementById("PNL_PROFILE"); //REM: TODO-HERE; make it later...

    try {
        (async () => {
            try {
                const response = await fetch(PROJECT_ROOT_DIR + 'src/section-selection-init.php', {
                    method: 'POST'
                });
                const data = await response.json();
                LOADING.classList.toggle('hidden');
                console.log(`::: ${import.meta.url}: is_logged_in = ${data.is_logged_in}, ${data.message}, ${data.username}`);
                PNL_PROFILE_NAME.textContent = `"${data.username}"`;
                //REM: TODO-HERE; temp impl
                BTN_NAV_PROFILE.addEventListener('click', async (event) => {
                    // const response = await fetch(PROJECT_ROOT_DIR + 'src/section-selection-sign-out.php', {
                    //     method: 'POST'
                    // });
                    event.preventDefault();
                    PNL_LOG_OUT.classList.toggle('hidden')
                    PNL_PROFILE.classList.toggle('hidden')
                });
                if (data.is_logged_in) {
                    BTN_NAV_PROFILE.classList.toggle('hidden');
                    SECTION_VERIFY.classList.toggle('hidden');
                    SECTION_SELECTION.classList.toggle('hidden');
                } else {
                    TXT_USERNAME.focus();
                    FORM_VERIFY.addEventListener('submit', (event) => {
                        event.preventDefault();
                        history.replaceState(null, '', window.location.href);
                    });
                    BTN_TXT_SUBMIT.addEventListener('click', async (event) => {
                        event.preventDefault();
                        try {
                            const signInRes = await fetch(PROJECT_ROOT_DIR + 'src/section-selection-sign-in.php', {
                                method: 'POST',
                                headers: {
                                    'Content-Type': 'application/json'
                                },
                                body: JSON.stringify({
                                    'username': TXT_USERNAME.value
                                })
                            });
                            const signInResData = await signInRes.json();
                            if (!signInResData.had_error && signInResData.is_logged_in) {
                                console.log(`::: ${import.meta.url}: is_logged_in = ${signInResData.is_logged_in}, ${signInResData.message}, ${signInResData.username}`);
                                BTN_NAV_PROFILE.classList.toggle('hidden');
                                SECTION_VERIFY.classList.toggle('hidden');
                                SECTION_SELECTION.classList.toggle('hidden');
                                //REM: TODO-HERE;
                                document.getElementById('PNL_PROFILE_NAME').textContent = signInResData.username;
                            }
                        } catch (error) {
                            console.log(error);
                        }
                    });
                    TXT_USERNAME.addEventListener('input', debounce(async function(event) {
                        event.preventDefault();
                        const signInVerifierRes = await fetch(PROJECT_ROOT_DIR + 'src/section-selection-sign-in-verifier.php', {
                            method: 'POST',
                            headers: {
                                'Content-Type': 'application/json'
                            },
                            body: JSON.stringify({
                                'username': event.target.value
                            })
                        });

                        //REM: TODO-HERE; refactor them...
                        MSG_VERIFIER.classList.remove('hidden');
                        const signInResData = await signInVerifierRes.json();
                        if (signInResData.had_error) {
                            MSG_VERIFIER.style.color = 'red';
                            MSG_VERIFIER.textContent = signInResData.message;
                            BTN_TXT_SUBMIT.setAttribute('disabled', '');
                            VERIFY_INDICATOR.style.backgroundColor = 'red'
                            console.log(signInResData.message);
                        } else if (signInResData.participant_id !== 'N/a') { //REM: make enum for it lated...
                            MSG_VERIFIER.textContent = signInResData.message;
                            MSG_VERIFIER.style.color = 'orange';
                            BTN_TXT_SUBMIT.removeAttribute('disabled');
                            BTN_TXT_SUBMIT.style.backgroundColor = 'orange';
                            BTN_TXT_SUBMIT.classList.add('active:bg-white')
                            VERIFY_INDICATOR.style.backgroundColor = 'orange'
                            console.log(signInResData.message);
                        } else {
                            BTN_TXT_SUBMIT.removeAttribute('disabled');
                            MSG_VERIFIER.textContent = signInResData.message;
                            MSG_VERIFIER.style.color = 'lightgreen';
                            BTN_TXT_SUBMIT.style.backgroundColor = '';
                            console.log(signInResData.message);
                            VERIFY_INDICATOR.style.backgroundColor = 'lightgreen'

                        }

                    }, 500));
                }
            } catch (error) {
                console.log(`::: ${import.meta.url}: ${error}`);
            }
        })();

    } catch (error) {
        console.log(`::: ${import.meta.url}: ${error}`);
    }
};