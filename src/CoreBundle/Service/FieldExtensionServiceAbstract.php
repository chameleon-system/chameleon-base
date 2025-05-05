<?php

namespace ChameleonSystem\CoreBundle\Service;

use TCMSField;

abstract class FieldExtensionServiceAbstract
{
    abstract public function getFieldExtensionHtml(\TCMSField $field): string;

    abstract public function getHtmlHeadIncludes(\TCMSField $field): array;

    abstract public function getHtmlFooterIncludes(\TCMSField $field): array;

    /**
     * You may load any data here from the same record or another.
     * For example you could load the name + description of a product, generate AI based SEO tags
     * and save them in the active field.
     *
     * @param \TCMSField $field The TCMSField instance
     *
     * @return string The value of the TCMSField
     */
    abstract public function getFieldValue(\TCMSField $field): string;

    protected function isFieldTypeText(\TdbCmsFieldType $fieldType): bool
    {
        $textFieldTypeSystemNames = [
            'CMSFIELD_STRING_UNIQUE',
            'CMSFIELD_STRING',
            'CMSFIELD_TEXT',
            'CMSFIELD_MARKDOWNTEXT',
            'CMSFIELD_WYSIWYG',
            'CMSFIELD_WYSIWYG_LIGHT',
        ];

        return \in_array($fieldType->fieldConstname, $textFieldTypeSystemNames);
    }
}
