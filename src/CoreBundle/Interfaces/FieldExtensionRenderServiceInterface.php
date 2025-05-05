<?php

namespace ChameleonSystem\CoreBundle\Interfaces;

interface FieldExtensionRenderServiceInterface
{
    public function addFieldExtension(FieldExtensionInterface $fieldExtension): void;

    public function renderFieldExtension(\TCMSField $field): string;

    public function getHtmlHeadIncludes(\TCMSField $field): array;

    public function getHtmlFooterIncludes(\TCMSField $field): array;

    public function getValueForFieldExtension(\TCMSField $field, string $fieldExtensionService): string;
}
