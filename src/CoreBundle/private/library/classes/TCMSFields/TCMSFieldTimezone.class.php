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
 * a timezone field.
 * /**/
class TCMSFieldTimezone extends TCMSField implements DoctrineTransformableInterface
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
        return $this->getDoctrineRenderer('mapping/string-char.xml.twig', [
            'fieldName' => $this->snakeToCamelCase($this->name),
            'type' => 'string',
            'column' => $this->name,
            'comment' => $this->oDefinition->sqlData['translation'],
            'default' => $this->oDefinition->sqlData['field_default_value'],
            'length' => 3,
        ])->render();
    }

    public function GetHTML()
    {
        $value = $this->_GetHTMLValue();
        $html = '<select name="'.TGlobal::OutHTML($this->name).'" id="'.TGlobal::OutHTML($this->name)."\" class=\"form-control form-control-sm\" style=\"width: 80px; display: inline;\">\n";
        $html .= '<option value=""';
        if ('' == $value) {
            $html .= ' selected>'.TGlobal::OutHTML(ChameleonSystem\CoreBundle\ServiceLocator::get('translator')->trans('chameleon_system_core.form.select_box_nothing_selected'))."</option>\n";
        } else {
            $html .= '>'.TGlobal::OutHTML(ChameleonSystem\CoreBundle\ServiceLocator::get('translator')->trans('chameleon_system_core.form.select_box_nothing_selected'))."</option>\n";
        }
        $html .= '<option value="+12"';
        if ('+12' == $value) {
            $html .= " selected>+12</option>\n";
        } else {
            $html .= ">+12</option>\n";
        }
        $html .= '<option value="+11"';
        if ('+11' == $value) {
            $html .= " selected>+11</option>\n";
        } else {
            $html .= ">+11</option>\n";
        }
        $html .= '<option value="+10"';
        if ('+10' == $value) {
            $html .= " selected>+10</option>\n";
        } else {
            $html .= ">+10</option>\n";
        }
        $html .= '<option value="+9"';
        if ('+9' == $value) {
            $html .= " selected>+9</option>\n";
        } else {
            $html .= ">+9</option>\n";
        }
        $html .= '<option value="+8"';
        if ('+8' == $value) {
            $html .= " selected>+8</option>\n";
        } else {
            $html .= ">+8</option>\n";
        }
        $html .= '<option value="+7"';
        if ('+7' == $value) {
            $html .= " selected>+7</option>\n";
        } else {
            $html .= ">+7</option>\n";
        }
        $html .= '<option value="+6"';
        if ('+6' == $value) {
            $html .= " selected>+6</option>\n";
        } else {
            $html .= ">+6</option>\n";
        }
        $html .= '<option value="+5"';
        if ('+5' == $value) {
            $html .= " selected>+5</option>\n";
        } else {
            $html .= ">+5</option>\n";
        }
        $html .= '<option value="+4"';
        if ('+4' == $value) {
            $html .= " selected>+4</option>\n";
        } else {
            $html .= ">+4</option>\n";
        }
        $html .= '<option value="+3"';
        if ('+3' == $value) {
            $html .= " selected>+3</option>\n";
        } else {
            $html .= ">+3</option>\n";
        }
        $html .= '<option value="+2"';
        if ('+2' == $value) {
            $html .= " selected>+2</option>\n";
        } else {
            $html .= ">+2</option>\n";
        }
        $html .= '<option value="+1"';
        if ('+1' == $value) {
            $html .= " selected>+1</option>\n";
        } else {
            $html .= ">+1</option>\n";
        }
        $html .= '<option value="+0"';
        if ('+0' == $value) {
            $html .= " selected>+0</option>\n";
        } else {
            $html .= ">+0</option>\n";
        }
        $html .= '<option value="-1"';
        if ('-1' == $value) {
            $html .= " selected>-1</option>\n";
        } else {
            $html .= ">-1</option>\n";
        }
        $html .= '<option value="-2"';
        if ('-2' == $value) {
            $html .= " selected>-2</option>\n";
        } else {
            $html .= ">-2</option>\n";
        }
        $html .= '<option value="-3"';
        if ('-3' == $value) {
            $html .= " selected>-3</option>\n";
        } else {
            $html .= ">-3</option>\n";
        }
        $html .= '<option value="-4"';
        if ('-4' == $value) {
            $html .= " selected>-4</option>\n";
        } else {
            $html .= ">-4</option>\n";
        }
        $html .= '<option value="-5"';
        if ('-5' == $value) {
            $html .= " selected>-5</option>\n";
        } else {
            $html .= ">-5</option>\n";
        }
        $html .= '<option value="-6"';
        if ('-6' == $value) {
            $html .= " selected>-6</option>\n";
        } else {
            $html .= ">-6</option>\n";
        }
        $html .= '<option value="-7"';
        if ('-7' == $value) {
            $html .= " selected>-7</option>\n";
        } else {
            $html .= ">-7</option>\n";
        }
        $html .= '<option value="-8"';
        if ('-8' == $value) {
            $html .= " selected>-8</option>\n";
        } else {
            $html .= ">-8</option>\n";
        }
        $html .= '<option value="-9"';
        if ('-9' == $value) {
            $html .= " selected>-9</option>\n";
        } else {
            $html .= ">-9</option>\n";
        }
        $html .= '<option value="-10"';
        if ('-10' == $value) {
            $html .= " selected>-10</option>\n";
        } else {
            $html .= ">-10</option>\n";
        }
        $html .= '<option value="-11"';
        if ('-11' == $value) {
            $html .= " selected>-11</option>\n";
        } else {
            $html .= ">-11</option>\n";
        }
        $html .= "</select>\n";

        return $html;
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
        if ($this->HasContent() && $bDataIsValid) {
            if (intval($this->data) < 12 && intval($this->data) >= -11) {
                $bDataIsValid = true;
            } else {
                $bDataIsValid = false;
                $oMessageManager = TCMSMessageManager::GetInstance();
                $sConsumerName = TCMSTableEditorManager::MESSAGE_MANAGER_CONSUMER;
                $sFieldTitle = $this->oDefinition->GetName();
                $oMessageManager->AddMessage($sConsumerName, 'TABLEEDITOR_FIELD_TIMEZONE_NOT_VALID', ['sFieldName' => $this->name, 'sFieldTitle' => $sFieldTitle]);
            }
        }

        return $bDataIsValid;
    }
}
