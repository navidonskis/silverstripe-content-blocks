import {createNavigationButton} from '../functions/createNavigationButton';
import {createAddnewContainer} from '../functions/createAddnewContainer';
import {createEditorContainer} from '../functions/createEditorContainer';
import {createRandomId} from '../functions/createRandomId';
import Marker from './Marker';

class GoogleMapEditor {

    constructor(container, options = {}) {
        this._instance = null;
        this._container = container;
        this._markers = [];
        this._options = {};
        this._navigationButton = null;

        this._defaults = {
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
            sprite: 'assets/images/sprite.png',
            closures: {
                onLoad: function (instance) {

                },

                onClickNavigationButton: function (instance) {
                    instance.onClickNavigationButton();
                },

                onMarkerClick: function (instance, marker) {
                    instance.onMarkerClick(marker);
                },

                onMarkerPlaced: function (instance, marker) {

                },

                onMarkerSave: function (instance, marker) {

                },

                onMarkerDelete: function (instance, marker) {

                },

                onZoomChanged: function (instance, zoomLevel) {

                },

                onCoordinatesChanged: function (instance, coordinates) {

                },
            },
            translations: {
                close: 'Close',
                address: 'Type your address',
                editMarker: 'Edit marker',
                displayInfo: 'Display info window?',
                content: 'Content',
                delete: 'Delete',
                save: 'Save'
            }
        };

        this.populateOptions(options);
        this.createInstance();
        this.populateMarkers();
        this.bindEvents();
    }

    getOptions() {
        return this._options;
    }

    bindEvents() {
        google.maps.event.addListenerOnce(this._instance, 'tilesloaded', () => {
            this._options.closures.onLoad(this);

            let sprite = this._options.sprite;
            let container = this._container;

            this._navigationButton = createNavigationButton(sprite, container);

            // initialize navigation button events
            this._navigationButton.addEventListener('click', () => {
                this._options.closures.onClickNavigationButton(this);
            });
        });

        google.maps.event.addListener(this._instance, 'zoom_changed', () => {
            this._options.closures.onZoomChanged(this, this._instance.getZoom());
        });

        google.maps.event.addListener(this._instance, 'dragend', () => {
            let center = this._instance.getCenter();
            this._options.closures.onCoordinatesChanged(this._instance, `${center.lat()},${center.lng()}`);
        });

        google.maps.event.addListener(this._instance, 'click', (event) => {
            this.placeMarker(event.latLng);
        });
    }

    onClickNavigationButton() {
        let container = createAddnewContainer(this._container, this._options.translations);
        let addressField = container.querySelector('input[name="address"]');

        addressField.focus();

        let searchBox = this.createSearchBox(addressField);

        searchBox.addListener('places_changed', () => {
            let places = searchBox.getPlaces();

            if (places.length == 0) return;

            let bounds = new google.maps.LatLngBounds();

            places.forEach(place => {
                if (!place.geometry) return;

                this.placeMarker(place.geometry.location, {address: place.formatted_address});

                if (place.geometry.viewport) {
                    bounds.union(place.geometry.viewport);
                } else {
                    bounds.extend(place.geometry.location);
                }
            });

            this._instance.fitBounds(bounds);
            container.parentNode.removeChild(container);
        });
    }

    collectFieldsData(container) {
        return {
            content: container.querySelector('textarea[name="content"]').value,
            displayWindow: container.querySelector('input[type="checkbox"]').checked
        };
    }

    onMarkerClick(marker) {
        if (!(marker instanceof Marker)) {
            return;
        }

        let container = createEditorContainer(this._container, this._options.translations, {
            address: marker.options.address,
            content: marker.options.content,
            displayWindow: marker.options.displayWindow
        });

        let searchBox = this.createSearchBox(container.querySelector('input[name="address"]'), container, false, marker);
        let bounds = null;

        searchBox.addListener('places_changed', () => {
            let places = searchBox.getPlaces();

            bounds = new google.maps.LatLngBounds();

            places.forEach(place => {
                if (!place.geometry) return;

                marker.temporary = Object.assign(marker.temporary, {
                    location: place.geometry.location,
                    address: place.formatted_address
                });

                if (place.geometry.viewport) {
                    bounds.union(place.geometry.viewport);
                } else {
                    bounds.extend(place.geometry.location);
                }
            });
        });

        container.querySelector('button[name="save"]').addEventListener('click', () => {
            let data = this.collectFieldsData(container);
            // save all temporary object to options and fit bounds if location was changed
            marker.options = Object.assign(marker.options, marker.temporary, data);

            if ('location' in marker.temporary) {
                marker.instance.setPosition(marker.options.location);
                this._instance.fitBounds(bounds);
            }

            if ('content' in data) {
                marker.instance.infoWindow.setContent(data.content);
            }

            marker.temporary = {};

            this._options.closures.onMarkerSave(this, marker);

            container.parentNode.removeChild(container);
        });

        container.querySelector('button[name="delete"]').addEventListener('click', () => {
            this._options.closures.onMarkerDelete(this, marker);
            this.removeMarker(marker);
            container.parentNode.removeChild(container);
        });
    }

    createSearchBox(inputField) {
        return new google.maps.places.SearchBox(inputField);
    }

    populateOptions(options = {}) {
        if ('map' in options) {
            options.map = Object.assign(this._defaults.map, options.map);
        }

        if ('closures' in options) {
            options.closures = Object.assign(this._defaults.closures, options.closures);
        }

        this._options = Object.assign(this._defaults, options);

        let data = this.getJSONOrEmpty(this._container.getAttribute('data-options'));

        if ('map' in data) {
            this._options.map = Object.assign(this._options.map, data.map);
            delete data['map'];
        }

        if ('closures' in data) {
            this._options.closures = Object.assign(this._options.closures, data.closures);
            delete data['closures'];
        }

        this._options = Object.assign(this._options, data);
    }

    populateMarkers() {
        let markers = this.getJSONOrEmpty(this._container.getAttribute('data-markers'));

        if(markers) {
            markers.forEach(marker => {
                let coordinates = marker.coordinates.split(',').map(Number).filter(x => !isNaN(x));

                this.placeMarker(new google.maps.LatLng(coordinates[0], coordinates[1]), {
                    address: marker.address || '',
                    content: marker.content || '',
                    displayWindow: marker.displayWindow,
                    instanceId: marker.instanceId
                }, false);
            });
        }
    }

    createInstance() {
        this._instance = new google.maps.Map(this._container, this._options.map);
    }

    placeMarker(location, settings = {}, useClosure = true) {
        let icons = {
            default: new google.maps.MarkerImage(
                this._options.sprite,
                new google.maps.Size(26, 32),
                new google.maps.Point(0, 32)
            ),
            active: new google.maps.MarkerImage(
                this._options.sprite,
                new google.maps.Size(26, 32),
                new google.maps.Point(32, 32)
            )
        };

        let marker = new Marker(Object.assign({
            instanceId: createRandomId(),
            markerImage: icons.default,
            location: location,
            draggable: true,
            infoWindowOn: 'hover',
            events: {
                onMouseover: (currentMarker) => {
                    currentMarker.instance.setIcon(icons.active);
                },

                onMouseout: (currentMarker) => {
                    currentMarker.instance.setIcon(icons.default);
                }
            }
        }, settings), this._instance);

        google.maps.event.addListener(marker.instance, 'click', () => {
            this._options.closures.onMarkerClick(this, marker);
        });

        google.maps.event.addListener(marker.instance, 'dragend', () => {
            this._options.closures.onMarkerSave(this, marker);
        });

        if (useClosure) {
            this._options.closures.onMarkerPlaced(this, marker);
        }

        this._markers.push(marker);
    }

    removeMarker(marker) {
        marker.instance.setMap(null);
        let key = false;

        this._markers.forEach((item, index) => {
            if (marker.instanceId == item.instanceId) {
                key = index;
            }
        });

        if (!isNaN(key)) {
            delete this._markers[key];
        }
    }

    getMarkers() {
        return this._markers;
    }

    getJSONOrEmpty(input) {
        if (typeof input == 'undefined' || input == '') {
            return {};
        }

        return JSON.parse(input);
    }
}

export default GoogleMapEditor;