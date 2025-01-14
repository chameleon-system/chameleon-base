<?php declare(strict_types=1);

namespace ChameleonSystem\CoreBundle\Service;

class DeletedFieldsService implements DeletedFieldsServiceInterface
{
    public function __construct(private readonly array $deletedFields)
    {
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
}
