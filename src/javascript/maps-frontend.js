import GoogleMap from './blocks/maps/modules/GoogleMap';
import {createRandomId} from './blocks/maps/functions/createRandomId';

class App {
    constructor() {
        window.GoogleMap = {
            readyApi: false,
            api: document.querySelector('meta[property="maps:key"]').getAttribute('content'),
            instances: [],
            init: () => {
                [].forEach.call(document.querySelectorAll('*[data-module="google-map"]'), container => {
                    let instanceId = createRandomId();
                    let options = container.getAttribute('data-options');

                    if (typeof options == 'undefined' || options == '') {
                        options = {};
                    } else {
                        options = JSON.parse(options);
                    }

                    window.GoogleMap.instances.push(new GoogleMap(container, Object.assign({
                        instanceId: instanceId
                    }, options)));
                });
            }
        };
    }

    init() {
        if (!window.GoogleMap.readyApi && (window.GoogleMap.api != '' || typeof window.GoogleMap.api != 'undefined')) {
            let script = document.createElement('script');

            script.type = 'text/javascript';
            script.src = `//maps.googleapis.com/maps/api/js?key=${window.GoogleMap.api}&libraries=places&callback=window.GoogleMap.init`;

            document.body.appendChild(script);
            window.GoogleMap.readyApi = true;
        }
    }
}

document.addEventListener("DOMContentLoaded", () => {
    if (!('GoogleMap' in window)) {
        const APP = new App();

        APP.init();
    }
});