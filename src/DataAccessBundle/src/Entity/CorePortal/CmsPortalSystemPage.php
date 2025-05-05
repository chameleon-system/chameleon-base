<?php

namespace ChameleonSystem\DataAccessBundle\Entity\CorePortal;

use ChameleonSystem\DataAccessBundle\Entity\CorePagedef\CmsTree;

class CmsPortalSystemPage
{
    public function __construct(
        private string $id,
        private ?int $cmsident = null,

        // TCMSFieldLookupParentID
        /** @var CmsPortal|null - Belongs to portal */
        private ?CmsPortal $cmsPortal = null,
        // TCMSFieldVarchar
        /** @var string - Name */
        private string $name = '',
        // TCMSFieldTreeNode
        /** @var CmsTree|null - Page */
        private ?CmsTree $cmsTree = null,
        // TCMSFieldVarchar
        /** @var string - System name */
        private string $nameInternal = ''
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
    public function getCmsPortal(): ?CmsPortal
    {
        return $this->cmsPortal;
    }

    public function setCmsPortal(?CmsPortal $cmsPortal): self
    {
        $this->cmsPortal = $cmsPortal;

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

    // TCMSFieldTreeNode
    public function getCmsTree(): ?CmsTree
    {
        return $this->cmsTree;
    }

    public function setCmsTree(?CmsTree $cmsTree): self
    {
        $this->cmsTree = $cmsTree;

        return $this;
    }

    // TCMSFieldVarchar
    public function getNameInternal(): string
    {
        return $this->nameInternal;
    }

    public function setNameInternal(string $nameInternal): self
    {
        $this->nameInternal = $nameInternal;

        return $this;
    }
}
