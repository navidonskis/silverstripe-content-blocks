/** global: google */

import Marker from './Marker';
import {createRandomId} from '../functions/createRandomId';

class GoogleMap {

    constructor(container, options) {
        this.instanceId = options.instanceId || '';
        this.container = container;
        this.markers = [];
        this.map = null;

        this.options = {};
        this.defaults = {
            map: {
                center: {
                    lat: 55.1309504,
                    lng: 24.5499231
                },
                zoom: 7,
                scrollwheel: false,
                navigationControl: true,
                mapTypeControl: false,
                scaleControl: true,
                draggable: true,
                streetViewControl: false
            },
            /**
             * show info window event type:
             *  click - only show window when clicking on marker
             *  hover - only show window when mouse over/out marker
             */
            infoWindowOn: 'click',
            globalMarkerIcon: ''
        };

        // set options from data attribute
        this.placeInstanceId(options);
        this.populateOptions(options);
        this.initMap();
        this.collectMarkers();
    }

    placeInstanceId() {
        if (this.instanceId) {
            this.container.setAttribute('instance', this.instanceId);
        }
    }

    populateOptions(options = {}) {
        if('map' in options && 'styles' in options.map) {
            options.map.styles = JSON.parse(options.map.styles);
        }

        if ('map' in options) {
            options.map = Object.assign(this.defaults.map, options.map);
        }

        this.options = Object.assign(this.options, this.defaults, options);
    }

    initMap() {
        this.map = new google.maps.Map(this.container, this.options.map);

        google.maps.event.addDomListener(window, 'resize', () => {
            this.map.setCenter(this.options.map.center);
        });
    }

    collectMarkers() {
        let data = this.container.getAttribute('data-markers');

        if (data != '' || typeof data != 'undefined') {
            data = JSON.parse(data);
        }

        data.forEach((option) => {
            this.markers.push(new Marker(Object.assign(option, {
                instanceId: createRandomId(),
                infoWindowOn: this.options.infoWindowOn,
                icon: this.options.globalMarkerIcon != '' ? this.options.globalMarkerIcon : '',
                events: {
                    onClick: (current) => this.markers.forEach((marker) => {
                        if (current.getInstanceId() != marker.getInstanceId()) {
                            marker.close();
                        }
                    })
                }
            }), this.map));
        });
    }
}

export default GoogleMap;