<?php

/*
 * This file is part of the Chameleon System (https://www.chameleonsystem.com).
 *
 * (c) ESONO AG (https://www.esono.de)
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

use ChameleonSystem\CoreBundle\Geocoding\GeocoderInterface;
use ChameleonSystem\CoreBundle\ServiceLocator;

class TGoogleMapEndPoint
{
    /**
     * list of all markers.
     *
     * @var array
     */
    protected $aMarkers = [];

    protected $aIcons = [];
    protected $sMapType = 'HYBRID';

    /**
     * unique ID of the map
     * created on class construct
     * call $oMap->getMapID() to get the id.
     *
     * @var string
     */
    protected $sMapId = '';

    /**
     * V.3 API key - needed for high traffic sites.
     *
     * @var string
     */
    protected $sApiKey = '';

    /**
     * V2 API is deprecated and will be disabled on 30, may 2013!
     *
     * @var int
     */
    protected $iAPIVersion = 3;

    protected $width = 300;
    protected $height = 300;
    protected $zoomWidth = 400;
    protected $zoomHeight = 400;
    protected $bUseZoomControl = true;
    protected $bUseMapTypeControl = true;
    protected $bShowSearchBar = false;
    protected $bShowResizeBar = false;
    protected $latitude = 0;
    protected $longitude = 0;
    protected $sFieldName = '';
    protected $bHookMenueLinks = false;

    /**
     * Used to switch street view on or off.
     *
     * @var bool
     */
    protected $bShowStreetViewControl = true;

    /**
     * Contains the google map styles added with function SetStyle().
     *
     * @var array
     */
    protected $aStyles = [];

    /**
     * Contains overview map. If false no overview map will be shown.
     *
     * @var TGoogleOverviewMap
     */
    protected $oOverViewMap = false;

    /**
     * Center map to show all markers. If false center map to maps latitude and longitude.
     *
     * @var bool
     */
    protected $bCenterMapFromMarkers = true;

    /**
     * Zoom map to max zoom which show all markers. If false map set zoom level from $iZoomLevel.
     *
     * @var bool
     */
    protected $bZoomMapFromMarkers = true;

    /**
     * Zoom-Level is a number between 0-17 from rough to fine.
     *
     * @var int|null
     */
    protected $iZoomLevel;

    /**
     * Minimal Zoom-Level is a number between 0-17 from rough to fine.
     *
     * @var string
     */
    protected $iMinZoomLevel;

    /**
     * Maximal Zoom-Level is a number between 0-17 from rough to fine.
     *
     * @var string
     */
    protected $iMaxZoomLevel;

    /**
     * if set true, renders the list of marker's target links in <noscript> container.
     *
     * @var bool
     */
    protected $bRenderNoJSMarkerTargetLink = true;

    /**
     * holds the options for the info overlay, like maxWidth.
     *
     * @var array
     */
    protected $aInfoWindowOptions = [];

    /**
     * init google map with unique map id.
     */
    public function __construct()
    {
        $this->sMapId = 'gmap_'.time();
    }

    /**
     * Disables the automatic center to show all markers within the map.
     * If disabled center map to maps latitude and longitude.
     */
    public function DisableCenterMapFromMarkers()
    {
        $this->bCenterMapFromMarkers = false;
    }

    /**
     * Disables the automatic max zoom to showa all markers within the map
     * If disabled map uses zoom level from $iZoomLevel.
     */
    public function DisableZoomMapFromMarkers()
    {
        $this->bZoomMapFromMarkers = false;
    }

    /**
     * Adds overview map to main map.
     *
     * @param TOverviewMap $oOverViewMap
     */
    public function AddOverviewMap($oOverViewMap)
    {
        $this->oOverViewMap = $oOverViewMap;
    }

    /**
     * enables/disables zoom control.
     *
     * @param bool $b
     */
    public function useZoomControl($b)
    {
        $this->bUseZoomControl = $b;
    }

    /**
     * enables/disables map type control.
     *
     * @param bool $b
     */
    public function useMapTypeControl($b)
    {
        $this->bUseMapTypeControl = $b;
    }

    /**
     * enables/disables resize bar.
     *
     * @param bool $b
     */
    public function showResizeBar($b)
    {
        $this->bShowResizeBar = $b;
    }

    /**
     * Disable/Enable StreetView.
     *
     * @param bool $b
     */
    public function showStreetViewControl($b)
    {
        $this->bShowStreetViewControl = $b;
    }

    /**
     * enables/disables event-to-link hooking in menues.
     *
     * @param bool $b
     */
    public function hookMenueLinks($b)
    {
        $this->bHookMenueLinks = $b;
    }

    /**
     * locate place coordinates by name/address.
     *
     * @param string $sPlace
     *
     * @return array{latitude: float, longitude: float}|false - contains latitude and longitude coordinate
     */
    public function locatePlace($sPlace = '')
    {
        $results = $this->getGeocoder()->geocode($sPlace);
        if (0 === count($results)) {
            return false;
        }

        return [
            'latitude' => $results[0]->getLatitude(),
            'longitude' => $results[0]->getLongitude(),
        ];
    }

    /**
     * set the map size in pixel.
     *
     * @param int $width
     * @param int $height
     */
    public function setMapSize($width = 300, $height = 300)
    {
        $this->width = $width;
        $this->height = $height;
    }

    /**
     * Set the Zoom-Level of the Google Map API.
     * Zoom-Level is the Number 0-17 from rough to fine.
     *
     * @param int $iZoomLevel The Zoom-Level of the Google Map API
     */
    public function SetZoomLevel($iZoomLevel)
    {
        $this->iZoomLevel = $iZoomLevel;
    }

    /**
     * sets the zoom range of the Google Map API.
     * possible values are from 0-17 from rough to fine.
     *
     * @param int $iMinZoomLevel
     * @param int $iMaxZoomLevel
     */
    public function setZoomRange($iMinZoomLevel = null, $iMaxZoomLevel = null)
    {
        $this->iMinZoomLevel = $iMinZoomLevel;
        $this->iMaxZoomLevel = $iMaxZoomLevel;
    }

    /**
     * enables/disables to render the list of marker's target links in <noscript> container.
     *
     * @param bool $b
     */
    public function UseRenderNoJSMarkerTargetLink($b = false)
    {
        $this->bRenderNoJSMarkerTargetLink = $b;
    }

    /**
     * set the map center.
     *
     * @param string $lat
     * @param string $lng
     */
    public function setMapCenter($lat, $lng)
    {
        $this->latitude = $lat;
        $this->longitude = $lng;
    }

    /**
     * set the maps zoom size.
     *
     * @param string $width
     * @param string $height
     */
    public function setMapZoomSize($width, $height)
    {
        $this->zoomWidth = $width;
        $this->zoomHeight = $height;
    }

    /**
     * Add google map style to map.
     *
     * @param string $sFeatureType
     * @param string $sElementType
     * @param array $aStyles List of styles for FeatureType and Element Type (array("visibility" => "off"))
     */
    public function SetStyle($sFeatureType, $sElementType, $aStyles)
    {
        $aNewStyle = [];
        $aNewStyle['featureType'] = $sFeatureType;
        $aNewStyle['elementType'] = $sElementType;
        $aNewStyle['stylers'] = $aStyles;
        $this->aStyles[] = $aNewStyle;
    }

    /**
     * Returns map styles as Json to add it to google map.
     *
     * @return string
     */
    protected function GetGoogleStyle()
    {
        $sGoogleStyle = '';
        if (count($this->aStyles) > 0) {
            $sGoogleStyle .= '[';
            foreach ($this->aStyles as $aStyle) {
                $sGoogleStyle .= '{';
                $sGoogleStyle .= 'featureType: "'.TGlobal::OutJS($aStyle['featureType']).'",';
                $sGoogleStyle .= 'elementType: "'.TGlobal::OutJS($aStyle['elementType']).'",';
                $sGoogleStyle .= 'stylers: '.$this->GetGoogleStyleDefinitions($aStyle['stylers']);
                $sGoogleStyle .= '},';
            }
            $sGoogleStyle = substr($sGoogleStyle, 0, -1);
            $sGoogleStyle .= ']';
        }

        return $sGoogleStyle;
    }

    /**
     * Returns style definitions from array as Json.
     *
     * @param array $aStyleDefinition
     *
     * @return string
     */
    protected function GetGoogleStyleDefinitions($aStyleDefinition)
    {
        $sStyleDefinition = '';
        if (is_array($aStyleDefinition) && count($aStyleDefinition) > 0) {
            $sStyleDefinition .= '[';
            foreach ($aStyleDefinition as $sStyle => $sValue) {
                if ('visibility' == $sStyle || 'hue' == $sStyle) {
                    $sStyleDefinition .= '{'.TGlobal::OutJS($sStyle).': "'.TGlobal::OutJS($sValue).'"},';
                } else {
                    $sStyleDefinition .= '{'.TGlobal::OutJS($sStyle).': '.TGlobal::OutJS($sValue).'},';
                }
            }
            $sStyleDefinition = substr($sStyleDefinition, 0, -1);
            $sStyleDefinition .= ']';
        }

        return $sStyleDefinition;
    }

    /**
     * @param string $sApiKey
     */
    public function setApiKey($sApiKey)
    {
        $this->sApiKey = $sApiKey;
    }

    /**
     * set the canvas (container div) id.
     *
     * @param string $sCanvasId
     */
    public function setCanvas($sCanvasId)
    {
        $this->sCanvasId = $sCanvasId;
    }

    /**
     * add a new marker to the map.
     *
     * @param TGoogleMapMarker $oMarker
     */
    public function addMarker($oMarker)
    {
        if ($oMarker instanceof TGoogleMapMarker) {
            $this->aMarkers[] = $oMarker;
        }
    }

    /**
     * add an icon to the map.
     *
     * @param TGoogleMapIcon $oIcon
     */
    public function addIcon($oIcon)
    {
        if ($oIcon instanceof TGoogleMapIcon) {
            if (!empty($oIcon->icon)) {
                $this->aIcons[] = $oIcon;
            }
        }
    }

    /**
     * sets the map display mode.
     *
     * @see http://code.google.com/intl/de-DE/apis/maps/documentation/reference.html#GMapType for v2
     * @see http://code.google.com/apis/maps/documentation/javascript/reference.html#MapTypeId for v3
     *
     * @param string $sMapMode
     */
    public function setMapType($sMapMode = 'HYBRID')
    {
        $this->sMapType = $sMapMode;
    }

    /**
     * field name which will be updates with latitude and longitude values
     * the field names are: $sFieldName."_lat/long".
     *
     * @param string $sFieldName
     */
    public function SetFieldName($sFieldName)
    {
        $this->sFieldName = $sFieldName;
    }

    /**
     * @param string $sIconId
     *
     * @return bool
     */
    protected function IconWithIdExits($sIconId)
    {
        $bExists = false;
        /** @var $oIcon TGoogleMapIcon */
        foreach ($this->aIcons as $oIcon) {
            if ($oIcon->getIconID() == $sIconId || $oIcon->getIconID() == 'icon_'.$sIconId) {
                $bExists = true;
            }
        }

        return $bExists;
    }

    /**
     * Renders the the google map code and HTML container.
     *
     * @param bool $bIncludeHTMLContainer
     *
     * @return string
     */
    protected function RenderV3($bIncludeHTMLContainer = false)
    {
        $aOut = [];
        $aOut = $this->getGoogleMapV3JsIncludes($aOut);
        if ($bIncludeHTMLContainer) {
            $aOut[] = $this->RenderMapContainer(true);
        }
        $aOut[] = '<script type="text/javascript">';
        $aOut = $this->AddGoogleMapV3JsInitializeFunction($aOut);
        $aOut[] = '</script>';

        $aOut = $this->AddGoogleMapV3JSFunctions($aOut);
        if ($this->bRenderNoJSMarkerTargetLink) {
            $aOut[] = $this->RenderNoJSMarkerTargetLinkList();
        }

        $sOut = implode("\n", $aOut);
        $sOut = '<!-- <CMS:exclude> -->'."\n".$sOut."\n";
        $sOut .= '<!-- </CMS:exclude> -->';

        return $sOut;
    }

    /**
     * Add initialize function as javascript to the map.
     *
     * @param array $aOut
     *
     * @return array
     */
    protected function AddGoogleMapV3JsInitializeFunction($aOut)
    {
        $sInfoWindowOptions = '';
        if (count($this->aInfoWindowOptions) > 0) {
            $sInfoWindowOptions = "{\n";
            $iCount = 0;
            foreach ($this->aInfoWindowOptions as $sOption => $sVal) {
                if (0 != $iCount) {
                    $sInfoWindowOptions .= ",\n";
                }
                $sInfoWindowOptions .= $sOption.": '".$sVal."'";
                ++$iCount;
            }
            $sInfoWindowOptions .= "}\n";
        }

        $aOut[] = 'infoWindow = new google.maps.InfoWindow('.$sInfoWindowOptions.');';
        $aOut[] = 'var '.$this->getMapID().' = null;';

        /** @var TGoogleMapIcon $oIcon */
        foreach ($this->aIcons as $oIcon) {
            $sMapSize = '';
            if (!empty($oIcon->height) && !empty($oIcon->width)) {
                $sMapSize = ', new google.maps.Size('.$oIcon->width.', '.$oIcon->height.')';
            }

            $aOut[] = 'var '.$oIcon->getIconID().' = new google.maps.MarkerImage("'.$oIcon->icon.'", null, null, null'.$sMapSize.');';
        }

        $aOut[] = '';
        $aOut[] = 'function GoogleMap_'.TGlobal::OutJS($this->getMapID()).'Initialize()';
        $aOut[] = '{';
        if (empty($this->latitude) || empty($this->longitude)) {
            $aOut[] = '  var latlng = new google.maps.LatLng(47.996662,7.846053);';
        } else {
            $aOut[] = '  var latlng = new google.maps.LatLng('.TGlobal::OutJS($this->latitude).','.TGlobal::OutJS($this->longitude).');';
        }
        $aOut = $this->AddGoogleMapV3OptionsToJs($aOut);
        $aOut[] = '';
        $aOut[] = '    '.$this->getMapID().' = new google.maps.Map(document.getElementById("'.TGlobal::OutJS($this->getMapID()).'"), myOptions);';
        $aOut = $this->AddGoogleMapV3StyleToJs($aOut);
        $aOut = $this->AddGoogleMapV3MarkerToJs($this->aMarkers, $aOut);
        $aOut = $this->AddGoogleMapsV3CenterMap($this->aMarkers, $aOut);

        if (!empty($this->oOverViewMap)) {
            $aOut[] = $this->oOverViewMap->Render();
        }

        $aOut[] = '}';

        return $aOut;
    }

    /**
     * Add needed javascript includes for google map v3.
     *
     * @param array $aOut
     *
     * @return array
     */
    public function getGoogleMapV3JsIncludes($aOut = [])
    {
        /**
         * prevents double loading of google maps include files.
         *
         * @var bool
         */
        static $bIncludesAlreadyLoaded = null;

        if (true === $bIncludesAlreadyLoaded) {
            return $aOut;
        }

        $sMapsURL = '//maps.googleapis.com/maps/api/js';
        if (!empty($this->sApiKey)) {
            $sMapsURL .= '?key='.TGlobal::OutHTML($this->sApiKey);
        }

        $aOut[] = '<script type="text/javascript" src="'.$sMapsURL.'"></script>';
        $aOut[] = '<script type="text/javascript" src="'.URL_USER_CMS_PUBLIC.'blackbox/components/google_map/js/google_maps_utilities_v3_markerWithLabel.js"></script>';
        if (!empty($this->oOverViewMap)) {
            $aOut[] = '<script type="text/javascript" src="'.URL_USER_CMS_PUBLIC.'blackbox/components/google_map/js/gmap_overviewmap.js"></script>';
        }

        $bIncludesAlreadyLoaded = true;

        return $aOut;
    }

    /**
     * Add javascript functions.
     * Overwrite this if you want to add custom functions.
     *
     * @param array $sOut
     *
     * @return array $sOut
     */
    protected function AddGoogleMapV3JSFunctions($sOut)
    {
        $sOut[] = '<script type="text/javascript">';
        $sOut[] = 'var markers = [];';
        $sOut[] = '$(document).ready( function () { ';
        $sOut[] = '    GoogleMap_'.TGlobal::OutJS($this->getMapID()).'Initialize();';
        $sOut[] = '});';

        if ($this->bShowResizeBar) {
            // Zoom map
            $sOut[] = '';
            $sOut[] = 'function switchMapSize() {';
            $sOut[] = '	if (!isZoomed) {';
            $sOut[] = '		document.getElementById("'.$this->getMapID().'_searchBar").style.width = '.TGlobal::OutJS($this->zoomWidth).'+"px";';
            $sOut[] = '		document.getElementById("'.$this->getMapID().'_zoomBar").style.width = '.TGlobal::OutJS($this->zoomWidth).'+"px";';
            $sOut[] = '		document.getElementById("'.$this->getMapID().'").style.width = '.TGlobal::OutJS($this->zoomWidth).'+"px";';
            $sOut[] = '		document.getElementById("'.$this->getMapID().'").style.height = '.TGlobal::OutJS($this->zoomHeight).'+"px";';
            $sOut[] = '		isZoomed = true;';
            $sOut[] = '	} else {';
            $sOut[] = '		document.getElementById("'.$this->getMapID().'_searchBar").style.width = '.TGlobal::OutJS($this->width).'+"px";';
            $sOut[] = '		document.getElementById("'.$this->getMapID().'_zoomBar").style.width = '.TGlobal::OutJS($this->width).'+"px";';
            $sOut[] = '		document.getElementById("'.$this->getMapID().'").style.width = '.TGlobal::OutJS($this->width).'+"px";';
            $sOut[] = '		document.getElementById("'.$this->getMapID().'").style.height = '.TGlobal::OutJS($this->height).'+"px";';
            $sOut[] = '		isZoomed = false;';
            $sOut[] = '	}';
            $sOut[] = '	'.TGlobal::OutJS($this->getMapID()).'.checkResize();';
            $sOut[] = '}';
        }

        $sOut[] = '
        function callDefaultMarkerEvent(html,marker) {
            infoWindow.setContent(html);
            infoWindow.open('.$this->getMapID().',marker);
        }
        ';
        $sOut[] = '</script>';

        return $sOut;
    }

    /**
     * Add configured styles as javascript to map.
     *
     * @param array $sOut
     *
     * @return array $sOut
     */
    protected function AddGoogleMapV3StyleToJs($sOut)
    {
        return $sOut;
    }

    /**
     * Add map options as javascript.
     *
     * @param array $sOut
     *
     * @return array $sOut
     */
    protected function AddGoogleMapV3OptionsToJs($sOut)
    {
        $sOut[] = '  var myOptions = {';
        if (empty($this->iZoomLevel)) {
            $iZoomLevel = 10;
        } else {
            $iZoomLevel = $this->iZoomLevel;
        }

        $sOut[] = '    zoom: '.TGlobal::OutJS($iZoomLevel).',';

        if (!is_null($this->iMinZoomLevel)) {
            $sOut[] = '    minZoom: '.TGlobal::OutJS($this->iMinZoomLevel).',';
        }

        if (!is_null($this->iMaxZoomLevel)) {
            $sOut[] = '    maxZoom: '.TGlobal::OutJS($this->iMaxZoomLevel).',';
        }

        $sOut[] = '    center: latlng,';
        $bShowStreetViewControl = $this->bShowStreetViewControl ? 'true' : 'false';
        $sOut[] = '    streetViewControl: '.$bShowStreetViewControl.',';
        $bShowResizeBar = $this->bShowResizeBar ? 'true' : 'false';
        $sOut[] = '    scaleControl: '.$bShowResizeBar.',';
        $bShowZoomControl = $this->bUseZoomControl ? 'true' : 'false';
        $sOut[] = '    zoomControl: '.$bShowZoomControl.',';

        if (count($this->aStyles) > 0) {
            $sOut[] = '    styles: '.$this->GetGoogleStyle().',';
        }

        $sOut[] = '    mapTypeId: google.maps.MapTypeId.'.TGlobal::OutJS($this->sMapType);
        $sOut[] = '};';

        return $sOut;
    }

    /**
     * Add configured markers as javascript.
     *
     * @param array $aMarkers
     * @param array $sOut
     *
     * @return array $sOut
     */
    protected function AddGoogleMapV3MarkerToJs($aMarkers, $sOut)
    {
        $count = 0;
        if (count($aMarkers) > 0) {
            if ($this->bCenterMapFromMarkers || $this->bZoomMapFromMarkers) {
                $sOut[] = 'var bounds = new google.maps.LatLngBounds();';
            }
            /** @var $oMarker TGoogleMapMarker */
            foreach ($aMarkers as $oMarker) {
                if (empty($oMarker->latitude) || empty($oMarker->longitude)) {
                    if (!empty($oMarker->place)) {
                        $aCoordinates = $this->locatePlace($oMarker->place);
                        $oMarker->latitude = $aCoordinates['latitude'];
                        $oMarker->longitude = $aCoordinates['longitude'];
                    }
                }

                if (!empty($oMarker->latitude) && !empty($oMarker->longitude)) {
                    $sIconObject = '';
                    if ('' !== $oMarker->iconIndex && array_key_exists($oMarker->iconIndex, $this->aIcons)) {
                        $sIconObject = $this->aIcons[$oMarker->iconIndex]->getIconID();
                    } else {
                        // fallback
                        // check if id is Icon ID instead of array index
                        reset($this->aIcons);
                        /** @var $oIconTmp TGoogleMapIcon */
                        foreach ($this->aIcons as $key => $oIconTmp) {
                            if ($oIconTmp->getIconID() == $oMarker->iconId || $oIconTmp->getIconID() == 'icon_'.$oMarker->iconId) {
                                $sIconObject = $this->aIcons[$key]->getIconID();
                                break;
                            }
                        }
                    }

                    if (!empty($sIconObject)) {
                        $sIconObject = ' icon: '.$sIconObject.', ';
                    }

                    $sMarkerId = $oMarker->getID();
                    $sOut[] = 'var marker_'.TGlobal::OutJS($sMarkerId).'LatLng = new google.maps.LatLng('.TGlobal::OutJS($oMarker->latitude).','.TGlobal::OutJS($oMarker->longitude).');';

                    $sPlaceStripped = stripslashes(trim($oMarker->place));
                    $sPlaceStripped = str_replace(chr(10), ' ', $sPlaceStripped);
                    $sPlaceStripped = str_replace(chr(13), ' ', $sPlaceStripped);

                    if (!empty($oMarker->sLabel)) {
                        $sOut[] = '
                        var marker_'.TGlobal::OutJS($sMarkerId).' = new MarkerWithLabel({
                           position: marker_'.TGlobal::OutJS($sMarkerId).'LatLng,
                           name: "marker_'.TGlobal::OutJS($sMarkerId).'",
                           map: '.$this->getMapID().',
                           draggable: false,
                           labelContent: '.json_encode($oMarker->sLabel).',
                           labelAnchor: new google.maps.Point(0, 0),
                           '.$sIconObject.'
                           labelClass: "'.$oMarker->sLabelClass.'", // the CSS class for the label
                           labelInBackground: false
                        });
                        ';

                        if (!empty($oMarker->sTargetLink)) {
                            $sOut[] = ' google.maps.event.addListener(marker_'.TGlobal::OutJS($sMarkerId).', "click", function (e) { document.location.href = "'.$oMarker->sTargetLink.'"; });';
                        }
                    } else {
                        $sOut[] = 'var marker_'.TGlobal::OutJS($sMarkerId).' = new google.maps.Marker({ position: marker_'.TGlobal::OutJS($sMarkerId).'LatLng, map: '.$this->getMapID().', '.$sIconObject.' title: "'.$sPlaceStripped.'"});';
                    }

                    if ($this->bCenterMapFromMarkers || $this->bZoomMapFromMarkers) {
                        $sOut[] = 'bounds.extend(marker_'.TGlobal::OutJS($sMarkerId).'LatLng);';
                    }
                    $sOut = $this->AddGoogleMapsV3MarkerEvent($sOut, $oMarker);
                    $sOut[] = 'markers["'.TGlobal::OutJS($oMarker->id).'"] = marker_'.TGlobal::OutJS($sMarkerId).';';
                    ++$count;
                }
            }
        }
        if ($count > 0 && ($this->bCenterMapFromMarkers || $this->bZoomMapFromMarkers)) {
            $sOut[] = $this->getMapID().'.fitBounds(bounds);';
        }

        return $sOut;
    }

    /**
     * Add center function as javascript.
     *
     * @param array $aMarkers
     * @param array $sOut
     *
     * @return array $sOut
     */
    protected function AddGoogleMapsV3CenterMap($aMarkers, $sOut)
    {
        if (count($aMarkers) > 0) {
            if ($this->bCenterMapFromMarkers && count($aMarkers) > 0) {
                $sOut[] = $this->getMapID().'.setCenter(bounds.getCenter());
                ';
            }
            if (!$this->bZoomMapFromMarkers) {
                $sOut[] = '
                var blistener = google.maps.event.addListener('.$this->getMapID().', \'bounds_changed\', function(event) {
                    if (this.getZoom() > '.$this->iZoomLevel.'){
                        this.setZoom('.$this->iZoomLevel.');
                    }
                    google.maps.event.removeListener(blistener);
                });
                ';
            }
        }

        return $sOut;
    }

    /**
     * Add javascript for marker events.
     *
     * @param array $sOut
     * @param TGoogleMapMarker $oMarker
     *
     * @return array $sOut
     */
    protected function AddGoogleMapsV3MarkerEvent($sOut, $oMarker)
    {
        $sMarkerId = $oMarker->getID();

        if (count($oMarker->Event) > 0) {
            foreach ($oMarker->Event as $sOnEvent => $sEventFunction) {
                $sOut[] = 'google.maps.event.addListener(marker_'.TGlobal::OutJS($sMarkerId).',"'.$sOnEvent.'",function(){'.$sEventFunction.'});';

                if ($this->bHookMenueLinks) {
                    // Generate link controls
                    $sOut[] = ' $("#mapItem'.$sMarkerId.'").bind(\''.$sOnEvent.'\', function() { '.$sEventFunction.' });';
                }
            }
        } else {
            // add default event (infoWindow on click)
            $sOut[] = 'google.maps.event.addListener(marker_'.TGlobal::OutJS($sMarkerId).',"click",function(){ callDefaultMarkerEvent("'.$oMarker->getDescription().'",marker_'.$sMarkerId.') });';

            if ($this->bHookMenueLinks) {
                // Generate link controls
                $sOut[] = ' $("#mapItem'.$sMarkerId.'").bind(\'click\', function() { callDefaultMarkerEvent("'.$oMarker->getDescription().'",marker_'.$sMarkerId.'); });';
            }
        }

        if ($this->bHookMenueLinks) {
            // Generate link controls
            $sOut[] = ' $("#mapItem'.$sMarkerId.'").addClass("gMapItemActiveLink");';
        }

        return $sOut;
    }

    /**
     * renders the map html/js code.
     *
     * @param bool $bUseV3 Set to true if you want to use google maps API version 3
     *
     * @return string
     */
    public function render($bUseV3 = false)
    {
        return $this->RenderV3(true);
    }

    /**
     * renders the maps HTML container.
     *
     * @param bool $bUseV3 Set to true if you want to use google maps API version 3
     *
     * @return string
     */
    public function RenderMapContainer($bUseV3 = false)
    {
        $aOut = [];
        if ($this->bShowResizeBar) {
            // Resize bar
            $aOut[] = '<div id="'.TGlobal::OutHTML($this->getMapID()).'_zoomBar" style="width: '.TGlobal::OutHTML($this->width).'px; height: 25px; background-color: #336699;">';
            $aOut[] = '	<div style="color: white; float: left;" onClick="switchMapSize();">resize</div>';
            $aOut[] = '</div>';
        }

        $sWidth = '';
        if (!empty($this->width)) {
            $sWidth = 'width: '.TGlobal::OutHTML($this->width).'px; ';
        }

        $sHeight = '';
        if (!empty($this->height)) {
            $sHeight = 'height: '.TGlobal::OutHTML($this->height).'px; ';
        }

        $aOut[] = '<div id="'.TGlobal::OutHTML($this->getMapID()).'" class="googleMap" style="'.$sWidth.''.$sHeight.'"></div>';

        $sMapContainer = implode("\n", $aOut);

        return $sMapContainer;
    }

    /**
     * renders the map Javascript code, you need to load this in the footer (GetHTMLFooterInclude method)
     * V2 API will be disabled on 30, may 2013!
     *
     * @param bool $bUseV3 Set to true if you want to use google maps API version 3
     *
     * @return string
     */
    public function RenderMapJS($bUseV3 = true)
    {
        $aOut = $this->getGoogleMapV3JsIncludes([]);
        $sIncludes = implode("\n", $aOut);

        return $sIncludes;
    }

    /**
     * renders the list of marker's target links in <noscript> container.
     *
     * @param array $aMarkers
     *
     * @return string
     */
    public function RenderNoJSMarkerTargetLinkList($aMarkers = [])
    {
        $sOut = '';
        if (empty($aMarkers)) {
            $aMarkers = $this->aMarkers;
        }
        if (is_array($aMarkers) && count($aMarkers) > 0) {
            $sOut = '<noscript>'."\n";
            foreach ($aMarkers as $oMarker) {
                if (!empty($oMarker->sTargetLink)) {
                    $sTargetLinkTitle = $oMarker->sTargetLinkTitle;
                    if (empty($sTargetLinkTitle)) {
                        $sTargetLinkTitle = $oMarker->title;
                    }
                    $sOut .= '<a href="'.$oMarker->sTargetLink.'">'.$sTargetLinkTitle."</a>\n";
                }
            }
            $sOut .= '</noscript>';
        }

        return $sOut;
    }

    /**
     * returns the unique map id.
     *
     * @return string
     */
    public function getMapID()
    {
        return $this->sMapId;
    }

    /**
     * @param string
     */
    public function setMapId($mapId)
    {
        $this->sMapId = $mapId;
    }

    /**
     * you may set your custom info overlay settings.
     *
     * @param array $aOptions
     */
    public function setInfoWindowOptions($aOptions)
    {
        if (is_array($aOptions)) {
            $this->aInfoWindowOptions = $aOptions;
        }
    }

    private function getGeocoder(): GeocoderInterface
    {
        return ServiceLocator::get('chameleon_system_core.geocoding.geocoder');
    }
}
