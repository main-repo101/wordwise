import {
    PROJECT_ROOT_DIR,
    PROJECT_RESOURCE_DIR
} from './module-info.js';

import {
    Dev,
} from './Dev.js';

const THIS_FILE_LOCATION =
    import.meta.url;


export const DEV = Dev.getInstance();
DEV.setIsDebugMode(false);


document.addEventListener('DOMContentLoaded', () => {
    if (DEV.isInDebugMode()) {
        document.getElementById('CONTENT').style.display = "none";
        fetch('/src/debug.php')
            .then(response => response.text())
            .then(data => {
                console.log(data);
            })
            .catch(error => console.log(error));

    } else
        document.getElementById('CONTENT').style.display = "block";

});