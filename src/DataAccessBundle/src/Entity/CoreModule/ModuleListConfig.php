<?php

namespace ChameleonSystem\DataAccessBundle\Entity\CoreModule;

use ChameleonSystem\DataAccessBundle\Entity\CorePagedef\CmsTplModuleInstance;
use ChameleonSystem\DataAccessBundle\Entity\CorePagedef\CmsTree;

class ModuleListConfig
{
    public function __construct(
        private string $id,
        private ?int $cmsident = null,

        // TCMSFieldTreeNode
        /** @var CmsTree|null - Teaser target page */
        private ?CmsTree $targetPage = null,
        // TCMSFieldTablefieldname
        /** @var string - Sort list by */
        private string $moduleListCmsfieldname = '',
        // TCMSFieldOption
        /** @var string - Order direction */
        private string $sortOrderDirection = '',
        // TCMSFieldLookupParentID
        /** @var CmsTplModuleInstance|null - Belongs to module instance */
        private ?CmsTplModuleInstance $cmsTplModuleInstance = null,
        // TCMSFieldVarchar
        /** @var string - Title */
        private string $name = '',
        // TCMSFieldVarchar
        /** @var string - Theme */
        private string $subHeadline = '',
        // TCMSFieldWYSIWYG
        /** @var string - Text */
        private string $description = ''
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

    // TCMSFieldTreeNode
    public function getTargetPage(): ?CmsTree
    {
        return $this->targetPage;
    }

    public function setTargetPage(?CmsTree $targetPage): self
    {
        $this->targetPage = $targetPage;

        return $this;
    }

    // TCMSFieldTablefieldname
    public function getModuleListCmsfieldname(): string
    {
        return $this->moduleListCmsfieldname;
    }

    public function setModuleListCmsfieldname(string $moduleListCmsfieldname): self
    {
        $this->moduleListCmsfieldname = $moduleListCmsfieldname;

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

    // TCMSFieldVarchar
    public function getSubHeadline(): string
    {
        return $this->subHeadline;
    }

    public function setSubHeadline(string $subHeadline): self
    {
        $this->subHeadline = $subHeadline;

        return $this;
    }

    // TCMSFieldWYSIWYG
    public function getDescription(): string
    {
        return $this->description;
    }

    public function setDescription(string $description): self
    {
        $this->description = $description;

        return $this;
    }
}
