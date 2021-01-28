<?php

namespace ChameleonSystem\CoreBundle\Interfaces;

interface CheckTableAccessRightsInterface
{
    public function checkAccessRightsOnTable(): bool;
}
