<?php

namespace ChameleonSystem\CoreBundle\Service;

use ChameleonSystem\CoreBundle\Exception\GuidCreationFailedException;
use ChameleonSystem\CoreBundle\Interfaces\GuidCreationServiceInterface;
use Doctrine\DBAL\Connection;
use Doctrine\DBAL\Exception;

class GuidCreationService implements GuidCreationServiceInterface
{
    private Connection $connection;

    public function __construct(Connection $connection)
    {
        $this->connection = $connection;
    }

    /**
     * {@inheritDoc}
     */
    public function findUnusedId(string $tableName): string
    {
        $quotedTableName = $this->connection->quoteIdentifier($tableName);
        $tries = 10;

        do {
            $id = \TTools::GetUUID();
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
