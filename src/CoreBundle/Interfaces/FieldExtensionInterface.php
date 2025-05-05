<?php

namespace ChameleonSystem\CoreBundle\Interfaces;

use TCMSField;

interface FieldExtensionInterface
{
    public function getFieldExtensionHtml(\TCMSField $field): string;

    public function getHtmlHeadIncludes(\TCMSField $field): array;

    public function getHtmlFooterIncludes(\TCMSField $field): array;

    /**
     * You may load any data here from the same record or another.
     * For example you could load the name + description of a product, generate AI based SEO tags
     * and save them in the active field.
     *
     * This method is accesible via ajax call.
     * Example:
     * /cms?tableid={{tableId}}&pagedef=tableeditor&id={{recordId}}&module_fnc%5Bcontentmodule%5D=ExecuteAjaxCall&_fnc=getValueForFieldExtension&callFieldMethod=1&_fieldName={{fieldName}}&callingFieldExtension={{fullNameSpaceOfYourServiceClass}}
     *
     * @param \TCMSField $field The TCMSField instance
     *
     * @return string The value of the TCMSField
     */
    public function getFieldValue(\TCMSField $field): string;
}
