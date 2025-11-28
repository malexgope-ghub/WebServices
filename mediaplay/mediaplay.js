async function startMediaLoop() {
    try {
        // 1. Obtener lista desde PHP
        const response = await fetch('mediaplay.php');
        const mediaList = await response.json();

        console.log("MEDIA LIST from mediaplay.php:", mediaList);

        // 2. Reproducir secuencialmente
        for (let i = 0; i < mediaList.length; i++) {
            const media = mediaList[i];
            console.log("playing:", media);
            await playMedia(media);
        }

        // 3. AL TERMINAR TODO EL CICLO → REFRESCAR PÁGINA
        console.log("reloading website...");
        location.reload(); 

    } catch (error) {
        console.error('loading error:', error);
    }
}

function playMedia(media) {
    return new Promise((resolve) => {
        const container = document.getElementById('slideshow');
        container.innerHTML = ''; // clear last content
        let element;

        // ----------------------------------------------------
        //  	IMAGES
        // ----------------------------------------------------
        if (media.type === 'image') {

            element = document.createElement('img');
            element.src = media.file;

            container.appendChild(element);

            startCountdown(media.duration);
            setTimeout(resolve, media.duration * 1000);
        }

        // ----------------------------------------------------
        //  VIDEOS
        // ----------------------------------------------------
        else if (media.type === 'video') {

            element = document.createElement('video');
            element.src = media.file;
            element.autoplay = true;
            element.muted = false;

            element.onerror = () => {
                console.error("ERROR al cargar el video:", media.file);
            };

            container.appendChild(element);

            element.addEventListener('loadedmetadata', () => {
                const duration = Math.floor(element.duration);
                console.log("video duration:", duration, "segundos");
                startCountdown(duration);
            });

            element.onended = () => {
                console.log("Video terminado:", media.file);
                resolve();
            };
        }
    });
}

// ----------------------------------------------------
//  TIME COUNTDOWN
// ----------------------------------------------------
function startCountdown(seconds) {
    const countdown = document.getElementById('countdown');

    let remaining = seconds;
    countdown.textContent = formatTime(remaining);

    clearInterval(window.countdownInterval);

    window.countdownInterval = setInterval(() => {
        remaining--;

        if (remaining >= 0) {
            countdown.textContent = formatTime(remaining);
        }

        if (remaining <= 0) {
            clearInterval(window.countdownInterval);
        }
    }, 1000);
}

function formatTime(sec) {
    const m = Math.floor(sec / 60).toString().padStart(2, '0');
    const s = (sec % 60).toString().padStart(2, '0');
    return `${m}:${s}`;
}

// ----------------------------------------------------
//  INICIAR
// ----------------------------------------------------
startMediaLoop();
