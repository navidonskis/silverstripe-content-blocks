import GoogleMapEditor from './blocks/maps/modules/GoogleMapEditor';

(function ($) {
    $.entwine('ss', function ($) {
        $('*[data-module="editor"]').entwine({
            onmatch: function () {

                window.editor = {
                    readyApi: false,
                    instance: null,
                    api: $(this).data('key'),
                    init: () => {
                        window.editor.instance = new GoogleMapEditor($(this)[0], {
                            closures: {
                                onMarkerPlaced: (instance, marker) => {
                                    $.ajax({
                                        url: $(this).data('update-marker-link'),
                                        method: 'GET',
                                        data: marker.getData()
                                    })
                                },
                                onMarkerSave: (instance, marker) => {
                                    $.ajax({
                                        url: $(this).data('update-marker-link'),
                                        method: 'GET',
                                        data: marker.getData()
                                    })
                                },
                                onMarkerDelete: (instance, marker) => {
                                    $.ajax({
                                        url: $(this).data('delete-marker-link'),
                                        method: 'GET',
                                        data: {
                                            InstanceId: marker.options.instanceId
                                        }
                                    })
                                },
                                onZoomChanged: (instance, zoomLevel) => {
                                    $.ajax({
                                        url: $(this).data('zoom-changed-link'),
                                        method: 'GET',
                                        data: {
                                            zoom: zoomLevel
                                        }
                                    })
                                },
                                onCoordinatesChanged: (instance, coordinates) => {
                                    $.ajax({
                                        url: $(this).data('coordinates-changed-link'),
                                        method: 'GET',
                                        data: {
                                            coordinates: coordinates
                                        }
                                    })
                                }
                            }
                        });
                    }
                };

                if (!window.editor.readyApi) {
                    if ($('#google-maps-api').length <= 0) {
                        let script = document.createElement('script');

                        script.id = 'google-maps-api';
                        script.type = 'text/javascript';
                        script.src = `//maps.googleapis.com/maps/api/js?key=${window.editor.api}&libraries=places&callback=window.editor.init`;

                        document.body.appendChild(script);
                        window.editor.readyApi = true;
                    } else {
                        window.editor.readyApi = true;
                        window.editor.init();
                    }
                }
            }
        });
    });
})(jQuery);