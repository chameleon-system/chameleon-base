<?php

/*
 * This file is part of the Chameleon System (https://www.chameleonsystem.com).
 *
 * (c) ESONO AG (https://www.esono.de)
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

use ChameleonSystem\CoreBundle\Interfaces\FlashMessageServiceInterface;

/**
 * {@inheritdoc}
 */
class TCMSFieldDate extends TCMSField
{
    /**
     * view path for frontend.
     */
    protected $sViewPath = 'TCMSFields/views/TCMSFieldDate';

    /**
     * {@inheritdoc}
     */
    public function GetHTML()
    {
        $viewRenderer = $this->getViewRenderer();
        $viewRenderer->AddSourceObject('fieldName', $this->name);
        $viewRenderer->AddSourceObject('fieldValue', $this->_GetHTMLValue());
        $viewRenderer->AddSourceObject('language', TCMSUser::GetActiveUser()->GetCurrentEditLanguage());
        $viewRenderer->AddSourceObject('datetimepickerFormat', 'L');
        $viewRenderer->AddSourceObject('datetimepickerSideBySide', 'false');

        return $viewRenderer->Render('TCMSFieldDate/datetimeInput.html.twig', null, false);
    }

    /**
     * this method converts post data like datetime (3 fields with date, hours, minutes in human readable format)
     * to sql format.
     *
     * @return string
     */
    public function ConvertPostDataToSQL()
    {
        if ('' === $this->data) {
            return '';
        }

        if (false === $this->IsSQLDate($this->data)) {
            $dateTime = new \DateTime($this->data);

            return $dateTime->format('Y-m-d');
        }

        return $this->data;
    }

    /**
     * {@inheritdoc}
     */
    public function GetReadOnly()
    {
        $dateTime = new \DateTime($this->data);

        return TGlobal::OutHTML($dateTime->format('d.m.Y'));
    }

    /**
     * {@inheritdoc}
     */
    protected function _GetHiddenField()
    {
        return '<input type="hidden" name="'.TGlobal::OutHTML($this->name).'" id='.TGlobal::OutHTML($this->name).'" value="'.TGlobal::OutHTML(ConvertDate($this->data, 'sql2g')).'" />';
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
     * @return string
     */
    public function GetRTFExport()
    {
        $date = $this->_GetFieldValue();

        setlocale(LC_TIME, 'de_DE@euro', 'de_DE', 'de', 'ge', 'de_DE.ISO8859-1');

        $dateArray = explode('-', $date);
        $timeStamp = mktime(0, 0, 0, $dateArray[1], $dateArray[2], $dateArray[0]);
        $dayString = strftime('%a', $timeStamp);

        setlocale(LC_TIME, 0);

        $dateTime = new \DateTime($date);
        $returnString = $dayString.' '.$dateTime->format('d.m.Y');

        return $returnString;
    }

    /**
     * {@inheritdoc}
     */
    public function DataIsValid()
    {
        $dataIsValid = parent::DataIsValid();
        if (false === $dataIsValid) {
            return false;
        }

        if ($this->HasContent() && false === $this->CheckValidDate($this->data)) {
            $dataIsValid = false;
            $flashMessageService = $this->getFlashMessageService();
            $sConsumerName = TCMSTableEditorManager::MESSAGE_MANAGER_CONSUMER;
            $sFieldTitle = $this->oDefinition->GetName();
            $sFieldTitle = TGlobal::Translate($sFieldTitle);
            $flashMessageService->addMessage(
                $sConsumerName,
                'TABLEEDITOR_FIELD_DATE_NOT_VALID',
                array('sFieldName' => $this->name, 'sFieldTitle' => $sFieldTitle)
            );
        }

        return $dataIsValid;
    }

    /**
     * checks if the date is valid.
     *
     * @param string|bool $sSqlDateTime
     *
     * @return bool
     */
    protected function CheckValidDate($sSqlDateTime)
    {
        if (false === $sSqlDateTime) {
            return true;
        }

        $patternGermanDate = '/^(0[1-9]|[12][0-9]|3[01]|[1-9])[\.\/](0[1-9]|1[012]|[1-9])[\.\/](\d{4}|\d{2})$/';
        $patternSqlDate = '/^[0-9]{4}-(0[1-9]|1[0-2])-(0[1-9]|[1-2][0-9]|3[0-1])$/';
        if (preg_match($patternGermanDate, $sSqlDateTime) || preg_match($patternSqlDate, $sSqlDateTime)) {
            $timeStamp = strtotime($sSqlDateTime);
            $checkDateTime = date('d.m.Y', $timeStamp);
            $checkDateTimeSql = date('Y-m-d', $timeStamp);
            if ($sSqlDateTime === $checkDateTime || $sSqlDateTime === $checkDateTimeSql) {
                return true;
            }
        }

        return false;
    }

    /**
     * @param string $sDate
     *
     * @return bool
     */
    protected function IsSQLDate($sDate)
    {
        $timeStamp = strtotime($sDate);
        $checkDateTimeSql = date('Y-m-d', $timeStamp);
        if ($sDate === $checkDateTimeSql) {
            return true;
        }

        // this could be a date before 1970...
        if (preg_match('@^[0-9]{4}-[0-9]{2}-[0-9]{2}$@', $sDate)) {
            $partsList = explode('-', $sDate);
            if (is_numeric($partsList[0]) && $partsList[0] <= 1970) {
                return true;
            }
        }

        return false;
    }

    /**
     * {@inheritdoc}
     */
    public function HasContent()
    {
        $sFieldDisplayType = $this->GetDisplayType();
        if ('readonly-if-filled' === $sFieldDisplayType) {
            $this->LoadCurrentDataFromDatabase();
            if (!empty($this->oRecordFromDB->sqlData[$this->name])) {
                return true;
            }
        } else {
            if (!empty($this->data) && '0000-00-00' !== $this->data) {
                return true;
            }
        }

        return false;
    }

    /**
     * {@inheritdoc}
     */
    public function getFrontendJavascriptInitMethodOnSubRecordLoad()
    {
        return 'initDateFields();';
    }

    /**
     * @return FlashMessageServiceInterface
     */
    private function getFlashMessageService()
    {
        return \ChameleonSystem\CoreBundle\ServiceLocator::get('chameleon_system_core.flash_messages');
    }

    /**
     * @return ViewRenderer
     */
    private function getViewRenderer()
    {
        return \ChameleonSystem\CoreBundle\ServiceLocator::get('chameleon_system_view_renderer.view_renderer');
    }
}
