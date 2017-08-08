import Video from './blocks/video/modules/Video';

document.addEventListener("DOMContentLoaded", () => {
    window.HELP_IMPROVE_VIDEOJS = false;

    let elements = document.querySelectorAll('*[data-module="video-block"]');
    if (elements.length > 0) {
        window.videos = [];

        [].forEach.call(elements, (element) => {
            window.videos.push(new Video(element));
        });
    }
});