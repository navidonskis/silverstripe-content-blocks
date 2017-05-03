/** global: google */

class Marker {

    constructor(options = {}, map) {
        this.map = map;

        this.defaults = {
            instanceId: null,
            location: null,
            address: '',
            coordinates: '',
            displayWindow: false,
            content: '',
            icon: '',
            markerImage: null,
            draggable: false,
            /**
             * show info window event type:
             *  click - only show window when clicking on marker
             *  hover - only show window when mouse over/out marker
             */
            infoWindowOn: 'click',
            events: {
                onClick: (marker) => {

                },

                onMouseover: (marker) => {

                },

                onMouseout: (marker) => {

                }
            }
        };

        this.temporary = {};

        if ('events' in options) {
            options.events = Object.assign(this.defaults.events, options.events);
        }

        this.options = Object.assign(this.defaults, options);

        this.createInstance();
        this.bindEvents();
    }

    update(settings = {}) {
        this.options = Object.assign(this.options, settings);

        if ('location' in settings) {
            this.instance.setPosition(settings.location);
        }

        if ('coordinates' in settings) {
            let coordinates = settings.coordinates.split(',').map(Number).filter(x => !isNaN(x));
            this.options.location = new google.maps.LatLng(coordinates[0], coordinates[1]);
            this.instance.setPosition(this.options.location);
        }
    }

    getCoordinates() {
        return `${this.instance.getPosition().lat()}, ${this.instance.getPosition().lng()}`;
    }

    getData() {
        return {
            InstanceId: this.options.instanceId,
            Address: this.options.address,
            Coordinates: this.getCoordinates(),
            Content: this.options.content,
            DisplayWindow: this.options.displayWindow
        };
    }

    createInstance() {
        if (this.options.location == null && this.options.coordinates != '') {
            let coordinates = this.options.coordinates.split(',').map(Number).filter(x => !isNaN(x));

            this.options.location = new google.maps.LatLng(coordinates[0], coordinates[1]);
        }

        this.instance = new google.maps.Marker({
            map: this.map,
            icon: this.options.markerImage || this.options.icon,
            position: this.options.location,
            draggable: this.options.draggable
        });

        this.instance.infoWindow = new google.maps.InfoWindow({
            content: this.options.content
        });
    }

    bindEvents() {
        switch (this.options.infoWindowOn) {
            case 'click':

                google.maps.event.addListener(this.instance, 'click', () => {
                    this.options.events.onClick(this);

                    this.open();
                });

                break;

            case 'hover':

                google.maps.event.addListener(this.instance, 'mouseover', () => {
                    this.options.events.onMouseover(this);

                    this.open();
                });

                google.maps.event.addListener(this.instance, 'mouseout', () => {
                    this.options.events.onMouseout(this);

                    this.close();
                });

                break;

            default:
                return;
        }
    }

    getInstanceId() {
        return this.options.instanceId;
    }

    open() {
        if (this.options.displayWindow) {
            this.instance.infoWindow.open(this.map, this.instance);
        }
    }

    close() {
        this.instance.infoWindow.close();
    }
}

export default Marker;