<?php

/*
 * This file is part of the Chameleon System (https://www.chameleonsystem.com).
 *
 * (c) ESONO AG (https://www.esono.de)
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

use ChameleonSystem\AutoclassesBundle\TableConfExport\DataModelParts;

/**
 * a timestamp.
/**/
class TCMSFieldTimestamp extends TCMSField
{

    public function getDoctrineDataModelParts(string $namespace): ?DataModelParts
    {
        $defaultValue =  $this->oDefinition->sqlData['field_default_value'] ?? null;
        if ($defaultValue === '0000-00-00' || $defaultValue === '0000-00-00 00:00:00') {
            $defaultValue = null;
        }
        if (null !== $defaultValue) {
            $defaultValue = \DateTime::createFromFormat('Y-m-d H:i:s', $defaultValue);
        }
        $data = $this->getDoctrineDataModelViewData(['type' => '\DateTime', 'defaultValue' => $defaultValue]);
        $rendererProperty = $this->getDoctrineRenderer('model/default.property.php.twig', $data);
        $rendererMethod = $this->getDoctrineRenderer('model/default.methods.php.twig', $data);

        return new DataModelParts($rendererProperty->render(),$rendererMethod->render(), $data['allowDefaultValue']);
    }
    public function GetHTML()
    {
        $newdate = $this->ConvertPostDataToSQL();
        $html = '<input type="hidden" id="'.TGlobal::OutHTML($this->name).'" name="'.TGlobal::OutHTML($this->name).'" value="'.TGlobal::OutHTML($newdate).'" />';

        $variablesArray = array();
        if (!empty($this->data)) {
            $valArray = explode(' ', $this->data);
            $dateArray = explode('-', $valArray[0]);
            $timeArray = explode(':', $valArray[1]);

            $variablesArray['%timestamp%'] = TGlobal::OutHTML($dateArray[2].'.'.$dateArray[1].'.'.$dateArray[0].' '.$timeArray[0].':'.$timeArray[1].':'.$timeArray[2]);
            $html .= TGlobal::Translate('chameleon_system_core.field_timestamp.last_change', $variablesArray);
        } else {
            $year = date('Y');
            $month = date('m');
            $day = date('d');
            $hour = date('H');
            $minutes = date('i');
            $seconds = date('s');

            $variablesArray['%timestamp%'] = "{$day}.{$month}.{$year} {$hour}:{$minutes}:{$seconds}";
            $html .= TGlobal::Translate('chameleon_system_core.field_timestamp.will_be_set_to', $variablesArray);
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
        $returnVal = date('Y-m-d H:i:s');

        return $returnVal;
    }
}
