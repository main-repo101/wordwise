/**
 * 
 * simple dispatch route
 */

import {
    PROJECT_RESOURCE_DIR,
    PROJECT_ROOT_DIR
} from '../../module-info.js';

import {
    DEV
} from '../../Dev.js';

import {
    UrlPathname
} from '../../model/UrlPathname.js';

export const ROUTES = Object.freeze({
    [UrlPathname.NOT_FOUND]: "/public/view/section-not-found.html",
    [UrlPathname.HOME]: "/public/view/section-home.html",
    [UrlPathname.POST_TEST]: "/public/view/section-post-test.html",
    [UrlPathname.PRE_TEST]: "/public/view/section-pre-test.html",
    [UrlPathname.SELECTION]: "/public/view/section-selection.html",
    [UrlPathname.DEBUG]: "/public/view/section-index-debug.html"
});


// export const route = (event) => {
//     event = event || window.event;
//     event.preventDefault();
//     window.history.pushState(null, null, event.target.href);
//     handleLocation();
// };

export const route = async (clickEvent) => {
    clickEvent.preventDefault();
    const href = clickEvent.target.getAttribute('href');
    window.history.pushState(null, null, href);
    await handleLocation();
};


export const handleLocation = async () => {

    const SECTION_CONTENT = document.getElementById("SECTION_CONTENT");
    const PATH = window.location.pathname.toLowerCase().trim();
    let ROUTE = ROUTES[PATH] || ROUTES[404];
    let htmlRawText = "";
    try {
        //REM: TODO-HERE; refactor it...
        if (PATH === UrlPathname.PRE_TEST || PATH === UrlPathname.POST_TEST) {
            const response = await fetch(PROJECT_ROOT_DIR + 'src/section-selection-init.php', {
                method: 'POST'
            });
            const data = await response.json();
            if (!data.is_logged_in)
                ROUTE = ROUTES[UrlPathname.HOME];
            else
                SECTION_CONTENT.classList.toggle('bg-home-img')

            console.log(`::: ${import.meta.url}: ${data.message}`);
        }

        const response = await fetch(ROUTE);
        if (!response.ok) {
            if (response.status === 404)
                throw new Error(`Resource not found, It is defined but not implemented: missing template | view | page for; '${PATH}'`);
            else
                throw new Error(`HTTP error!Status: ${response.status}`);
        }

        htmlRawText = await response.text();

        if (ROUTE === ROUTES[UrlPathname.DEBUG])
            DEV.setIsDebugMode(true);
        else
            DEV.setIsDebugMode(false);

    } catch (error) {
        console.error(`::: ${import.meta.url}: ${error}`);
        //REM: Display a custom message for 404 errors
        htmlRawText = `<h1>404 - ${error.message}</h1>`;

    }

    const x = document.getElementById("SECTION_CONTENT");
    if (x) x.innerHTML = htmlRawText;;
};


// handleLocation();