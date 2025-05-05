<?php

namespace ChameleonSystem\AutoclassesBundle\TableConfExport;

use Twig\Extension\AbstractExtension;
use Twig\TwigFilter;

class TwigExtension extends AbstractExtension
{
    public function getFilters()
    {
        return [
            new TwigFilter('chameleon_ucfirst', static fn ($value) => ucfirst($value)),
        ];
    }
}
