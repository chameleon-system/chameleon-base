<?php

/*
 * This file is part of the Chameleon System (https://www.chameleonsystem.com).
 *
 * (c) ESONO AG (https://www.esono.de)
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace ChameleonSystem\DatabaseMigration\Query;

/**
 * QueryInterface defines a service that generates and executes SQL queries from MigrationQueryData objects.
 */
interface QueryInterface
{
    /**
     * Executes a query defined by the given $migrationQueryData object. Internally calls getQuery() which determines the
     * query type by calling the concrete subclasses' getBaseQuery() method.
     *
     * @return array An array consisting of the executed query (string) and and parameters (array)
     *
     * @throws \Doctrine\DBAL\DBALException if a database error occurs while executing
     * @throws \InvalidArgumentException see assertPrerequisites()
     */
    public function execute(MigrationQueryData $migrationQueryData);

    /**
     * Generates and returns a query and query parameters for the passed $migrationQueryData object.
     *
     * @return array An array consisting of the query (string) and and parameters (array)
     */
    public function getQuery(MigrationQueryData $migrationQueryData);
}
