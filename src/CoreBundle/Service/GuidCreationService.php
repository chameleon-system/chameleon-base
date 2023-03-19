<?php

/*
 * This file is part of the Chameleon System (https://www.chameleonsystem.com).
 *
 * (c) ESONO AG (https://www.esono.de)
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace ChameleonSystem\CoreBundle\Service;

use ChameleonSystem\CoreBundle\Exception\GuidCreationFailedException;
use ChameleonSystem\CoreBundle\Interfaces\GuidCreationServiceInterface;
use Doctrine\DBAL\Connection;
use Doctrine\DBAL\Exception;

class GuidCreationService implements GuidCreationServiceInterface
{
    private Connection $connection;
    private \TTools $tools;

    public function __construct(Connection $connection, \TTools $tools)
    {
        $this->connection = $connection;
        $this->tools = $tools;
    }

    /**
     * {@inheritDoc}
     */
    public function findUnusedId(string $tableName): string
    {
        $quotedTableName = $this->connection->quoteIdentifier($tableName);
        $tries = 10;

        do {
            $id = $this->tools::GetUUID();
            try {
                $count = $this->connection->fetchOne("SELECT count(*) FROM $quotedTableName WHERE `id` = :id", ['id' => $id]);
                if (false !== $count) {
                    $count = \intval($count);
                }
            } catch (Exception $exception) {
                throw new GuidCreationFailedException('GuidCreationService cannot create an unused ID for '.$tableName, $exception->getCode(), $exception);
            }
            --$tries;
        } while (false !== $count && $count > 0 && $tries > 0);

        if (0 !== $count) {
            throw new GuidCreationFailedException('GuidCreationService was unable to create an unused ID after some attempts for '.$tableName);
        }

        return $id;
    }
}
