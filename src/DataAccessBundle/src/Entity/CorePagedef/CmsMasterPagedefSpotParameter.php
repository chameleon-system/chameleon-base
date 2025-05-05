<?php

namespace ChameleonSystem\DataAccessBundle\Entity\CorePagedef;

class CmsMasterPagedefSpotParameter
{
    public function __construct(
        private string $id,
        private ?int $cmsident = null,

        // TCMSFieldLookupParentID
        /** @var CmsMasterPagedefSpot|null - Belongs to cms page template spot */
        private ?CmsMasterPagedefSpot $cmsMasterPagedefSpot = null,
        // TCMSFieldVarchar
        /** @var string - Name */
        private string $name = '',
        // TCMSFieldVarchar
        /** @var string - Value */
        private string $value = ''
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

    // TCMSFieldLookupParentID
    public function getCmsMasterPagedefSpot(): ?CmsMasterPagedefSpot
    {
        return $this->cmsMasterPagedefSpot;
    }

    public function setCmsMasterPagedefSpot(?CmsMasterPagedefSpot $cmsMasterPagedefSpot): self
    {
        $this->cmsMasterPagedefSpot = $cmsMasterPagedefSpot;

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
    public function getValue(): string
    {
        return $this->value;
    }

    public function setValue(string $value): self
    {
        $this->value = $value;

        return $this;
    }
}
