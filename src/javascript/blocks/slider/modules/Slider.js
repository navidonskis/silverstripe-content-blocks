class Slider {

    constructor(element) {
        this.options = {
            infinite: 1,
            classNameFrame: 'slider-block__frame',
            classNameSlideContainer: 'slider-block__slides',
            enableMouseEvents: true,
            classNamePrevCtrl: 'prev',
            classNameNextCtrl: 'right'
        };

        this.element = element;
        this.instance = lory(this.element, this.options);
    }
}

export default Slider;