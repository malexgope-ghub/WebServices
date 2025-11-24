
<?php
$folder = 'KIOSK_MEDIA';    //Linux Path in root directory
$images = glob("$folder/*.{jpg,jpeg,png,gif,bmp,webp}", GLOB_BRACE);
$videos = glob("$folder/*.{mp4,webm,ogg}", GLOB_BRACE);
$media = array_merge($images, $videos);
?>
<!DOCTYPE html>
<html lang="es">
<head>
<meta charset="UTF-8">
<title>MediaPlay</title>
<style>
  html, body { margin:0; padding:0; background:black; overflow:hidden; height:100%; width:100%;}

#slideshow {
    position: fixed;
    top: 0;
    left: 0;
    width: 100vw;
    height: 100vh;
    display: flex;
    justify-content: center;
    align-items: center;
}

#slideshow img,
#slideshow video {
    width: 100%;
    height: 100%;
    object-fit: contain; /*can be other distributions*/
}

  #countdown { position:fixed; bottom:20px; right:30px; color:white; font-size:1em; font-family:monospace; background:rgba(0,0,0,0.2); padding:10px 20px; border-radius:10px; }
</style>
</head>
<body>
<div id="slideshow"></div>
<div id="countdown">00:00</div>

<script>
const media = <?php echo json_encode($media, JSON_UNESCAPED_SLASHES); ?>;
const interval = 90000; // images timeout
let currentIndex = 0;
const slideshow = document.getElementById('slideshow');
const countdown = document.getElementById('countdown');

function formatTime(ms) {
  const totalSeconds = Math.floor(ms / 1000);
  const minutes = String(Math.floor(totalSeconds / 60)).padStart(2, '0');
  const seconds = String(totalSeconds % 60).padStart(2, '0');
  const fileName = media[currentIndex];
  return `${fileName} ${currentIndex + 1}/${media.length} ${minutes}:${seconds}`;
}

function startCountdown(duration) {
  let remaining = duration;
  countdown.textContent = formatTime(remaining);
  const timer = setInterval(() => {
    remaining -= 1000;
    countdown.textContent = formatTime(remaining);
    if (remaining <= 0) clearInterval(timer);
  }, 1000);
}

function showNextMedia() {
  slideshow.innerHTML = '';
  const file = media[currentIndex];
  let duration = interval;

  if (/\.(mp4|webm|ogg)$/i.test(file)) {
    // VIDEOS
    const video = document.createElement('video');
    video.src = file;
    video.autoplay = true;
    video.controls = false;
    video.muted = false; // optional, remember able autoplay in browser if is not muted
	video.playsInline = true;
    slideshow.appendChild(video);

    video.onloadedmetadata = () => {	
		video.currentTime = 0; // reload metadata video and keep the cache
		video.play();
      duration = video.duration * 1000;
      startCountdown(duration);
      setTimeout(nextMedia, duration);
    };
  } else {
    // IMAGES
    const img = document.createElement('img');
    img.src = file;
    slideshow.appendChild(img);
    startCountdown(duration);
    setTimeout(nextMedia, duration);
  }
}

function nextMedia() {
  currentIndex++;
  if (currentIndex < media.length) {
    showNextMedia();
  } else {
    location.reload();
  }
}

showNextMedia();

// click to full screen
document.addEventListener('click', () => {
  if (!document.fullscreenElement) {
    document.documentElement.requestFullscreen();
  }
});
</script>
</body>
</html>
