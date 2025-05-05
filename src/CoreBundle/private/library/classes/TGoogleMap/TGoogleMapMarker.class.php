<?php

/*
 * This file is part of the Chameleon System (https://www.chameleonsystem.com).
 *
 * (c) ESONO AG (https://www.esono.de)
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

class TGoogleMapMarker
{
    public $latitude = 0;
    public $longitude = 0;
    public $title = '';

    /**
     * @var string
     */
    public $description = '';

    /**
     * value of TGoogleMapIcon->id.
     *
     * @var string
     */
    public $iconId = '';

    /**
     * array index of TGoogleMap->aIcons.
     *
     * @var string
     */
    public $iconIndex = '';

    /**
     * alternative to lat/long you may set an address to locate the marker
     * format: street, citycode, city, country.
     *
     * @var string
     */
    public $place = '';

    public $md5sum = '';

    /**
     * Adds an Event to google marker.
     * array('click'=> 'do this javasript').
     *
     * @var array
     */
    public $Event = [];

    /**
     * Adds a label on bottom of the marker with the value of the variable.
     *
     * @var string
     */
    public $sLabel = '';

    /**
     * Adds a class to the markers label if $sLabel contains text.
     *
     * @var string
     */
    public $sLabelClass = 'gmaplable';

    /**
     * Adds id to the marker. All markers will be added to a marker array where the id is the key.
     * So you can add custom events to an marker.
     *
     * @var string
     */
    public $id = '';

    /**
     * Adds target link to the marker.
     * So you can add custom target page link to an marker.
     *
     * @var string
     */
    public $sTargetLink = '';

    /**
     * Adds title of target link to the marker.
     *
     * @var string
     */
    public $sTargetLinkTitle = '';

    /**
     * init google map marker with given id or unique id
     * Add custom id if you want to add custom events to the marker.
     *
     * @param string|bool $sId
     */
    public function __construct($sId = false)
    {
        $this->SetID($sId);
    }

    /**
     * sets the id of the marker
     * if no id is defined it will generate a unique id.
     *
     * @param string|bool $sId
     */
    public function SetID($sId = false)
    {
        if (!$sId) {
            $sId = str_replace('-', '', TTools::GetUUID());
        }
        $this->id = $sId;
    }

    /**
     * returns the id of the marker.
     *
     * @return string
     */
    public function getID()
    {
        $sID = str_replace('-', '', $this->id);

        return $sID;
    }

    /**
     * @return string
     */
    public function getDescription()
    {
        $sDescription = str_replace("\r\n", '', $this->description);
        $sDescription = str_replace("\n\r", '', $sDescription);
        $sDescription = str_replace("\n", '', $sDescription);
        $sDescription = str_replace("\r", '', $sDescription);
        $sDescription = str_replace("\t", '', $sDescription);
        $sDescription = str_replace("\'", "'", $sDescription);
        $sDescription = str_replace('\"', '"', $sDescription);
        $sDescription = json_encode($sDescription);
        $sDescription = str_replace('""', '', $sDescription);
        if ('"' == substr($sDescription, 0, 1)) {
            $sDescription = substr($sDescription, 1);
        }
        if ('"' == substr($sDescription, -1)) {
            $sDescription = substr($sDescription, 0, -1);
        }

        return $sDescription;
    }
}
