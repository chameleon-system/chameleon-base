/**
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
CHAMELEON.CORE.FieldGeoCoordinates = CHAMELEON.CORE.FieldGeoCoordinates || {};

CHAMELEON.CORE.FieldGeoCoordinates =
{
    /**
     * null|string
     */
    latitude: null,
    /**
     * null|string
     */
    longitude: null,
    /**
     * string|null
     */
    fieldName: null,

    /**
     * string|null
     */
    addressNotFound: null,
    /**
     * string|null
     */
    emptyAddress: null,
    /**
     * string|null
     */
    mapId: null,

    /**
     * string|null
     */
    marker: null,

    /**
     * string
     */
    wrongLatitude: '',

    /**
     * string
     */
    wrongLongitude: '',


    /**
     * @param {string} fieldName
     * @param {string} moduleUrl
     */
    init: function (fieldName, moduleUrl) {
        const inputs = document.querySelectorAll('.field' + fieldName + ' .fieldMapCoordinate');
        inputs.forEach(function(input) {
            input.addEventListener('blur', function() {
                CHAMELEON.CORE.FieldGeoCoordinates.updateCoordinates(fieldName);
            });
        });

        const openBtn = document.querySelector('.openOSM');
        if (openBtn) {
            openBtn.addEventListener('click', function () {
                CHAMELEON.CORE.FieldGeoCoordinates.openMap(fieldName, moduleUrl);
            });
        }
    },

    /**
     * @param {string} fieldName
     */
    updateCoordinates: function (fieldName) {
        var lat = document.getElementById(fieldName + '_lat').value;
        var lng = document.getElementById(fieldName + '_lng').value;
        document.getElementById(fieldName).value = lat + '|' + lng;
    },

    /**
     * @param {string} fieldName
     * @param {string} moduleUrl
     */
    openMap: function (fieldName, moduleUrl) {
        let lat, lng;
        let latField = document.querySelector('#' + fieldName + '_lat');
        let lngField = document.querySelector('#' + fieldName + '_lng');

        const validLatPattern = /^-?([1-8]?\d(\.\d+)?|90(\.0+)?)$/; // Validiert Werte zwischen -90 und 90
        const validLngPattern = /^-?((1[0-7]\d(\.\d+)?)|([1-9]?\d(\.\d+)?|180(\.0+)?))$/; // Validiert Werte zwischen -180 und 180

        if (latField) {
            lat = latField.value;
            if (lat !== '' && !validLatPattern.test(lat)) {
                alert(CHAMELEON.CORE.FieldGeoCoordinates.wrongLatitude);
                return;
            }
        }

        if (lngField) {
            lng = lngField.value;
            if (lng !== '' && !validLngPattern.test(lng)) {
                alert(CHAMELEON.CORE.FieldGeoCoordinates.wrongLongitude);
                return;
            }
        }
        CreateModalIFrameDialogCloseButton(moduleUrl + '&lat=' + lat + '&lng=' + lng + '&sFieldName=' + fieldName);
    },
    /**
     * @param {string} mapId
     * @param {string} fieldName
     * @param {string} lat
     * @param {string} lng
     * @param {string} emptyAddress
     * @param {string} addressNotFound
     */
    initAddressPicker: function (mapId, fieldName, lat, lng, addressNotFound, emptyAddress) {
        this.mapId = mapId;
        this.fieldName = fieldName;
        this.latitude = lat;
        this.longitude = lng;
        this.addressNotFound = addressNotFound;
        this.emptyAddress = emptyAddress;
        this.marker = null;

        if ('' !== lat && '' !== lng) {
            map = L.map(mapId).setView([lat, lng], 16);
            this.marker = L.marker([lat, lng]).addTo(map).bindPopup(`Lat: ${lat}, Lng: ${lng}`).openPopup();
        } else {
            map = L.map(mapId).setView([51.1657, 10.4515], 6); // Mitte von Deutschland
        }

        L.tileLayer('https://{s}.tile.openstreetmap.org/{z}/{x}/{y}.png', {
            maxZoom: 19
        }).addTo(map);

        map.on('click', function(e) {
            document.getElementById('addressError').innerText = '';
            CHAMELEON.CORE.FieldGeoCoordinates.latitude = e.latlng.lat;
            CHAMELEON.CORE.FieldGeoCoordinates.longitude = e.latlng.lng;

            if (CHAMELEON.CORE.FieldGeoCoordinates.marker !== null) {
                map.removeLayer(CHAMELEON.CORE.FieldGeoCoordinates.marker);
            }
            CHAMELEON.CORE.FieldGeoCoordinates.marker = L.marker(e.latlng).addTo(map).bindPopup(`Lat: ${CHAMELEON.CORE.FieldGeoCoordinates.latitude}, Lng: ${CHAMELEON.CORE.FieldGeoCoordinates.longitude}`).openPopup();
        });

        document.getElementById('btnFindAddress').addEventListener('click', function () {
            CHAMELEON.CORE.FieldGeoCoordinates.searchAddress();
        });

        document.getElementById('addressPickerForm').addEventListener('submit', function (event) {
            event.preventDefault();
            CHAMELEON.CORE.FieldGeoCoordinates.searchAddress();
        });

        document.getElementById('btnSaveCoordinates').addEventListener('click', function () {
            CHAMELEON.CORE.FieldGeoCoordinates.saveCoordinates();
            window.parent.CloseModalIFrameDialog();
        });

        document.getElementById('btnClose').addEventListener('click', function () {
            window.parent.CloseModalIFrameDialog();
        });
    },

    saveCoordinates: function () {
        let lat = CHAMELEON.CORE.FieldGeoCoordinates.latitude;
        let lng = CHAMELEON.CORE.FieldGeoCoordinates.longitude;
        const fieldName = CHAMELEON.CORE.FieldGeoCoordinates.fieldName;
        let fieldLat = window.parent.document.getElementById(fieldName + '_lat');
        if (fieldLat) {
            fieldLat.value = lat;;
        }
        let fieldLng = window.parent.document.getElementById(fieldName + '_lng');
        if (fieldLng) {
            fieldLng.value = lng;
        }
        var fieldCoords = window.parent.document.getElementById(fieldName);
        if (fieldCoords) {
            fieldCoords.value = lat + '|' + lng;
        }
    },

    searchAddress: function () {
        const address = document.getElementById('address').value;
        if (address) {
            L.Control.Geocoder.nominatim().geocode(address, function(results) {
                if (results && results.length > 0) {
                    document.getElementById('addressError').innerText = '';
                    const result = results[0];
                    CHAMELEON.CORE.FieldGeoCoordinates.latitude = result.center.lat;
                    CHAMELEON.CORE.FieldGeoCoordinates.longitude = result.center.lng;
                    if (CHAMELEON.CORE.FieldGeoCoordinates.marker) {
                        map.removeLayer(CHAMELEON.CORE.FieldGeoCoordinates.marker);
                    }
                    CHAMELEON.CORE.FieldGeoCoordinates.marker = L.marker(result.center).addTo(map).bindPopup(`Lat: ${result.center.lat}, Lng: ${result.center.lng}`).openPopup();
                    map.setView(result.center, 16);
                } else {
                    document.getElementById('addressError').innerText = CHAMELEON.CORE.FieldGeoCoordinates.addressNotFound;
                }
            });
        } else {
            document.getElementById('addressError').innerText = CHAMELEON.CORE.FieldGeoCoordinates.emptyAddress;
        }
    },

};
