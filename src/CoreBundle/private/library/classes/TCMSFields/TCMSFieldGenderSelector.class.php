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
use Symfony\Contracts\Translation\TranslatorInterface;

/**
 * male or female.
 * /**/
class TCMSFieldGenderSelector extends TCMSFieldOption implements DoctrineTransformableInterface
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
            'length' => '1',
        ])->render();
    }

    /**
     * {@inheritdoc}
     */
    public function GetOptions()
    {
        parent::GetOptions();

        $translator = $this->getTranslationService();
        $this->options['m'] = $translator->trans('chameleon_system_core.field_gender.male', [], 'admin');
        $this->options['f'] = $translator->trans('chameleon_system_core.field_gender.female', [], 'admin');
    }

    /**
     * {@inheritdoc}
     */
    public function DataIsValid()
    {
        $dataIsValid = $this->CheckMandatoryField();
        if (false === $dataIsValid) {
            return $dataIsValid;
        }

        if ('m' === $this->data || 'f' === $this->data) {
            return true;
        }

        return false;
    }

    /**
     * @return TranslatorInterface
     */
    private function getTranslationService()
    {
        return ChameleonSystem\CoreBundle\ServiceLocator::get('translator');
    }
}
