<?php

/*
 * This file is part of the Chameleon System (https://www.chameleonsystem.com).
 *
 * (c) ESONO AG (https://www.esono.de)
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace ChameleonSystem\core\DatabaseAccessLayer\LengthCalculationStrategy;

use Doctrine\DBAL\Connection;

interface EntityListLengthCalculationStrategyInterface
{
    public function __construct(Connection $databaseConnection);

    /**
     * @param string $normalizedQuery - should be all caps with not \n, \r or \t
     *
     * @return bool
     */
    public function isValidStrategyFor($normalizedQuery);

    public function calculateLength($query, array $queryParameters = [], array $queryParameterTypes = []);
}
