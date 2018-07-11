<?php

/**
 * @deprecated since 6.2.0 - no longer used (uses an old Maps API which is no longer supported).
 */
if (!empty($data['oTableRow']->sqlData['kml_url'])) {
    ?>
<script type="text/javascript">
    function showGoogleMap<?=$data['instanceID']; ?>(mapContainerID) {
        var geoXml = new GGeoXml("<?=$data['oTableRow']->sqlData['kml_url']; ?>");
        var map = new GMap2(document.getElementById(mapContainerID));
        map.addControl(new GSmallMapControl());
        map.addControl(new GMapTypeControl());
        map.setCenter(new GLatLng(<?=$data['latitude']; ?>,<?=$data['longitude']; ?>), <?=$data['zoomFactor']; ?>);
        map.setMapType(<?=$data['mapType']; ?>);
        map.addOverlay(geoXml);
    }
</script>
<div id="google_map<?=$data['instanceID']; ?>"
     style="width: <?=$data['oTableRow']->sqlData['width']; ?>px; height: <?=$data['oTableRow']->sqlData['height']; ?>px;"></div>
<?php
}
?>