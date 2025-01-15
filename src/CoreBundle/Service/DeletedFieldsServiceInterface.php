<?php

namespace ChameleonSystem\CoreBundle\Service;

interface DeletedFieldsServiceInterface
{
    /**
     * @return array<string,string[]> key: table name, values: list of field names
     */
    public function getDeletedFields(): array;

    /**
     * @return string[] list of field names of a given table
     */
    public function getTableDeletedFields(string $tableName): array;

    public function stripDeletedFields(string $query): string;
}
