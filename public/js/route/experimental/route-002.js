import {
    PROJECT_RESOURCE_DIR,
    PROJECT_ROOT_DIR
} from '../../module-info.js';

//REM: Define a function to handle routing and content loading
const handleLocation = () => {
    //REM: Extract the current pathname from the URL
    const pathname = window.location.pathname;
    //REM: Make an AJAX request to the corresponding PHP file based on the pathname
    fetch(PROJECT_ROOT_DIR + 'src/shs/project_wordwise/controller/experimental/route.php?path=' + pathname)
        .then(response => {
            if (!response.ok) {
                throw new Error(`HTTP error! Status: ${response.status}`);
            }
            return response.text();
        })
        .then(htmlRawText => {
            //REM: Update the content of the element with the id "CONTENT" with the retrieved HTML
            document.getElementById("CONTENT").innerHTML = htmlRawText;
        })
        .catch(error => {
            console.error("Error:", error);
            //REM: Display a custom message in case of error
            document.getElementById("CONTENT").innerHTML = `<h1>Error: ${error.message}</h1>`;
        });
};

//REM: Call handleLocation on page load and when the popstate event occurs
window.onload = handleLocation;
window.onpopstate = handleLocation;