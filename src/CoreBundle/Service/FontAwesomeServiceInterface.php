<?php

namespace ChameleonSystem\CoreBundle\Service;

interface FontAwesomeServiceInterface
{
    public function filterFontAwesomeClasses(array $cssClassNames): array;
}
