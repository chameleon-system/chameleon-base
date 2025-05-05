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

class Delete extends AbstractQuery
{
    /**
     * {@inheritdoc}
     */
    protected function getBaseQuery($quotedTableName)
    {
        return "DELETE FROM $quotedTableName";
    }

    /** @noinspection PhpMissingParentCallCommonInspection
     * {@inheritdoc}
     * Disables the SET query part as it is invalid for DELETE statements.
     */
    protected function getSetQueryPart(MigrationQueryData $migrationQueryData, array $translatedFields)
    {
        return ['', []];
    }
}
