<?php

namespace ChameleonSystem\DataAccessBundle\Entity\Core;

class PkgCmsCounter
{
    public function __construct(
        private string $id,
        private ?int $cmsident = null,

        // TCMSFieldVarchar
        /** @var string - Name */
        private string $name = '',
        // TCMSFieldVarchar
        /** @var string - System name */
        private string $systemName = '',
        // TCMSFieldExtendedLookupMultiTable
        /** @var string - Owner */
        private string $owner = '',
        // TCMSFieldExtendedLookupMultiTable
        /** @var string - Owner */
        private string $ownerTableName = '',
        // TCMSFieldNumber
        /** @var int - Value */
        private int $value = 0
    ) {
    }

    public function getId(): string
    {
        return $this->id;
    }

    public function setId(string $id): self
    {
        $this->id = $id;

        return $this;
    }

    public function getCmsident(): ?int
    {
        return $this->cmsident;
    }

    public function setCmsident(int $cmsident): self
    {
        $this->cmsident = $cmsident;

        return $this;
    }

    // TCMSFieldVarchar
    public function getName(): string
    {
        return $this->name;
    }

    public function setName(string $name): self
    {
        $this->name = $name;

        return $this;
    }

    // TCMSFieldVarchar
    public function getSystemName(): string
    {
        return $this->systemName;
    }

    public function setSystemName(string $systemName): self
    {
        $this->systemName = $systemName;

        return $this;
    }

    // TCMSFieldExtendedLookupMultiTable
    public function getOwner(): string
    {
        return $this->owner;
    }

    public function setOwner(string $owner): self
    {
        $this->owner = $owner;

        return $this;
    }

    // TCMSFieldExtendedLookupMultiTable
    public function getOwnerTableName(): string
    {
        return $this->ownerTableName;
    }

    public function setOwnerTableName(string $ownerTableName): self
    {
        $this->ownerTableName = $ownerTableName;

        return $this;
    }

    // TCMSFieldNumber
    public function getValue(): int
    {
        return $this->value;
    }

    public function setValue(int $value): self
    {
        $this->value = $value;

        return $this;
    }
}
