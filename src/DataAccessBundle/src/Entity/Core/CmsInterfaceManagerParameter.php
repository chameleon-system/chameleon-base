<?php

namespace ChameleonSystem\DataAccessBundle\Entity\Core;

class CmsInterfaceManagerParameter
{
    public function __construct(
        private string $id,
        private ?int $cmsident = null,

        // TCMSFieldLookupParentID
        /** @var CmsInterfaceManager|null - Belongs to interface */
        private ?CmsInterfaceManager $cmsInterfaceManager = null,
        // TCMSFieldText
        /** @var string - Description */
        private string $description = '',
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
    public function getCmsInterfaceManager(): ?CmsInterfaceManager
    {
        return $this->cmsInterfaceManager;
    }

    public function setCmsInterfaceManager(?CmsInterfaceManager $cmsInterfaceManager): self
    {
        $this->cmsInterfaceManager = $cmsInterfaceManager;

        return $this;
    }

    // TCMSFieldText
    public function getDescription(): string
    {
        return $this->description;
    }

    public function setDescription(string $description): self
    {
        $this->description = $description;

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
