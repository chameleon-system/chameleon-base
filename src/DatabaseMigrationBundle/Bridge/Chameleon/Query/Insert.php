<?php

/*
 * This file is part of the Chameleon System (https://www.chameleonsystem.com).
 *
 * (c) ESONO AG (https://www.esono.de)
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace ChameleonSystem\DatabaseMigrationBundle\Bridge\Chameleon\Query;

use ChameleonSystem\DatabaseMigration\Query\MigrationQueryData;

class Insert extends AbstractQuery
{
    /**
     * {@inheritDoc}
     */
    protected function assertPrerequisites(MigrationQueryData $migrationQueryData)
    {
        parent::assertPrerequisites($migrationQueryData);

        if (0 === count($migrationQueryData->getFields())) {
            throw new \InvalidArgumentException('At least 1 field must be written in an insert statement.');
        }
    }

    /**
     * {@inheritdoc}
     */
    protected function getBaseQuery($quotedTableName)
    {
        return "INSERT INTO $quotedTableName";
    }

    /** @noinspection PhpMissingParentCallCommonInspection
     * Disables the WHERE query part as it is invalid for INSERT statements.
     *
     * @return array
     */
    protected function getWhereQueryPart(MigrationQueryData $migrationQueryData, array $translatedFields)
    {
        return ['', []];
    }
}
