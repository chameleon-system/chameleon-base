<?php

/*
 * This file is part of the Chameleon System (https://www.chameleonsystem.com).
 *
 * (c) ESONO AG (https://www.esono.de)
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace ChameleonSystem\CoreBundle\Bridge\Chameleon\Module\MapCoordinates;

use ChameleonSystem\CoreBundle\ServiceLocator;
use ChameleonSystem\CoreBundle\Util\InputFilterUtilInterface;

class MapCoordinates extends \MTPkgViewRendererAbstractModuleMapper
{
    public function __construct(
        private readonly InputFilterUtilInterface $inputFilterUtil
    ) {
    }

    /**
     * {@inheritdoc}
     */
    public function Accept(\IMapperVisitorRestricted $visitor, $cachingEnabled, \IMapperCacheTriggerRestricted $cacheTriggerManager)
    {
        $visitor->SetMappedValue('fieldname', $this->inputFilterUtil->getFilteredGetInput('sFieldName', ''));
        $visitor->SetMappedValue('lat', $this->inputFilterUtil->getFilteredGetInput('lat', ''));
        $visitor->SetMappedValue('lng', $this->inputFilterUtil->getFilteredGetInput('lng', ''));

        if (ServiceLocator::getParameter('chameleon_system_core.geocoding.attribution.show')) {
            $visitor->SetMappedValue('showGeocodingAttribution', true);
            $visitor->SetMappedValue('geocodingAttributionName', ServiceLocator::getParameter('chameleon_system_core.geocoding.attribution.name'));
            $visitor->SetMappedValue('geocodingAttributionUrl', ServiceLocator::getParameter('chameleon_system_core.geocoding.attribution.url'));
        } else {
            $visitor->SetMappedValue('showGeocodingAttribution', false);
        }
    }

    /**
     * {@inheritdoc}
     */
    public function GetHtmlHeadIncludes()
    {
        $includes = parent::GetHtmlHeadIncludes();
        $includes[] = '<link rel="stylesheet" href="'.$this->global->GetStaticUrlToWebLib('/fields/FieldGeoCoordinates/Leaflet-dist/leaflet.css').'" />';
        $includes[] = '<script type="text/javascript" src="'.$this->global->GetStaticUrlToWebLib('/fields/FieldGeoCoordinates/Leaflet-dist/leaflet.js', false).'"></script>';
        $includes[] = '<link rel="stylesheet" href="'.$this->global->GetStaticUrlToWebLib('/fields/FieldGeoCoordinates/Geocoder-dist/Control.Geocoder.css').'" />';
        $includes[] = '<script type="text/javascript" src="'.$this->global->GetStaticUrlToWebLib('/fields/FieldGeoCoordinates/Geocoder-dist/Control.Geocoder.js', false).'"></script>';
        $includes[] = '<link rel="stylesheet" href="'.$this->global->GetStaticUrlToWebLib('/fields/FieldGeoCoordinates/FieldGeoCoordinates.css?v2').'" />';
        $includes[] = '<script type="text/javascript" src="'.$this->global->GetStaticURLToWebLib('/fields/FieldGeoCoordinates/FieldGeoCoordinates.js').'"></script>';

        return $includes;
    }
}
