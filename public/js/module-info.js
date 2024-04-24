// export const PROJECT_ROOT = `${__dirname(__dirname)}${DIRECTORY_SEPARATOR}`;
// export const PROJECT_RESOURCE = `${PROJECT_ROOT}${DIRECTORY_SEPARATOR}resources${DIRECTORY_SEPARATOR}`;

//REM: Determine the base URL of the current module
const baseUrl = new URL(
    import.meta.url
).origin;

export const PROJECT_ROOT_DIR = `${baseUrl}/`;
//REM: Define the project's resource directory relative to the base URL
export const PROJECT_RESOURCE_DIR = `${baseUrl}/public/res/`;

export function sleep(milliseconds) {
    return new Promise(resolve => setTimeout(resolve, milliseconds));
}


const AUDIO = document.getElementById('BGM_001');

export function bgmPlay() {
    if (AUDIO) {
        AUDIO.play()
            .then(() => console.log(`::: ${import.meta.url}: Audio playback started`))
            .catch(error => console.error(`::: ${import.meta.url}: Error playing audio: `, error));
    } else {
        console.error(`::: ${import.meta.url}: Audio element not found`);
    }
}