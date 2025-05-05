<?php

namespace ChameleonSystem\MultiModuleBundle\Entity;

use ChameleonSystem\DataAccessBundle\Entity\CorePagedef\CmsTplModuleInstance;
use ChameleonSystem\DataAccessBundle\Entity\CorePagedef\CmsTree;

class PkgMultiModuleSetItem
{
    public function __construct(
        private string $id,
        private ?int $cmsident = null,

        // TCMSFieldVarchar
        /** @var string - Module name */
        private string $name = '',
        // TCMSFieldLookupParentID
        /** @var PkgMultiModuleSet|null - Belongs to set */
        private ?PkgMultiModuleSet $pkgMultiModuleSet = null,
        // TCMSFieldModuleInstance
        /** @var CmsTplModuleInstance|null - Module instance */
        private ?CmsTplModuleInstance $cmsTplModuleInstance = null,
        // TCMSFieldPosition
        /** @var int - Sorting */
        private int $sortOrder = 0,
        // TCMSFieldVarchar
        /** @var string - System name */
        private string $systemName = '',
        // TCMSFieldTreeNode
        /** @var CmsTree|null - Alternative link for tabs */
        private ?CmsTree $alternativeTabUrlForAjax = null
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

    // TCMSFieldLookupParentID
    public function getPkgMultiModuleSet(): ?PkgMultiModuleSet
    {
        return $this->pkgMultiModuleSet;
    }

    public function setPkgMultiModuleSet(?PkgMultiModuleSet $pkgMultiModuleSet): self
    {
        $this->pkgMultiModuleSet = $pkgMultiModuleSet;

        return $this;
    }

    // TCMSFieldModuleInstance
    public function getCmsTplModuleInstance(): ?CmsTplModuleInstance
    {
        return $this->cmsTplModuleInstance;
    }

    public function setCmsTplModuleInstance(?CmsTplModuleInstance $cmsTplModuleInstance): self
    {
        $this->cmsTplModuleInstance = $cmsTplModuleInstance;

        return $this;
    }

    // TCMSFieldPosition
    public function getSortOrder(): int
    {
        return $this->sortOrder;
    }

    public function setSortOrder(int $sortOrder): self
    {
        $this->sortOrder = $sortOrder;

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

    // TCMSFieldTreeNode
    public function getAlternativeTabUrlForAjax(): ?CmsTree
    {
        return $this->alternativeTabUrlForAjax;
    }

    public function setAlternativeTabUrlForAjax(?CmsTree $alternativeTabUrlForAjax): self
    {
        $this->alternativeTabUrlForAjax = $alternativeTabUrlForAjax;

        return $this;
    }
}
