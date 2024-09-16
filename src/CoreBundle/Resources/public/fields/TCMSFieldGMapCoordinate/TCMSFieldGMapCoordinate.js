/**
 * @deprecated since 7.1.34
 * use FieldGeoCoordinates instead of TCMSFieldGMapCoordinate to use OpenStreetMap instead of Google Maps
 *
 * @typedef {object} GeocodingResult
 * @property {string|null} name
 * @property {float} longitude
 * @property {float} latitude
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

            var point = new google.maps.LatLng(coordinates.latitude, coordinates.longitude);
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
     * @return {Promise<GeocodingResult|null, string>}
     */
    geocode: function(query) {
        return fetch(`/cms/rest/geocode?query=${encodeURIComponent(query)}`, { method: 'GET' })
            .then(response => {
                if (response.status !== 200) {
                    throw response.statusText;
                }
                return response.json();
            })
            .then(response => response[0] ?? null);
    },
};
