<?php

/*
 * This file is part of the Chameleon System (https://www.chameleonsystem.com).
 *
 * (c) ESONO AG (https://www.esono.de)
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

use Symfony\Component\HttpFoundation\Request;

class CMSGMap extends TCMSModelBase
{
    /**
     * @var array
     */
    protected $aGMapIncludes = array();

    /**
     * {@inheritdoc}
     */
    public function Execute()
    {
        $this->data = parent::Execute();
        $this->data['googleMapHtml'] = $this->getGoogleMapHtml();

        return $this->data;
    }

    /**
     * @return string
     */
    private function getGoogleMapHtml()
    {
        $request = $this->getCurrentRequest();
        if (false === $request->query->has('sFieldName')) {
            return '';
        }

        $fieldName = $request->query->get('sFieldName');
        $this->data['fieldName'] = $fieldName;

        $googleMapId = 'gmap'.$fieldName;
        $this->data['googleMapId'] = $googleMapId;

        $googleMap = new TGoogleMap();
        $googleMap->setMapId($googleMapId);
        $googleMap->setMapSize(750, 500);
        $googleMap->SetFieldName($fieldName);

        if (true === $request->query->has('googleMapsApiKey') && '' !== $request->query->get('googleMapsApiKey')) {
            $googleMap->setApiKey($request->query->get('googleMapsApiKey'));
        }

        if (true === $request->query->has('lat') && true === $request->query->has('lng')) {
            $lat = $request->query->get('lat');
            $lng = $request->query->get('lng');
            if ('' !== $lat && '' !== $lng) {
                $googleMap->setMapCenter($lat, $lng);
            }
        }

        $this->aGMapIncludes = $googleMap->getGoogleMapV3JsIncludes();

        return $googleMap->render(true);
    }

    /**
     * {@inheritdoc}
     */
    public function GetHtmlHeadIncludes()
    {
        $includes = parent::GetHtmlHeadIncludes();
        $includes[] = '<script src="'.TGlobal::GetStaticURLToWebLib('/fields/TCMSFieldGMapCoordinate/TCMSFieldGMapCoordinate.js').'" type="text/javascript"></script>';

        if (count($this->aGMapIncludes) > 0) {
            $includes = array_merge($includes, $this->aGMapIncludes);
        }

        return $includes;
    }

    /**
     * @return Request|null
     */
    private function getCurrentRequest()
    {
        return \ChameleonSystem\CoreBundle\ServiceLocator::get('request_stack')->getCurrentRequest();
    }
}
