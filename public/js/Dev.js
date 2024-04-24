import {
    PROJECT_ROOT_DIR,
    PROJECT_RESOURCE_DIR
} from './module-info.js';

class Dev {
    constructor() {
        this.isDebugMode = false;
    }

    static getInstance() {
        if (!Dev.instance)
            Dev.instance = new Dev();
        return Dev.instance;
    }

    isInDebugMode() {
        return this.isDebugMode;
    }

    setIsDebugMode(isDebugMode) {
        if (typeof isDebugMode !== 'boolean')
            throw new Error('isDebugMode must be a boolean value');
        this.isDebugMode = isDebugMode;
    }
}

export const DEV = Dev.getInstance();

export const debugIt = async () => {
    // const BG_HOME = document.getElementById('SECTION_CONTENT');
    // const AUDIO = document.getElementById('BGM_001');

    // if (BG_HOME && AUDIO) {
    try {
        if (DEV.isInDebugMode()) {
            // BG_HOME.classList.toggle('bg-home-img');
            // AUDIO.pause(); //REM: ??? don't really know if this is legit nor if it is working?
            // AUDIO.currentTime = 0; //REM: ??? don't really know if this is legit nor if it is working?
            (async () => {
                try {
                    const response = await fetch(PROJECT_ROOT_DIR + 'src/debug/dev.php');
                    const data = await response.text();
                    console.log(`:::PHP_DEBUG: ${import.meta.url}: Debug mode:`);
                    console.log(data);
                } catch (error) {
                    console.log(`:::PHP_DEBUG: ${import.meta.url}: ${error}`);
                }
            })();
        }
    } catch (error) {
        console.log(`:::PHP_DEBUG: ${import.meta.url}: ${error}`);
    }
    // }
};