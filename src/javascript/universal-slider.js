import Slider from './blocks/slider/modules/Slider';

document.addEventListener("DOMContentLoaded", () => {
    let elements = document.querySelectorAll('*[data-module="slider"]');

    if(elements.length > 0) {
        window.sliders = [];

        [].forEach.call(elements, (element) => {
            window.sliders.push(new Slider(element));
        });
    }
});