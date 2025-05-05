<?php

namespace ChameleonSystem\DataAccessBundle\Entity\CoreTableConfiguration;

class CmsTblDisplayOrderfields
{
    public function __construct(
        private string $id,
        private ?int $cmsident = null,

        // TCMSFieldVarchar
        /** @var string - Database field name */
        private string $name = '',
        // TCMSFieldOption
        /** @var string - Order direction */
        private string $sortOrderDirection = 'ASC',
        // TCMSFieldPosition
        /** @var int - Position */
        private int $position = 0,
        // TCMSFieldLookupParentID
        /** @var CmsTblConf|null - Belongs to table */
        private ?CmsTblConf $cmsTblConf = null
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

    // TCMSFieldOption
    public function getSortOrderDirection(): string
    {
        return $this->sortOrderDirection;
    }

    public function setSortOrderDirection(string $sortOrderDirection): self
    {
        $this->sortOrderDirection = $sortOrderDirection;

        return $this;
    }

    // TCMSFieldPosition
    public function getPosition(): int
    {
        return $this->position;
    }

    public function setPosition(int $position): self
    {
        $this->position = $position;

        return $this;
    }

    // TCMSFieldLookupParentID
    public function getCmsTblConf(): ?CmsTblConf
    {
        return $this->cmsTblConf;
    }

    public function setCmsTblConf(?CmsTblConf $cmsTblConf): self
    {
        $this->cmsTblConf = $cmsTblConf;

        return $this;
    }
}
