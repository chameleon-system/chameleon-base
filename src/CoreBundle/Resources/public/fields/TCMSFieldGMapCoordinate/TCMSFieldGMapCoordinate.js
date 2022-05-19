/**
 * @see https://geojson.org/
 * @typedef {object} GeoJsonPoint
 * @property {"Point"} type
 * @property {float[]} coordinates - Always 2 items: [ lng, lat ]
 */

/**
 * @see https://geojson.org/
 * @typedef {object} GeoJsonFeature
 * @property {"Feature"} type
 * @property {float[]} bbox - Always 4 items: [ lng, lat, lng, lat ]
 * @property {GeoJsonPoint} geometry
 * @property {object} properties
 * @property {string} properties.category
 * @property {string} properties.display_name
 * @property {float} properties.importance
 * @property {int} properties.osm_id
 * @property {string} properties.osm_type
 * @property {int} properties.place_id
 * @property {int} properties.place_rank
 * @property {string} properties.type
 */

/**
 * @see https://geojson.org/
 * @typedef {object} GeoJsonFeatureCollection
 * @property {"FeatureCollection"} type
 * @property {GeoJsonFeature[]} features
 * @property {string} [licence]
 */

if (typeof CHAMELEON === "undefined" || !CHAMELEON) {
    var CHAMELEON = {};
}
CHAMELEON.CORE = CHAMELEON.CORE || {};
CHAMELEON.CORE.TCMSFieldGMapCoordinate = CHAMELEON.CORE.TCMSFieldGMapCoordinate || {};

CHAMELEON.CORE.TCMSFieldGMapCoordinate =
{
    /**
     * null|string
     */
    latitude: null,
    /**
     * null|string
     */
    longitude: null,

    coordinateMarker: null,

    /**
     * string|null
     */
    coordinateTitle: null,

    /**
     * string|null
     */
    mapId: null,

    /**
     * @param {string} fieldName
     * @param {string} moduleUrl
     */
    init: function (fieldName, moduleUrl) {
        $('.field' + fieldName + ' .fieldGoogleMapCoordinate').on('blur change', function () {
            $('#' + fieldName).val($('#' + fieldName + '_lat').val() + '|' + $('#' + fieldName + '_lng').val());
        });

        $('.field' + fieldName + ' .openMap').on('click', function () {
            CHAMELEON.CORE.TCMSFieldGMapCoordinate.openMap(fieldName, moduleUrl);
        });
    },

    /**
     * @param {string} fieldName
     * @param {string} moduleUrl
     */
    openMap: function (fieldName, moduleUrl) {
        CreateModalIFrameDialogCloseButton(moduleUrl + '&lat=' + $('#' + fieldName + '_lat').val() + '&lng=' + $('#' + fieldName + '_lng').val() + '&sFieldName=' + fieldName);
    },

    /**
     * @param {string} latitude
     * @param {string} longitude
     */
    updateStaticImageMap: function (latitude, longitude) {
        $staticMap = $('#staticMap');
        if($staticMap.length === 0) {
            return;
        }

        var markerSettings = 'color:blue%7Clabel:S%7C' + latitude + ',' + longitude;
        var newStaticGoogleMapUrl = this.replaceQueryParam('markers', markerSettings, $staticMap.attr('src'));
        $staticMap.attr('src', newStaticGoogleMapUrl);
    },

    /**
     * @param {string} param
     * @param {string} newval
     * @param {string} search
     * @returns {string}
     */
    replaceQueryParam: function (param, newval, search) {
        var regex = new RegExp("([?;&])" + param + "[^&;]*[;&]?");
        var query = search.replace(regex, "$1").replace(/&$/, '');

        return (query.length > 2 ? query + "&" : "?") + (newval ? param + "=" + newval : '');
    },

    /**
     * @param {object} mapObject
     * @param {string} mapId
     * @param {string} fieldName
     * @param {string} coordinateTitle
     */
    initAddressPicker: function (mapId, fieldName, coordinateTitle) {

        this.coordinateTitle = coordinateTitle;
        var mapObject = window[mapId];
        this.mapId = mapId;
        var centerLatLng = mapObject.getCenter();
        this.coordinateMarker = new google.maps.Marker({position: centerLatLng});
        this.coordinateMarker.setMap(mapObject);
        this.coordinateMarker.setDraggable(true);

        // show coordinates
        google.maps.event.addListener(this.coordinateMarker, "dragend", function () {
                var point = CHAMELEON.CORE.TCMSFieldGMapCoordinate.coordinateMarker.getPosition();
                CHAMELEON.CORE.TCMSFieldGMapCoordinate.latitude = point.lat();
                CHAMELEON.CORE.TCMSFieldGMapCoordinate.longitude = point.lng();
                CHAMELEON.CORE.TCMSFieldGMapCoordinate.viewCoordinate(point.lat().toFixed(5), point.lng().toFixed(5));
            }
        );

        $('#btnFindAddress').on('click', function () {
            CHAMELEON.CORE.TCMSFieldGMapCoordinate.searchPlace($('#place').val());
        });

        $('#addressPickerForm').on('submit', function () {
            CHAMELEON.CORE.TCMSFieldGMapCoordinate.searchPlace($('#place').val());
            return false;
        });

        $('#btnSaveCoordinates').on('click', function () {
            var lat = CHAMELEON.CORE.TCMSFieldGMapCoordinate.latitude;
            var lng = CHAMELEON.CORE.TCMSFieldGMapCoordinate.longitude;
            $('#' + fieldName + '_lat', window.parent.document).val(lat);
            $('#' + fieldName + '_lng', window.parent.document).val(lng);
            $('#' + fieldName, window.parent.document).val(lat + '|' + lng);
            window.parent.CHAMELEON.CORE.TCMSFieldGMapCoordinate.updateStaticImageMap(lat, lng);
            window.parent.CloseModalIFrameDialog();
        });
    },

    /**
     * @param {string|null} latitude
     * @param {string|null} longitude
     */
    viewCoordinate: function (latitude, longitude) {
        if (latitude && longitude) {
            $("#coordinates").html(CHAMELEON.CORE.TCMSFieldGMapCoordinate.coordinateTitle + ': ' + latitude + ' | ' + longitude);
        } else {
            $("#coordinates").html('');
        }
    },

    /**
     * @param {string} address
     */
    searchPlace: function (address) {
        CHAMELEON.CORE.TCMSFieldGMapCoordinate.geocode(address).then(coordinates => {
            if (!coordinates) {
                CHAMELEON.CORE.TCMSFieldGMapCoordinate.viewCoordinate(null, null);
                return;
            }

            var point = new google.maps.LatLng(coordinates.lat, coordinates.lng);
            CHAMELEON.CORE.TCMSFieldGMapCoordinate.coordinateMarker.setPosition(point);
            CHAMELEON.CORE.TCMSFieldGMapCoordinate.latitude = point.lat();
            CHAMELEON.CORE.TCMSFieldGMapCoordinate.longitude = point.lng();
            CHAMELEON.CORE.TCMSFieldGMapCoordinate.viewCoordinate(
                point.lat().toFixed(5),
                point.lng().toFixed(5),
            );
            var mapObject = window[CHAMELEON.CORE.TCMSFieldGMapCoordinate.mapId];
            mapObject.setCenter(point);
        }).catch(reason => {
            alert("Geocode was not successful for the following reason: " + reason);
        });
    },

    /**
     * @param {string} query
     * @return {Promise<{ lat: float, lng: float }|null, string>}
     */
    geocode: function(query) {
        /**
         * @see https://nominatim.org/release-docs/develop/api/Search/
         * Using geojson output instead of default JSON in order to be compatible with more tools
         * (e.g. selfhosted photon geocoder) by using a standardized data format.
         */
        var url = new URL('https://nominatim.openstreetmap.org/search');
        url.searchParams.set('format', 'geojson');
        url.searchParams.set('limit', '1');
        // Note: `country` sets a 'preference' for results, not a hard limit
        url.searchParams.set('country', 'de');
        url.searchParams.set('q', query);

        return fetch(url, { method: 'GET' })
            .then(response => {
                if (response.status !== 200) {
                    throw response.statusText;
                }
                return response.json();
            })
            .then(response => {
                /** @type {GeoJsonFeatureCollection} response */
                if (response.features.length > 0) {
                    var lng = response.features[0].geometry.coordinates[0];
                    var lat = response.features[0].geometry.coordinates[1];
                    return { lat: lat, lng: lng }
                }

                return null;
            });
    },
};
