<?php

namespace ChameleonSystem\CoreBundle\Service;

use ChameleonSystem\CoreBundle\Interfaces\FieldExtensionInterface;
use ChameleonSystem\CoreBundle\Interfaces\FieldExtensionRenderServiceInterface;

class FieldExtensionRenderService implements FieldExtensionRenderServiceInterface
{
    /**
     * @var array<FieldExtensionInterface>
     */
    private array $fieldExtensions = [];

    public function addFieldExtension(FieldExtensionInterface $fieldExtension): void
    {
        $this->fieldExtensions[] = $fieldExtension;
    }

    public function renderFieldExtension(\TCMSField $field): string
    {
        $html = '';
        foreach ($this->fieldExtensions as $fieldExtension) {
            $html .= $fieldExtension->getFieldExtensionHtml($field);
        }

        return $html;
    }

    public function getHtmlHeadIncludes(\TCMSField $field): array
    {
        $includes = [];
        foreach ($this->fieldExtensions as $fieldExtension) {
            $includes = array_merge($includes, $fieldExtension->getHtmlHeadIncludes($field));
        }

        return $includes;
    }

    public function getHtmlFooterIncludes(\TCMSField $field): array
    {
        $includes = [];
        foreach ($this->fieldExtensions as $fieldExtension) {
            $includes = array_merge($includes, $fieldExtension->getHtmlFooterIncludes($field));
        }

        return $includes;
    }

    public function getValueForFieldExtension(\TCMSField $field, string $fieldExtensionService): string
    {
        $value = '';
        foreach ($this->fieldExtensions as $fieldExtension) {
            if (is_a($fieldExtension, $fieldExtensionService)) {
                $value = $fieldExtension->getFieldValue($field);
                break;
            }
        }

        return $value;
    }
}
