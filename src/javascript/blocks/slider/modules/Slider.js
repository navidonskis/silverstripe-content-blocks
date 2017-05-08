class Slider {

    constructor(element) {
        this.options = {
            infinite: 1,
            classNameFrame: 'slider-block__frame',
            classNameSlideContainer: 'slider-block__slides',
            enableMouseEvents: true,
            classNamePrevCtrl: 'prev',
            classNameNextCtrl: 'right',
            slideSpeed: 300
        };

        this.element = element;
        this.slides = this.element.querySelectorAll('*[data-item]');

        if (this.element.hasAttribute('data-options')) {
            this.options = Object.assign(this.options, JSON.parse(this.element.getAttribute('data-options')));
        }

        this.instance = lory(this.element, this.options);

        this.bindEvents();
    }

    bindEvents() {
        [].forEach.call(this.element.querySelectorAll('*[data-item="video"]'), (videoElement) => {
            let playButton = videoElement.querySelector('.slider-block__overlay--play-button');

            playButton.addEventListener('click', () => {
                this.currentElement = videoElement;

                if (this.currentElement.hasAttribute('data-embed')) {
                    utils.addClass(this.currentElement, 'has-embed');
                    this.embedLink = this.currentElement.getAttribute('data-embed');
                    this.play();
                }
            });
        });

        this.element.addEventListener('after.lory.slide', (events) => {
            this.stop();

            let currentSlide = this.getCurrentSlide(events.detail.currentSlide);

            if (currentSlide && currentSlide.hasAttribute('data-autoplay') && parseInt(currentSlide.getAttribute('data-autoplay')) === 1) {
                this.currentElement = currentSlide;

                if (this.currentElement.hasAttribute('data-embed')) {
                    utils.addClass(this.currentElement, 'has-embed');
                    this.embedLink = this.currentElement.getAttribute('data-embed');
                    this.play();
                }
            }
        });
    }

    getCurrentSlide(index) {
        let currentIndex = index - 1;

        if (currentIndex in this.slides) {
            return this.slides[currentIndex];
        }

        return false;
    }

    stop() {
        if (this.currentElement && utils.hasClass(this.currentElement, 'has-embed')) {
            utils.removeClass(this.currentElement, 'has-embed');
            this.content = this.currentElement.querySelector('.slider-block__content');
            this.content.innerHTML = this.contentBefore;
            this.embedLink = null;

            this.content = null;
            this.currentElement = null;
        }
    }

    play() {
        this.content = this.currentElement.querySelector('.slider-block__content');
        this.contentBefore = this.content.innerHTML;
        this.content.innerHTML = `
    <div class="slider-block__content--embed">
        <iframe src="${this.embedLink}" width="100%" height="100%" webkitallowfullscreen mozallowfullscreen allowfullscreen></iframe>
    </div>
`;
    }
}

export default Slider;