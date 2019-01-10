<?php

/*
 * This file is part of the Chameleon System (https://www.chameleonsystem.com).
 *
 * (c) ESONO AG (https://www.esono.de)
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

use ChameleonSystem\CoreBundle\ServiceLocator;

require_once PATH_LIBRARY.'/functions/ConvertDate.fun.php';

class TCMSFieldDateTime extends TCMSField
{
    /**
     * view path for frontend.
     */
    protected $sViewPath = 'TCMSFields/views/TCMSFieldDateTime';

    public function GetHTML()
    {
        $viewRenderer = $this->getViewRenderer();
        $viewRenderer->AddSourceObject('fieldName', $this->name);
        $viewRenderer->AddSourceObject('fieldValue', $this->_GetHTMLValue());
        $viewRenderer->AddSourceObject('language', TCMSUser::GetActiveUser()->GetCurrentEditLanguage());
        $viewRenderer->AddSourceObject('datetimepickerFormat', '');
        $viewRenderer->AddSourceObject('datetimepickerSideBySide', 'true');
        $viewRenderer->AddSourceObject('datetimepickerWithIcon', false);

        return $viewRenderer->Render('TCMSFieldDate/datetimeInput.html.twig', null, false);
    }

    protected function showUTCDateTime()
    {
        $sCurrentUTCTime = '';
        $isUTC = $this->getFieldTypeConfigKey('isUTC');
        if ('1' == $isUTC || 'true' == $isUTC) {
            $sServerDateTimeZoneSetting = date_default_timezone_get();
            date_default_timezone_set('UTC');
            $oLocal = TdbCmsLocals::GetActive();
            $sFormatedDateTime = $oLocal->FormatDate(date('Y-m-d H:i:s'));
            $sCurrentUTCTime = '&nbsp;&nbsp;&nbsp;<strong>'.TGlobal::OutHTML(TGlobal::Translate('chameleon_system_core.field_date_time.current_utc')).': '.$sFormatedDateTime.'</strong>';
            date_default_timezone_set($sServerDateTimeZoneSetting);
        }

        return $sCurrentUTCTime;
    }

    public function GetReadOnly()
    {
        $currentDate = $this->_GetHTMLValue();
        if ('0000-00-00 00:00:00' == $currentDate) {
            $html = TGlobal::Translate('chameleon_system_core.field_date_time.not_set');
        } else {
            $aDateParts = explode(' ', $currentDate);
            $date = $aDateParts[0];
            if ('0000-00-00' == $date) {
                $date = '';
            } else {
                $date = ConvertDate($date, 'sql2g');
            }

            $aTimeParts = explode(':', $aDateParts[1]);
            $hour = $aTimeParts[0];
            $minutes = $aTimeParts[1];

            $html = $this->_GetHiddenField();

            $sUTCDateTime = $this->showUTCDateTime();
            if (!empty($sUTCDateTime)) {
                $html .= '<strong>UTC</strong>&nbsp;&nbsp;';
            }
            $html .= TGlobal::OutHTML($date.' '.$hour.':'.$minutes.' '.TGlobal::Translate('chameleon_system_core.field_date_time.time'));
            if (!empty($sUTCDateTime)) {
                $html .= $sUTCDateTime;
            }
        }

        return $html;
    }

    /**
     * this method converts post data like datetime (3 fields with date, hours, minutes in human readable format)
     * to sql format.
     *
     * @return mixed
     */
    public function ConvertPostDataToSQL()
    {
        $returnVal = '';
        $bCompleteDatePassed = (false !== $this->oTableRow->sqlData && array_key_exists($this->name, $this->oTableRow->sqlData) && !empty($this->oTableRow->sqlData[$this->name]));
        if ($bCompleteDatePassed) {
            $returnVal = $this->oTableRow->sqlData[$this->name];
        }
        $this->data = $returnVal;

        return $returnVal;
    }

    /**
     * {@inheritdoc}
     */
    public function GetCMSHtmlHeadIncludes()
    {
        $includes = parent::GetCMSHtmlHeadIncludes();
        $includes[] = sprintf('<link href="%s" media="screen" rel="stylesheet" type="text/css" />', TGlobal::GetStaticURL('/chameleon/blackbox/javascript/tempus-dominus-5.1.2/css/tempusdominus-bootstrap-4.min.css')); //datetimepicker

        return $includes;
    }

    /**
     * {@inheritdoc}
     */
    public function GetCMSHtmlFooterIncludes()
    {
        $includes = parent::GetCMSHtmlFooterIncludes();
        $includes[] = sprintf('<script src="%s" type="text/javascript"></script>', TGlobal::GetStaticURL('/chameleon/blackbox/javascript/moment-2.23.0/js/moment-with-locales.min.js')); //moment.js for datetimepicker
        $includes[] = sprintf('<script src="%s" type="text/javascript"></script>', TGlobal::GetStaticURL('/chameleon/blackbox/javascript/tempus-dominus-5.1.2/js/tempusdominus-bootstrap-4.min.js')); //datetimepicker

        return $includes;
    }

    /**
     * checks if field is mandatory and if field content is valid
     * overwrite this method to add your field based validation
     * you need to add a message to TCMSMessageManager for handling error messages
     * <code>
     * <?php
     *   $oMessageManager = TCMSMessageManager::GetInstance();
     *   $sConsumerName = TCMSTableEditorManager::MESSAGE_MANAGER_CONSUMER;
     *   $oMessageManager->AddMessage($sConsumerName,'TABLEEDITOR_FIELD_IS_MANDATORY');
     * ?>
     * </code>.
     *
     * @return bool - returns false if field is mandatory and field content is empty or data is not valid
     */
    public function DataIsValid()
    {
        $bDataIsValid = parent::DataIsValid();
        if ($bDataIsValid) {
            if ($this->HasContent() && !$this->CheckValidDate($this->GetSQL())) {
                $bDataIsValid = false;
                $oMessageManager = TCMSMessageManager::GetInstance();
                $sConsumerName = TCMSTableEditorManager::MESSAGE_MANAGER_CONSUMER;
                $sFieldTitle = $this->oDefinition->GetName();
                $oMessageManager->AddMessage($sConsumerName, 'TABLEEDITOR_FIELD_DATE_NOT_VALID', array('sFieldName' => $this->name, 'sFieldTitle' => $sFieldTitle));
            }
        }

        return $bDataIsValid;
    }

    /**
     * checks if date is valid.
     *
     * @param string $sSqlDateTime
     *
     * @return bool
     */
    protected function CheckValidDate($sSqlDateTime)
    {
        $bValidDate = false;
        if ('0000-00-00 00:00:00' != $sSqlDateTime) {
            $pattern = '/^\d\d\d\d-(0[1-9]|1[012])-(0[1-9]|[12][0-9]|3[01])\s(0[0-9]:([0-5][0-9]):([0-5][0-9])|1[0-9]:([0-5][0-9]):([0-5][0-9])|2[0-3]:([0-5][0-9]):([0-5][0-9])|24:00:00)$/';
            if (preg_match($pattern, $sSqlDateTime)) {
                $sTimeStamp = strtotime($sSqlDateTime);
                $sCheckDateTime = date('Y-m-d H:i:s', $sTimeStamp);
                if ($sSqlDateTime == $sCheckDateTime) {
                    $bValidDate = true;
                }
            }
        } else {
            $bValidDate = true;
        }

        return $bValidDate;
    }

    /**
     * returns true if field data is not empty
     * overwrite this method for mlt and property fields.
     *
     * @return bool
     */
    public function HasContent()
    {
        $bHasContent = false;
        $sContent = $this->ConvertPostDataToSQL();
        if (!empty($sContent)) {
            $bHasContent = true;
        }

        return $bHasContent;
    }

    /**
     * called before save of form, the returned value is set
     * to form data of owning form for this field
     * important: this is called weather data is valid or not!
     *
     * @return mixed
     */
    public function PkgCmsFormTransformFormDataBeforeSave($oForm)
    {
        if (isset($this->oTableRow->sqlData) && is_array($this->oTableRow->sqlData)) {
            $oGlobal = TGlobal::instance();
            if ($oGlobal->UserDataExists($this->name.'_date')) {
                $this->oTableRow->sqlData[$this->name.'_date'] = $oGlobal->GetUserData($this->name.'_date');
            }
            if ($oGlobal->UserDataExists($this->name.'_hour')) {
                $this->oTableRow->sqlData[$this->name.'_hour'] = $oGlobal->GetUserData($this->name.'_hour');
            }
            if ($oGlobal->UserDataExists($this->name.'_min')) {
                $this->oTableRow->sqlData[$this->name.'_min'] = $oGlobal->GetUserData($this->name.'_min');
            }
        }

        return $this->data;
    }

    public function toString()
    {
        $aDateParts = explode(' ', $this->_GetHTMLValue());
        $date = $aDateParts[0];
        if ('0000-00-00' == $date) {
            $date = '';
        } else {
            $date = ConvertDate($date, 'sql2g');
        }
        if (count($aDateParts) > 1) {
            $date .= ' '.$aDateParts[1];
        }

        return $date;
    }

    private function getViewRenderer(): ViewRenderer
    {
        return ServiceLocator::get('chameleon_system_view_renderer.view_renderer');
    }
}
