<?php

namespace ChameleonSystem\DataAccessBundle\Entity\CoreModule;

class ModuleCustomlistConfigSortfields
{
    public function __construct(
        private string $id,
        private ?int $cmsident = null,

        // TCMSFieldLookupParentID
        /** @var ModuleCustomlistConfig|null - Belongs to list */
        private ?ModuleCustomlistConfig $moduleCustomlistConfig = null,
        // TCMSFieldVarchar
        /** @var string - Field name */
        private string $name = '',
        // TCMSFieldOption
        /** @var string - Direction */
        private string $direction = 'ASC',
        // TCMSFieldPosition
        /** @var int - Position */
        private int $position = 0
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
    public function getModuleCustomlistConfig(): ?ModuleCustomlistConfig
    {
        return $this->moduleCustomlistConfig;
    }

    public function setModuleCustomlistConfig(?ModuleCustomlistConfig $moduleCustomlistConfig): self
    {
        $this->moduleCustomlistConfig = $moduleCustomlistConfig;

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
    public function getDirection(): string
    {
        return $this->direction;
    }

    public function setDirection(string $direction): self
    {
        $this->direction = $direction;

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
}
