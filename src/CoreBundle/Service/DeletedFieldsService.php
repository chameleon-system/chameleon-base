<?php

declare(strict_types=1);

namespace ChameleonSystem\CoreBundle\Service;

use Doctrine\DBAL\Connection;

class DeletedFieldsService implements DeletedFieldsServiceInterface
{
    public function __construct(
        private readonly array $deletedFields,
        private readonly Connection $connection,
    ) {
    }

    /**
     * {@inheritdoc}
     */
    public function getDeletedFields(?string $tableName = null): array
    {
        return $this->deletedFields;
    }

    /**
     * {@inheritdoc}
     */
    public function getTableDeletedFields(string $tableName): array
    {
        return $this->deletedFields[$tableName] ?? [];
    }

    public function stripDeletedFields(string $query): string
    {
        $tableNamePattern = '/^(INSERT INTO|UPDATE) `(?<table>.+?)`/iu';
        if (1 !== preg_match($tableNamePattern, $query, $matches)) {
            return $query;
        }

        $table = $matches['table'];
        $deletedFields = $this->getTableDeletedFields($table);

        foreach ($deletedFields as $deletedField) {
            if (false === str_contains($query, $deletedField)) {
                continue;
            }

            $maskedField = $this->connection->quoteIdentifier($deletedField);
            $leadingCommaPattern = '/,\s*'.$maskedField." = '(\\\'|[^'])*'\s*/iu";
            $trailingCommaPattern = '/'.$maskedField." = '(\\\'|[^'])*'\s*,\s*/iu";
            $query = preg_replace([$leadingCommaPattern, $trailingCommaPattern], '', $query);
        }

        return $query;
    }
}
