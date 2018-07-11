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

require_once PATH_LIBRARY.'/functions/ConvertDate.fun.php';

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
        $viewRenderer->AddSourceObject('fieldIconUrl', TGlobal::GetStaticURL('/chameleon/blackbox/images/icons/calendar.gif'));

        return $viewRenderer->Render('TCMSFieldDate/dateInput.html.twig', null, false);
    }

    /**
     * {@inheritdoc}
     */
    public function _GetHTMLValue()
    {
        $data = parent::_GetHTMLValue();

        $htmlDate = ConvertDate($data, 'sql2g');
        if ('00.00.0000' === $htmlDate) {
            return '';
        }

        return $htmlDate;
    }

    /**
     * this method converts post data like datetime (3 fields with date, hours, minutes in human readable format)
     * to sql format.
     *
     * @return string
     */
    public function ConvertPostDataToSQL()
    {
        if (!empty($this->data) && !$this->IsSQLDate($this->data)) {
            return ConvertDate($this->data, 'g2sql');
        } elseif (!empty($this->data) && $this->IsSQLDate($this->data)) {
            return $this->data;
        }

        return '';
    }

    /**
     * {@inheritdoc}
     */
    public function GetReadOnly()
    {
        return TGlobal::OutHTML(ConvertDate($this->data, 'sql2g'));
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
        $aIncludes = array();
        $aIncludes[] = sprintf('<script src="%s" type="text/javascript"></script>', TGlobal::GetStaticURL('/chameleon/blackbox/javascript/jquery/jQueryUI/ui.core.js'));
        $aIncludes[] = sprintf('<link href="%s" media="screen" rel="stylesheet" type="text/css" />', TGlobal::GetStaticURLToWebLib('/javascript/jquery/jQueryUI/themes/cupertino/cupertino.css'));
        $aIncludes[] = sprintf('<script src="%s" type="text/javascript"></script>', TGlobal::GetStaticURL('/chameleon/blackbox/javascript/jquery/jQueryUI/datepicker/ui.datepicker.js'));

        $backendUser = TCMSUser::GetActiveUser();
        $currentLanguage = $backendUser->GetCurrentEditLanguage();
        if ('en' === $currentLanguage) {
            $currentLanguage .= '-GB';
        } // specify the EN version

        $aIncludes[] = sprintf('<script src="%s" type="text/javascript"></script>', TGlobal::GetStaticURL(
            '/chameleon/blackbox/javascript/jquery/jQueryUI/datepicker/i18n/ui.datepicker-'.$currentLanguage.'.js'
        ));

        $init = "
      <script type=\"text/javascript\">
      $(document).ready(function() {
        $.datepicker.setDefaults({
          showOn: 'button',
          buttonImageOnly: true,
          firstDay: 1,
          showWeek: true,
          dateFormat: 'dd.mm.yy',
          changeMonth: true,
          changeYear: true          
        });
      });
      </script>
      ";

        $aIncludes[] = $init;
        $aIncludes[] = sprintf('<script src="%s" type="text/javascript"></script>', TGlobal::GetStaticURL('/chameleon/blackbox/javascript/jquery/maskedinput/maskedinput.js'));

        return $aIncludes;
    }

    /**
     * @return string
     */
    public function GetRTFExport()
    {
        $date = $this->_GetFieldValue();
        $dateArray = explode('-', $date);

        $germanDate = ConvertDate($date, 'sql2g');

        setlocale(LC_TIME, 'de_DE@euro', 'de_DE', 'de', 'ge', 'de_DE.ISO8859-1');

        $timeStamp = mktime(0, 0, 0, $dateArray[1], $dateArray[2], $dateArray[0]);
        $dayString = strftime('%a', $timeStamp);

        setlocale(LC_TIME, 0);

        $returnString = $dayString.' '.$germanDate;

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

        if ($this->HasContent() && !$this->CheckValidDate($this->data)) {
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
        $patternUsDate = '/^(\d{4}|\d{2})[.|-](0[1-9]|1[012]|[1-9])[-|-](0[1-9]|[12][0-9]|3[01]|[1-9])$/';
        if (preg_match($patternGermanDate, $sSqlDateTime) || preg_match($patternUsDate, $sSqlDateTime)) {
            $timeStamp = strtotime($sSqlDateTime);
            $checkDateTime = date('d.m.Y', $timeStamp);
            $checkDateTimeUs = date('Y-m-d', $timeStamp);
            if ($sSqlDateTime === $checkDateTime || $sSqlDateTime === $checkDateTimeUs) {
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
    public function getHtmlHeadIncludes()
    {
        $aIncludes = parent::getHtmlHeadIncludes();

        $aIncludes[] = '<script src="'.TGlobal::GetStaticURL(
                '/chameleon/javascript/jquery/maskedinput/maskedinput.js'
            ).'" type="text/javascript"></script>';

        $aIncludes[] = "<script type=\"text/javascript\">
          $(document).ready(function() {
              initDateFields();
          });

          function initDateFields() {
             $('.dateMask').mask('99.99.9999');
          }
        </script>";

        return $aIncludes;
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
