import {utils} from '../../../core/utils';
import videojs from 'video.js';

class Video {

    constructor(container) {
        this.container = container;

        this.player = null;

        this._layouts = {
            wrapper: 'data-wrapper',
            playButton: 'data-clicktoplay',
            container: 'data-container'
        };

        this._options = {
            autoPlay: false,
            controls: true,
            coverImage: false,
            type: 'file',
            embed: false,
            videoTypes: {
                mp4: false,
                webm: false,
                ogg: false
            },
        };

        this.init();

        this._playerOptions = {
            autoplay: this._options.autoPlay,
            controls: this._options.controls,
            sources: (() => {
                let sources = [];

                for (let format in this._options.videoTypes) {
                    let source = this._options.videoTypes[format];

                    if (source) {
                        sources.push({
                            src: source,
                            type: `video/${format}`
                        });
                    }
                }

                return sources;
            })(),
            poster: this._options.coverImage
        };

        this.bindEvents();
        this.simulateAutoPlay();
    }

    init() {
        if (this.container.hasAttribute('data-options')) {
            let options = JSON.parse(this.container.getAttribute('data-options'));

            if ('videoTypes' in options) {
                options.videoTypes = Object.assign(this._options.videoTypes, options.videoTypes);
            }

            this._options = Object.assign(this._options, options);
        }

        for (let key in this._layouts) {
            this._layouts[key] = this.container.querySelector(`*[${this._layouts[key]}]`);
        }
    }

    bindEvents() {
        this._layouts.playButton.addEventListener('click', (event) => this.onClickPlay(event));
    }

    simulateAutoPlay() {
        if (!this._options.autoPlay) return false;

        this._layouts.playButton.click();
    }

    createIframeVideo() {
        let container = this._layouts.container;
        container.innerHTML = utils.render`
    <div class="video-block__container--embed">
        <iframe src="${this._options.embed}" width="100%" height="100%" webkitallowfullscreen mozallowfullscreen allowfullscreen></iframe>
    </div>
`;
    }

    createVideoSource() {
        let container = this._layouts.container;

        container.innerHTML = utils.render`
    <video class="video-js">
        <p class="vjs-no-js">
          To view this video please enable JavaScript, and consider upgrading to a web browser that
          <a href="http://videojs.com/html5-video-support/" target="_blank">supports HTML5 video</a>
        </p>
    </video>
`;
        this.player = videojs(container.querySelector('video'), this._playerOptions, () => {

        });
    }

    onClickPlay(event) {
        event.preventDefault();

        if (this.player != null) return false;

        this._playerOptions.autoplay = true; // start play

        if (this._options.type == 'file') {
            this.createVideoSource();
        } else {
            this.createIframeVideo();
        }

        return false;
    }
}

export default Video;