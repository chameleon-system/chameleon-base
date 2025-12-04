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
        $items = [];
        foreach ($this->fieldExtensions as $fieldExtension) {
            $html = trim($fieldExtension->getFieldExtensionHtml($field));
            if ('' !== $html) {
                // wrap to neutralize floats from individual extension buttons/links
                $items[] = '<div class="field-extension-item" style="display: block; padding: 4px 0; float: none; text-align: left;">'
                    .$html.'</div>';
            }
        }

        if ([] === $items) {
            return '';
        }

        $fieldName = preg_replace('/[^a-zA-Z0-9_-]/', '-', $field->name);
        $uniqueId = uniqid('', false);
        $toggleId = 'field-extension-toggle-'.$fieldName.'-'.$uniqueId;
        $menuId = 'field-extension-menu-'.$fieldName.'-'.$uniqueId;
        $menuContent = implode('', $items);

        return <<<HTML
<div class="field-extension-dropdown" style="position: relative; display: block; text-align: right; margin-top: 6px;">
    <button type="button" class="btn btn-outline-secondary btn-sm" id="{$toggleId}" title="Field extensions">
        <span class="fas fa-ellipsis-h"></span>
    </button>
    <div class="field-extension-menu dropdown-menu" id="{$menuId}" style="display: none; position: absolute; right: 0; left: auto; top: calc(100% + 4px); z-index: 1000; min-width: 220px; max-width: min(420px, calc(100vw - 32px)); padding: 8px 10px; box-shadow: 0 4px 12px rgba(0, 0, 0, 0.15); white-space: normal; word-break: break-word; background: #fff; border: 1px solid #ddd; border-radius: 4px;">
        {$menuContent}
    </div>
</div>
<script>
    (function () {
        var toggle = document.getElementById('{$toggleId}');
        var menu = document.getElementById('{$menuId}');
        if (!toggle || !menu) {
            return;
        }

        toggle.addEventListener('click', function (event) {
            event.preventDefault();
            event.stopPropagation();
            if ('block' === menu.style.display) {
                menu.style.display = 'none';
            } else {
                menu.style.display = 'block';
            }
        });

        document.addEventListener('click', function (event) {
            if (!menu.contains(event.target) && event.target !== toggle) {
                menu.style.display = 'none';
            }
        });
    })();
</script>
HTML;
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
