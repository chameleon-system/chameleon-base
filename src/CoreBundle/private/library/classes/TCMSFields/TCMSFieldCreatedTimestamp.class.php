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
 * a timestamp set only when the record is created (ie when the field is still empty).
 * /**/
class TCMSFieldCreatedTimestamp extends TCMSFieldTimestamp
{
    protected function getDoctrineDataModelXml(string $namespace): string
    {
        return $this->getDoctrineRenderer('mapping/datetime.xml.twig', [
            'fieldName' => $this->snakeToCamelCase($this->name),
            'column' => $this->name,
            'type' => 'datetime',
            'comment' => $this->oDefinition->sqlData['translation'],
            'default' => 'CURRENT_TIMESTAMP',
        ])->render();
    }

    public function GetHTML()
    {
        $newdate = $this->ConvertPostDataToSQL();
        $html = '<input type="hidden" id="'.TGlobal::OutHTML($this->name).'" name="'.TGlobal::OutHTML($this->name).'" value="'.TGlobal::OutHTML($newdate).'" />';

        $variablesArray = [];
        if (!empty($this->data) && '0000-00-00 00:00:00' != $this->data) {
            $valArray = explode(' ', $this->data);
            $dateArray = explode('-', $valArray[0]);
            $timeArray = explode(':', $valArray[1]);

            $variablesArray['%timestamp%'] = TGlobal::OutHTML($dateArray[2].'.'.$dateArray[1].'.'.$dateArray[0].' '.$timeArray[0].':'.$timeArray[1].':'.$timeArray[2]);
            $html .= ChameleonSystem\CoreBundle\ServiceLocator::get('translator')->trans('chameleon_system_core.field_timestamp.is_set_to', $variablesArray);
        } else {
            $year = date('Y');
            $month = date('m');
            $day = date('d');
            $hour = date('H');
            $minutes = date('i');
            $seconds = date('s');

            $variablesArray['%timestamp%'] = "{$day}.{$month}.{$year} {$hour}:{$minutes}:{$seconds}";
            $html .= ChameleonSystem\CoreBundle\ServiceLocator::get('translator')->trans('chameleon_system_core.field_timestamp.will_be_set_to', $variablesArray);
        }

        return $html;
    }

    /**
     * renders the read only view of the field.
     *
     * @return string
     */
    public function GetReadOnly()
    {
        $html = $this->GetHTML();

        return $html;
    }

    /**
     * this method converts post data like datetime (3 fields with date, hours, minutes in human readable format)
     * to sql format.
     */
    public function ConvertPostDataToSQL()
    {
        if (empty($this->data) || '0000-00-00 00:00:00' == $this->data) {
            $sReturnValue = parent::ConvertPostDataToSQL();
        } else {
            $sReturnValue = $this->data;
        }

        return $sReturnValue;
    }
}
