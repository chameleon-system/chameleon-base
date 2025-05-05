<?php

/*
 * This file is part of the Chameleon System (https://www.chameleonsystem.com).
 *
 * (c) ESONO AG (https://www.esono.de)
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 *
 * @deprecated since 7.1.34
 * use TCMSFieldGeoCoordinates instead to use OpenStreetMap instead of Google Maps
 */

use ChameleonSystem\AutoclassesBundle\TableConfExport\DataModelParts;
use ChameleonSystem\AutoclassesBundle\TableConfExport\DoctrineTransformableInterface;
use ChameleonSystem\CoreBundle\Service\GoogleApiKeyProviderInterface;
use ChameleonSystem\CoreBundle\Util\UrlUtil;

class TCMSFieldGMapCoordinate extends TCMSField implements DoctrineTransformableInterface
{
    public function getDoctrineDataModelParts(string $namespace, array $tableNamespaceMapping): DataModelParts
    {
        $parameters = [
            'source' => get_class($this),
            'type' => 'string',
            'docCommentType' => 'string',
            'description' => $this->oDefinition->sqlData['translation'],
            'propertyName' => $this->snakeToCamelCase($this->name),
            'defaultValue' => sprintf("'%s'", addslashes($this->oDefinition->sqlData['field_default_value'])),
            'allowDefaultValue' => true,
            'getterName' => 'get'.$this->snakeToPascalCase($this->name),
            'setterName' => 'set'.$this->snakeToPascalCase($this->name),
        ];
        $propertyCode = $this->getDoctrineRenderer('model/default.property.php.twig', $parameters)->render();
        $methodCode = $this->getDoctrineRenderer('model/default.methods.php.twig', $parameters)->render();

        return new DataModelParts(
            $propertyCode,
            $methodCode,
            $this->getDoctrineDataModelXml($namespace),
            [],
            true
        );
    }

    protected function getDoctrineDataModelXml(string $namespace): string
    {
        return $this->getDoctrineRenderer('mapping/string.xml.twig', [
            'fieldName' => $this->snakeToCamelCase($this->name),
            'type' => 'string',
            'column' => $this->name,
            'comment' => $this->oDefinition->sqlData['translation'],
            'default' => $this->oDefinition->sqlData['field_default_value'],
            'length' => '' === $this->oDefinition->sqlData['length_set'] ? 255 : $this->oDefinition->sqlData['length_set'],
        ])->render();
    }

    /**
     * {@inheritdoc}
     */
    public function GetSQL()
    {
        $returnVal = false;
        $bLatitudePassed = trim(false !== $this->oTableRow->sqlData && array_key_exists($this->name.'_lat', $this->oTableRow->sqlData) && !empty($this->oTableRow->sqlData[$this->name.'_lat']));
        $bLongitudePassed = trim(false !== $this->oTableRow->sqlData && array_key_exists($this->name.'_lng', $this->oTableRow->sqlData) && !empty($this->oTableRow->sqlData[$this->name.'_lng']));
        if (!empty($bLatitudePassed) && !empty($bLongitudePassed)) {
            $returnVal = $this->oTableRow->sqlData[$this->name.'_lat'].'|'.$this->oTableRow->sqlData[$this->name.'_lng'];
        } else {
            $bCompleteDatePassed = (false !== $this->oTableRow->sqlData && array_key_exists($this->name, $this->oTableRow->sqlData) && !empty($this->oTableRow->sqlData[$this->name]));
            if ($bCompleteDatePassed) {
                $returnVal = $this->oTableRow->sqlData[$this->name];
            }
        }

        if ('|' === $returnVal) {
            $returnVal = '';
        }

        return $returnVal;
    }

    /**
     * {@inheritdoc}
     */
    public function GetHTML()
    {
        parent::GetHTML();

        $this->fieldCSSwidth = 200;

        $aCoordinates = explode('|', $this->_GetFieldValue());
        $lat = '';
        $lng = '';

        if (2 === count($aCoordinates)) {
            $lat = $aCoordinates[0];
            $lng = $aCoordinates[1];
        }

        $viewRenderer = $this->getViewRenderer();
        $viewRenderer->AddSourceObject('fieldName', $this->name);
        $viewRenderer->AddSourceObject('fieldValue', $this->_GetFieldValue());
        $viewRenderer->AddSourceObject('latitude', $lat);
        $viewRenderer->AddSourceObject('longitude', $lng);
        $viewRenderer->AddSourceObject('googleMapsApiKey', $this->getGoogleMapsApiKey());
        $viewRenderer->AddSourceObject('isMandatoryField', $this->IsMandatoryField());

        return $viewRenderer->Render('TCMSFieldGMapCoordinate/inputFieldsWithStaticMap.html.twig', null, false);
    }

    /**
     * {@inheritdoc}
     */
    public function _GetHTMLValue()
    {
        $html = parent::_GetHTMLValue();
        $html = TGlobal::OutHTML($html);

        return $html;
    }

    /**
     * {@inheritdoc}
     */
    public function DataIsValid()
    {
        $bDataIsValid = parent::DataIsValid();
        if ($bDataIsValid) {
            $sSQLData = $this->ConvertPostDataToSQL();

            if (!$this->IsMandatoryField() && ('|' == $sSQLData || '' == $sSQLData)) {
                $bDataIsValid = true;
            } else {
                $pattern = '/^-?([0-9](\.\d+)?|[1-7][0-9](\.\d+)?|8[0-4](\.\d+)?|85(\.00*)?)\|-?([0-9](\.\d+)?|[1-9][0-9](\.\d+)?|1[0-7][0-9](\.\d+)?|180(\.00*)?)$/';
                if ($this->HasContent() && !preg_match($pattern, $sSQLData)) {
                    $bDataIsValid = false;
                    $oMessageManager = TCMSMessageManager::GetInstance();
                    $sConsumerName = TCMSTableEditorManager::MESSAGE_MANAGER_CONSUMER;
                    $sFieldTitle = $this->oDefinition->GetName();
                    $oMessageManager->AddMessage($sConsumerName, 'TABLEEDITOR_FIELD_GOOGLECOORDINATES_NOT_VALID', ['sFieldName' => $this->name, 'sFieldTitle' => $sFieldTitle]);
                }
            }
        }

        return $bDataIsValid;
    }

    /**
     * {@inheritdoc}
     */
    public function HasContent()
    {
        $bHasContent = false;
        $sContent = $this->ConvertPostDataToSQL();
        if (!empty($sContent) || '|' == $sContent) {
            $bHasContent = true;
        }

        return $bHasContent;
    }

    /**
     * {@inheritdoc}
     */
    public function RenderFieldMethodsString()
    {
        $aMethodData = $this->GetFieldMethodBaseDataArray();
        $aMethodData['sMethodName'] = $this->GetFieldMethodName('GoogleMap');
        $aMethodData['sReturnType'] = 'TGoogleMap';
        $aMethodData['sClassName'] = 'TGoogleMap';
        $aMethodData['sClassSubType'] = 'TGoogleMap';
        $aMethodData['sClassType'] = 'Core';
        $aMethodData['aParameters']['sWidth'] = self::GetMethodParameterArray('string', "'300'", 'width of the google map');
        $aMethodData['aParameters']['sHeight'] = self::GetMethodParameterArray('string', "'300'", 'height of the google map');
        $aMethodData['aParameters']['sMapType'] = self::GetMethodParameterArray('string', "'ROADMAP'", 'Map type of the google map');
        $aMethodData['aParameters']['iZoom'] = self::GetMethodParameterArray('string', '6', 'Zoom level of the map');
        $aMethodData['aParameters']['bShowResizeBar'] = self::GetMethodParameterArray('boolean', 'false', 'show resize bar on map');
        $aMethodData['aParameters']['bShowStreetViewControl'] = self::GetMethodParameterArray('boolean', 'true', 'show street view control on map');
        $aMethodData['aParameters']['bHookMenuLinks'] = self::GetMethodParameterArray('boolean', 'false', 'hook menu links on map');
        $aMethodData['aParameters']['apiKey'] = self::GetMethodParameterArray('string', "'".$this->getGoogleMapsApiKey()."'", '');
        $oViewParser = new TViewParser();
        /* @var $oViewParser TViewParser */
        $oViewParser->bShowTemplatePathAsHTMLHint = false;
        $oViewParser->AddVarArray($aMethodData);

        $sMethodCode = $oViewParser->RenderObjectView('getobject', 'TCMSFields/TCMSFieldGMapCoordinate');
        $oViewParser->AddVar('sMethodCode', $sMethodCode);
        $sCode = $oViewParser->RenderObjectView('method', 'TCMSFields/TCMSField');

        return $sCode;
    }

    /**
     * @return string|null
     */
    protected function getGoogleMapsApiKey()
    {
        return $this->getGoogleApiKeyProvider()->getMapsApiKey();
    }

    /**
     * {@inheritdoc}
     */
    public function GetCMSHtmlFooterIncludes()
    {
        $includes = parent::GetCMSHtmlFooterIncludes();
        $includes[] = '<script src="'.TGlobal::GetStaticURLToWebLib('/fields/TCMSFieldGMapCoordinate/TCMSFieldGMapCoordinate.js').'" type="text/javascript"></script>';
        $includes[] = '<script type="text/javascript">
$(document).ready(function() {
    CHAMELEON.CORE.TCMSFieldGMapCoordinate.init("'.TGlobal::OutJS($this->name).'","'.$this->getGoogleMapsBackendModuleUrl().'");
});
</script>';

        return $includes;
    }

    /**
     * @return string
     */
    protected function getGoogleMapsBackendModuleUrl()
    {
        $googleMapsApiKey = $this->getGoogleMapsApiKey();
        if (null === $googleMapsApiKey) {
            $googleMapsApiKey = '';
        }

        $urlUtil = $this->getUrlUtil();

        return $urlUtil->getArrayAsUrl([
            'pagedef' => 'gmap',
            '_pagedefType' => 'Core',
            'googleMapsApiKey' => $googleMapsApiKey,
        ], PATH_CMS_CONTROLLER.'?');
    }

    /**
     * @return GoogleApiKeyProviderInterface
     */
    protected function getGoogleApiKeyProvider()
    {
        return ChameleonSystem\CoreBundle\ServiceLocator::get('chameleon_system_core.service.google_api_key');
    }

    /**
     * @return ViewRenderer
     */
    private function getViewRenderer()
    {
        return ChameleonSystem\CoreBundle\ServiceLocator::get('chameleon_system_view_renderer.view_renderer');
    }

    /**
     * @return UrlUtil
     */
    private function getUrlUtil()
    {
        return ChameleonSystem\CoreBundle\ServiceLocator::get('chameleon_system_core.util.url');
    }
}
