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

class TCMSFieldDateTimeNow extends TCMSFieldDateTime
{
    /**
     * view path for frontend.
     */
    protected $sViewPath = 'TCMSFields/views/TCMSFieldDateTimeNow';

    public function GetHTML()
    {
        $viewRenderer = $this->getViewRenderer();
        $viewRenderer->AddSourceObject('fieldName', $this->name);
        $viewRenderer->AddSourceObject('fieldValue', $this->_GetHTMLValue());
        $viewRenderer->AddSourceObject('language', TCMSUser::GetActiveUser()->GetCurrentEditLanguage());
        $viewRenderer->AddSourceObject('datetimepickerFormat', 'L LTS');
        $viewRenderer->AddSourceObject('datetimepickerSideBySide', 'true');
        $viewRenderer->AddSourceObject('datetimepickerWithIcon', false);

        return $viewRenderer->Render('TCMSFieldDate/datetimeInput.html.twig', null, false);
    }

    public function _GetHTMLValue()
    {
        $fieldValue = parent::_GetHTMLValue();
        if ('' === $fieldValue || '0000-00-00 00:00:00' === $fieldValue) {
            $fieldValue = date('Y-m-d H:i:s');
        }

        return $fieldValue;
    }

    public function GetReadOnly()
    {
        $currentDate = $this->_GetHTMLValue();
        if ('0000-00-00 00:00:00' == $currentDate) {
            $html = date('Y-m-d H:i:s');
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
            $html .= TGlobal::OutHTML($date.' '.$hour.':'.$minutes.' '.TGlobal::Translate('chameleon_system_core.field_date_time.time'));
        }

        return $html;
    }

    private function getViewRenderer(): ViewRenderer
    {
        return ServiceLocator::get('chameleon_system_view_renderer.view_renderer');
    }
}
