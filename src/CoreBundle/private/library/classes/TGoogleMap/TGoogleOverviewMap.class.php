<?php

/*
 * This file is part of the Chameleon System (https://www.chameleonsystem.com).
 *
 * (c) ESONO AG (https://www.esono.de)
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

/**
 * You can create an overview map and add it to a google v3 map.
 * /**/
class TGoogleOverviewMap
{
    /**
     * Height of the overview map.
     *
     * @var int
     */
    public $sHeight = 150;

    /**
     * Width of the overview map.
     *
     * @var int
     */
    public $sWidth = 150;

    /**
     * Difference between the zoom level from parent map and the overview map
     * Set to negative value if you want to zoom in  in overview map.
     * Set positive value if you want to zoom out in overview map;.
     *
     * @var int
     */
    public $sZoomDifference = 3;

    /**
     * Map style of the overview map.
     *
     * @var string
     */
    public $sMapType = 'ROADMAP';

    /**
     * Set to true if you want to show the overview map in parent map or
     * set to false if you want to show the overview map right to the parent map.
     *
     * @var bool
     */
    public $bShowInParent = true;

    /**
     * Color of the rectangle wihc shows the parent map size.
     *
     * @var string
     */
    public $sRectangleColor = 'FF0000';

    /**
     * you need to pass the corresponding map object.
     *
     * @var TGoogleMap|null
     */
    public $oGoogleMap;

    /**
     * Renders the javascript for overview map.
     *
     * @return string
     */
    public function Render()
    {
        $sShowInParent = 'false';
        if ($this->bShowInParent) {
            $sShowInParent = 'true';
        }

        $sMapID = 'map';
        if (!is_null($this->oGoogleMap)) {
            $sMapID = $this->oGoogleMap->getMapID();
        }

        $sOverViewMap = $sMapID.".Overview({
                        'zoom_difference': ".TGlobal::OutJS($this->sZoomDifference).",
                        'box_width': ".TGlobal::OutJS($this->sWidth).",
                        'box_height': ".TGlobal::OutJS($this->sHeight).",
                        'rectangle_color': '".TGlobal::OutJS($this->sRectangleColor)."',
                        'maptype': google.maps.MapTypeId.".TGlobal::OutJS($this->sMapType).",
                        'showinparent_map': ".$sShowInParent.'
                      });';

        return $sOverViewMap;
    }
}
