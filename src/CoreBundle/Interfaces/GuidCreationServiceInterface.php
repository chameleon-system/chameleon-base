<?php

namespace ChameleonSystem\CoreBundle\Interfaces;

use ChameleonSystem\CoreBundle\Exception\GuidCreationFailedException;

interface GuidCreationServiceInterface
{
    /**
     * @param string $tableName
     * @return string
     * @throws GuidCreationFailedException
     */
    public function findUnusedId(string $tableName): string;
}
