<?php

namespace ChameleonSystem\CoreBundle\Service;

use ChameleonSystem\CoreBundle\Interfaces\FieldExtensionInterface;
use ChameleonSystem\CoreBundle\Interfaces\FieldExtensionRenderServiceInterface;
use ChameleonSystem\CoreBundle\ServiceLocator;

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
        $items = [];
        foreach ($this->fieldExtensions as $fieldExtension) {
            $html = trim($fieldExtension->getFieldExtensionHtml($field));
            if ('' !== $html) {
                $items[] = $html;
            }
        }

        if ([] === $items) {
            return '';
        }

        $fieldName = preg_replace('/[^a-zA-Z0-9_-]/', '-', $field->name);
        $uniqueId = uniqid('', false);

        /** @var \ViewRenderer $viewRenderer */
        $viewRenderer = ServiceLocator::get('chameleon_system_view_renderer.view_renderer');
        $viewRenderer->AddSourceObject('fieldName', $fieldName);
        $viewRenderer->AddSourceObject('items', $items);
        $viewRenderer->AddSourceObject('menuId', 'field-extension-menu-'.$fieldName.'-'.$uniqueId);
        $viewRenderer->AddSourceObject('toggleId', 'field-extension-toggle-'.$fieldName.'-'.$uniqueId);

        return $viewRenderer->Render('FieldExtension/dropdown.html.twig');
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
