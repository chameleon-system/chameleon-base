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
 * todays date (editable).
/**/
class TCMSFieldDateToday extends TCMSFieldDate
{
    /**
     * view path for frontend.
     */
    protected $sViewPath = 'TCMSFields/views/TCMSFieldDateToday';

    /**
     * indicates if the date currently stored in the database is 0000-00-00
     * we need this info to show that in the field so the users don`t get confused why
     * the field shows the current date, but the record doesn`t have it in the database.
     *
     * @var bool
     */
    protected $currentDateIsEmpty = false;

    public function GetHTML()
    {
        $html = parent::GetHTML();

        if ($this->currentDateIsEmpty) {
            $html .= '<span class="alert alert-info">'.TGlobal::Translate('chameleon_system_core.field_date_time.not_set').'</span>';
        }

        return $html;
    }

    public function _GetHTMLValue()
    {
        $htmldate = $this->data;
        if ('0000-00-00' === $htmldate || empty($htmldate)) {
            $this->currentDateIsEmpty = true;
            $htmldate = date('Y-m-d');
        }

        return $htmldate;
    }
}
