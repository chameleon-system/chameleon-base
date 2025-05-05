<?php

namespace ChameleonSystem\DataAccessBundle\Entity\CoreModule;

use ChameleonSystem\DataAccessBundle\Entity\CorePagedef\CmsTplModuleInstance;

class CmsWizardStep
{
    public function __construct(
        private string $id,
        private ?int $cmsident = null,

        // TCMSFieldLookupParentID
        /** @var CmsTplModuleInstance|null - Belongs to module instance */
        private ?CmsTplModuleInstance $cmsTplModuleInstance = null,
        // TCMSFieldVarchar
        /** @var string - CMS display name */
        private string $displayName = '',
        // TCMSFieldVarchar
        /** @var string - Title / headline */
        private string $name = '',
        // TCMSFieldWYSIWYG
        /** @var string - Description */
        private string $description = '',
        // TCMSFieldVarchar
        /** @var string - Internal name */
        private string $systemname = '',
        // TCMSFieldPosition
        /** @var int - Position */
        private int $position = 0,
        // TCMSFieldVarchar
        /** @var string - URL name */
        private string $urlName = '',
        // TCMSFieldVarchar
        /** @var string - Class name */
        private string $class = '',
        // TCMSFieldOption
        /** @var string - Class type */
        private string $classType = 'Customer',
        // TCMSFieldVarchar
        /** @var string - Class subtype */
        private string $classSubtype = '',
        // TCMSFieldVarchar
        /** @var string - View to be used for the step */
        private string $renderViewName = '',
        // TCMSFieldOption
        /** @var string - View type */
        private string $renderViewType = 'Customer',
        // TCMSFieldVarchar
        /** @var string - View subtype – where is the view relative to view folder */
        private string $renderViewSubtype = '',
        // TCMSFieldBoolean
        /** @var bool - Classes / views come from a package */
        private bool $isPackage = false
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
    public function getDisplayName(): string
    {
        return $this->displayName;
    }

    public function setDisplayName(string $displayName): self
    {
        $this->displayName = $displayName;

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

    // TCMSFieldVarchar
    public function getSystemname(): string
    {
        return $this->systemname;
    }

    public function setSystemname(string $systemname): self
    {
        $this->systemname = $systemname;

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

    // TCMSFieldVarchar
    public function getUrlName(): string
    {
        return $this->urlName;
    }

    public function setUrlName(string $urlName): self
    {
        $this->urlName = $urlName;

        return $this;
    }

    // TCMSFieldVarchar
    public function getClass(): string
    {
        return $this->class;
    }

    public function setClass(string $class): self
    {
        $this->class = $class;

        return $this;
    }

    // TCMSFieldOption
    public function getClassType(): string
    {
        return $this->classType;
    }

    public function setClassType(string $classType): self
    {
        $this->classType = $classType;

        return $this;
    }

    // TCMSFieldVarchar
    public function getClassSubtype(): string
    {
        return $this->classSubtype;
    }

    public function setClassSubtype(string $classSubtype): self
    {
        $this->classSubtype = $classSubtype;

        return $this;
    }

    // TCMSFieldVarchar
    public function getRenderViewName(): string
    {
        return $this->renderViewName;
    }

    public function setRenderViewName(string $renderViewName): self
    {
        $this->renderViewName = $renderViewName;

        return $this;
    }

    // TCMSFieldOption
    public function getRenderViewType(): string
    {
        return $this->renderViewType;
    }

    public function setRenderViewType(string $renderViewType): self
    {
        $this->renderViewType = $renderViewType;

        return $this;
    }

    // TCMSFieldVarchar
    public function getRenderViewSubtype(): string
    {
        return $this->renderViewSubtype;
    }

    public function setRenderViewSubtype(string $renderViewSubtype): self
    {
        $this->renderViewSubtype = $renderViewSubtype;

        return $this;
    }

    // TCMSFieldBoolean
    public function isIsPackage(): bool
    {
        return $this->isPackage;
    }

    public function setIsPackage(bool $isPackage): self
    {
        $this->isPackage = $isPackage;

        return $this;
    }
}
