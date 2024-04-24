// const {
//     URL
// } = require('url');

// const path = require('path');

// const baseUrl = new URL(
//     'file://' + path.dirname(require.main.filename) + '/'
// );

// const PROJECT_ROOT_DIR = baseUrl.toString();
// //REM: Define the project's resource directory relative to the base URL
// const PROJECT_RESOURCE_DIR = `${baseUrl}/resources/`;

// module.exports = {
//     PROJECT_ROOT_DIR,
//     PROJECT_RESOURCE_DIR
// };

//REM: Define the base URL manually
const baseUrl = window.location.origin;

//REM: Define the project's resource directory relative to the base URL
const PROJECT_RESOURCE_DIR = `${baseUrl}/resources/`;

//REM: Export variables if needed (not necessary in browser environment)
window.PROJECT_ROOT_DIR = baseUrl;
window.PROJECT_RESOURCE_DIR = PROJECT_RESOURCE_DIR;