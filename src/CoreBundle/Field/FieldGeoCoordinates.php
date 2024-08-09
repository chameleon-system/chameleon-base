<?php

/*
 * This file is part of the Chameleon System (https://www.chameleonsystem.com).
 *
 * (c) ESONO AG (https://www.esono.de)
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace ChameleonSystem\CoreBundle\Field;

use ChameleonSystem\CoreBundle\ServiceLocator;
use ChameleonSystem\CoreBundle\Util\UrlUtil;
use Symfony\Contracts\Translation\TranslatorInterface;
use TCMSField;
use TCMSMessageManager;
use TCMSTableEditorManager;
use TGlobal;
use ViewRenderer;

class FieldGeoCoordinates extends TCMSField
{
    /**
     * {@inheritdoc}
     */
    public function GetSQL()
    {
        $returnVal = false;
        $bLatitudePassed = trim(false !== $this->oTableRow->sqlData && array_key_exists($this->name . '_lat', $this->oTableRow->sqlData) && !empty($this->oTableRow->sqlData[$this->name . '_lat']));
        $bLongitudePassed = trim(false !== $this->oTableRow->sqlData && array_key_exists($this->name . '_lng', $this->oTableRow->sqlData) && !empty($this->oTableRow->sqlData[$this->name . '_lng']));
        if (!empty($bLatitudePassed) && !empty($bLongitudePassed)) {
            $returnVal = $this->oTableRow->sqlData[$this->name . '_lat'] . '|' . $this->oTableRow->sqlData[$this->name . '_lng'];
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
        $viewRenderer->AddSourceObject('isMandatoryField', $this->IsMandatoryField());

        return $viewRenderer->Render('Fields/FieldGeoCoordinates/inputFieldsWithStaticMap.html.twig', null, false);
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
                    $oMessageManager->AddMessage($sConsumerName, 'TABLEEDITOR_FIELD_GOOGLECOORDINATES_NOT_VALID', array('sFieldName' => $this->name, 'sFieldTitle' => $sFieldTitle));
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
        return '';
    }

    /**
     * {@inheritdoc}
     */
    public function GetCMSHtmlFooterIncludes()
    {
        $includes = parent::GetCMSHtmlFooterIncludes();
        $includes[] = '<script src="' . TGlobal::GetStaticURLToWebLib('/fields/FieldGeoCoordinates/FieldGeoCoordinates.js') . '" type="text/javascript"></script>';
        $includes[] = '<script type="text/javascript">
$(document).ready(function() {
    CHAMELEON.CORE.FieldGeoCoordinates.init("' . TGlobal::OutJS($this->name) . '","' . $this->getMapsBackendModuleUrl() . '");
    CHAMELEON.CORE.FieldGeoCoordinates.wrongLatitude = "'.$this->getTranslator()->trans('chameleon_system_core.field_map_coordinates.wrong_latitude').'";
    CHAMELEON.CORE.FieldGeoCoordinates.wrongLongitude = "'.$this->getTranslator()->trans('chameleon_system_core.field_map_coordinates.wrong_longitude').'";
});
</script>';

        return $includes;
    }

    /**
     * @return string
     */
    protected function getMapsBackendModuleUrl()
    {

        $urlUtil = $this->getUrlUtil();

        return $urlUtil->getArrayAsUrl([
            'pagedef' => 'geoMap',
            '_pagedefType' => 'Core'
            ], PATH_CMS_CONTROLLER . '?');
    }


    /**
     * @return ViewRenderer
     */
    private function getViewRenderer()
    {
        return \ChameleonSystem\CoreBundle\ServiceLocator::get('chameleon_system_view_renderer.view_renderer');
    }

    /**
     * @return UrlUtil
     */
    private function getUrlUtil()
    {
        return \ChameleonSystem\CoreBundle\ServiceLocator::get('chameleon_system_core.util.url');
    }

    private function getTranslator(): TranslatorInterface
    {
        return ServiceLocator::get('chameleon_system_core.translator');
    }
}
