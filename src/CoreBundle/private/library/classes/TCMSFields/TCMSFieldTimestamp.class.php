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
use ChameleonSystem\AutoclassesBundle\TableConfExport\DoctrineTransformableInterface;

/**
 * a timestamp.
/**/
class TCMSFieldTimestamp extends TCMSField implements DoctrineTransformableInterface
{

    public function getDoctrineDataModelParts(string $namespace): DataModelParts
    {
        $parameters = [
            'source' => get_class($this),
            'type' => '?\\DateTime',
            'docCommentType' => '\\DateTime|null',
            'description' => $this->oDefinition->sqlData['translation'],
            'propertyName' => $this->snakeToCamelCase($this->name),
            'defaultValue' => 'null',
            'allowDefaultValue' => true,
            'getterName' => 'get'. $this->snakeToCamelCase($this->name, false),
            'setterName' => 'set'. $this->snakeToCamelCase($this->name, false),
        ];
        $propertyCode = $this->getDoctrineRenderer('model/default.property.php.twig', $parameters)->render();
        $methodCode = $this->getDoctrineRenderer('model/default.methods.php.twig', $parameters)->render();

        return new DataModelParts(
            $propertyCode,
            $methodCode,
            [],
            true
        );
    }

    public function getDoctrineDataModelXml(string $namespace): string
    {
        return $this->getDoctrineRenderer('mapping/datetime.xml.twig', [
            'fieldName' => $this->snakeToCamelCase($this->name),
            'column' => $this->name,
            'type' => 'datetime',
            'comment' => $this->oDefinition->sqlData['translation'],
            'default' => null,
        ])->render();
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
