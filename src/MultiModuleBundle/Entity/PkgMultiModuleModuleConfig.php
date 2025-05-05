<?php

namespace ChameleonSystem\MultiModuleBundle\Entity;

use ChameleonSystem\DataAccessBundle\Entity\CorePagedef\CmsTplModuleInstance;

class PkgMultiModuleModuleConfig
{
    public function __construct(
        private string $id,
        private ?int $cmsident = null,

        // TCMSFieldLookupParentID
        /** @var CmsTplModuleInstance|null - Belongs to module instance */
        private ?CmsTplModuleInstance $cmsTplModuleInstance = null,
        // TCMSFieldExtendedLookup
        /** @var PkgMultiModuleSet|null - Multimodule set */
        private ?PkgMultiModuleSet $pkgMultiModuleSet = null
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

    // TCMSFieldExtendedLookup
    public function getPkgMultiModuleSet(): ?PkgMultiModuleSet
    {
        return $this->pkgMultiModuleSet;
    }

    public function setPkgMultiModuleSet(?PkgMultiModuleSet $pkgMultiModuleSet): self
    {
        $this->pkgMultiModuleSet = $pkgMultiModuleSet;

        return $this;
    }
}
