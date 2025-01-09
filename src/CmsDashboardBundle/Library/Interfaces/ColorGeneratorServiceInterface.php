<?php

namespace ChameleonSystem\CmsDashboardBundle\Library\Interfaces;
interface ColorGeneratorServiceInterface
{
    public function generateColor(int $index, int $total): string;
}