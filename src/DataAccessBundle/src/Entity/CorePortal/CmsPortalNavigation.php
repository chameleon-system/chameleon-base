<?php

namespace ChameleonSystem\DataAccessBundle\Entity\CorePortal;

use ChameleonSystem\DataAccessBundle\Entity\CorePagedef\CmsTree;

class CmsPortalNavigation
{
    public function __construct(
        private string $id,
        private ?int $cmsident = null,

        // TCMSFieldLookupParentID
        /** @var CmsPortal|null - Belongs to portal */
        private ?CmsPortal $cmsPortal = null,
        // TCMSFieldVarchar
        /** @var string - Navigation title */
        private string $name = 'neue Navigation',
        // TCMSFieldNavigationTreeNode
        /** @var CmsTree|null - Start node in navigation tree */
        private ?CmsTree $treeNode = null
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

    // TCMSFieldNavigationTreeNode
    public function getTreeNode(): ?CmsTree
    {
        return $this->treeNode;
    }

    public function setTreeNode(?CmsTree $treeNode): self
    {
        $this->treeNode = $treeNode;

        return $this;
    }
}
