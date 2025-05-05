<?php

namespace ChameleonSystem\DataAccessBundle\Entity\CoreModule;

use ChameleonSystem\DataAccessBundle\Entity\CorePagedef\CmsTplModuleInstance;

class CmsWizardConfig
{
    public function __construct(
        private string $id,
        private ?int $cmsident = null,

        // TCMSFieldLookupParentID
        /** @var CmsTplModuleInstance|null - Belongs to module instance */
        private ?CmsTplModuleInstance $cmsTplModuleInstance = null,
        // TCMSFieldVarchar
        /** @var string - Title / headline */
        private string $name = '',
        // TCMSFieldBoolean
        /** @var bool - Specifies whether the list of steps is in a package */
        private bool $listIsPackage = false
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
    public function getCmsTplModuleInstance(): ?CmsTplModuleInstance
    {
        return $this->cmsTplModuleInstance;
    }

    public function setCmsTplModuleInstance(?CmsTplModuleInstance $cmsTplModuleInstance): self
    {
        $this->cmsTplModuleInstance = $cmsTplModuleInstance;

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

    // TCMSFieldBoolean
    public function isListIsPackage(): bool
    {
        return $this->listIsPackage;
    }

    public function setListIsPackage(bool $listIsPackage): self
    {
        $this->listIsPackage = $listIsPackage;

        return $this;
    }
}
