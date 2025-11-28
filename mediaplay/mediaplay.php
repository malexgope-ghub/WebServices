<?php
function getMedia($folder = 'KIOSK_MEDIA', $imageDuration = 10) {
    $imagenes = glob("$folder/*.{jpg,jpeg,png,PNG,gif,bmp,webp}", GLOB_BRACE);
    $videos = glob("$folder/*.{mp4,webm,ogg}", GLOB_BRACE);

    $mediaList = [];

    foreach ($imagenes as $img) {
        $mediaList[] = [
            'file' => $img,
            'type' => 'image',
            'duration' => $imageDuration
        ];
    }
    foreach ($videos as $video) {
        $mediaList[] = [
            'file' => $video,
            'type' => 'video'
        ];
    }

    return $mediaList;
}

header('Content-Type: application/json');
echo json_encode(getMedia());
?>

