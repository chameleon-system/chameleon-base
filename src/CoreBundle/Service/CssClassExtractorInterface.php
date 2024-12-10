<?php

namespace ChameleonSystem\CoreBundle\Service;

interface CssClassExtractorInterface
{
    public function extractCssClasses(string $filePath): array;
}