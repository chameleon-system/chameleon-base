<?php

namespace ChameleonSystem\DataAccessBundle\Entity\Core;

class CmsUsergroup
{
    public function __construct(
        private string $id,
        private ?int $cmsident = null,

        // TCMSFieldVarchar
        /** @var string - German translation */
        private string $name = '',
        // TCMSFieldVarchar
        /** @var string - CMS group ID */
        private string $internalIdentifier = '',
        // TCMSFieldBoolean
        /** @var bool - Is selectable */
        private bool $isChooseable = true,
        // TCMSFieldBoolean
        /** @var bool - Required by the system */
        private bool $isSystem = false
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
    public function getInternalIdentifier(): string
    {
        return $this->internalIdentifier;
    }

    public function setInternalIdentifier(string $internalIdentifier): self
    {
        $this->internalIdentifier = $internalIdentifier;

        return $this;
    }

    // TCMSFieldBoolean
    public function isIsChooseable(): bool
    {
        return $this->isChooseable;
    }

    public function setIsChooseable(bool $isChooseable): self
    {
        $this->isChooseable = $isChooseable;

        return $this;
    }

    // TCMSFieldBoolean
    public function isIsSystem(): bool
    {
        return $this->isSystem;
    }

    public function setIsSystem(bool $isSystem): self
    {
        $this->isSystem = $isSystem;

        return $this;
    }
}
