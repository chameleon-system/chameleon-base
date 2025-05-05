<?php

namespace ChameleonSystem\DataAccessBundle\Entity\Core;

class DataCountry
{
    public function __construct(
        private string $id,
        private ?int $cmsident = null,

        // TCMSFieldBoolean
        /** @var bool - Active */
        private bool $active = true,
        // TCMSFieldVarchar
        /** @var string - Name */
        private string $name = '',
        // TCMSFieldLookup
        /** @var TCountry|null - System country */
        private ?TCountry $tCountry = null,
        // TCMSFieldBoolean
        /** @var bool - Belongs to main group */
        private bool $primaryGroup = false,
        // TCMSFieldVarchar
        /** @var string - PLZ pattern */
        private string $postalcodePattern = ''
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

    // TCMSFieldBoolean
    public function isActive(): bool
    {
        return $this->active;
    }

    public function setActive(bool $active): self
    {
        $this->active = $active;

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

    // TCMSFieldLookup
    public function getTCountry(): ?TCountry
    {
        return $this->tCountry;
    }

    public function setTCountry(?TCountry $tCountry): self
    {
        $this->tCountry = $tCountry;

        return $this;
    }

    // TCMSFieldBoolean
    public function isPrimaryGroup(): bool
    {
        return $this->primaryGroup;
    }

    public function setPrimaryGroup(bool $primaryGroup): self
    {
        $this->primaryGroup = $primaryGroup;

        return $this;
    }

    // TCMSFieldVarchar
    public function getPostalcodePattern(): string
    {
        return $this->postalcodePattern;
    }

    public function setPostalcodePattern(string $postalcodePattern): self
    {
        $this->postalcodePattern = $postalcodePattern;

        return $this;
    }
}
