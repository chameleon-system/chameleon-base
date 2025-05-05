<?php

namespace ChameleonSystem\DataAccessBundle\Entity\Core;

use ChameleonSystem\DataAccessBundle\Entity\CorePortal\CmsPortal;
use ChameleonSystem\DataAccessBundle\Entity\CoreTableConfiguration\CmsTblConf;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;

class CmsTplModule
{
    public function __construct(
        private string $id,
        private ?int $cmsident = null,

        // TCMSFieldText
        /** @var string - Description */
        private string $description = '',
        // TCMSFieldVarchar
        /** @var string - Icon font CSS class */
        private string $iconFontCssClass = '',
        // TCMSFieldText
        /** @var string - View / mapper configuration */
        private string $viewMapperConfig = '',
        // TCMSFieldText
        /** @var string - Mapper chain */
        private string $mapperChain = '',
        // TCMSFieldText
        /** @var string - Translations of the views */
        private string $viewMapping = '',
        // TCMSFieldBoolean
        /** @var bool - Enable revision management */
        private bool $revisionManagementActive = false,
        // TCMSFieldBoolean
        /** @var bool - Module contents are copied */
        private bool $isCopyAllowed = false,
        // TCMSFieldBoolean
        /** @var bool - Show in template engine */
        private bool $showInTemplateEngine = true,
        // TCMSFieldPosition
        /** @var int - Position */
        private int $position = 0,
        // TCMSFieldBoolean
        /** @var bool - Offer module to specific groups only */
        private bool $isRestricted = false,
        // TCMSFieldLookupMultiselectCheckboxes
        /** @var Collection<int, CmsUsergroup> - Allow for these groups */
        private Collection $cmsUsergroupCollection = new ArrayCollection(),
        // TCMSFieldLookupMultiselectCheckboxes
        /** @var Collection<int, CmsPortal> - Display in portal */
        private Collection $cmsPortalCollection = new ArrayCollection(),
        // TCMSFieldVarchar
        /** @var string - Module name */
        private string $name = '',
        // TCMSFieldLookupMultiselect
        /** @var Collection<int, CmsTblConf> - Connected tables */
        private Collection $cmsTblConfCollection = new ArrayCollection(),
        // TCMSFieldVarchar
        /** @var string - Class name / service ID */
        private string $classname = ''
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
    public function getIconFontCssClass(): string
    {
        return $this->iconFontCssClass;
    }

    public function setIconFontCssClass(string $iconFontCssClass): self
    {
        $this->iconFontCssClass = $iconFontCssClass;

        return $this;
    }

    // TCMSFieldText
    public function getViewMapperConfig(): string
    {
        return $this->viewMapperConfig;
    }

    public function setViewMapperConfig(string $viewMapperConfig): self
    {
        $this->viewMapperConfig = $viewMapperConfig;

        return $this;
    }

    // TCMSFieldText
    public function getMapperChain(): string
    {
        return $this->mapperChain;
    }

    public function setMapperChain(string $mapperChain): self
    {
        $this->mapperChain = $mapperChain;

        return $this;
    }

    // TCMSFieldText
    public function getViewMapping(): string
    {
        return $this->viewMapping;
    }

    public function setViewMapping(string $viewMapping): self
    {
        $this->viewMapping = $viewMapping;

        return $this;
    }

    // TCMSFieldBoolean
    public function isRevisionManagementActive(): bool
    {
        return $this->revisionManagementActive;
    }

    public function setRevisionManagementActive(bool $revisionManagementActive): self
    {
        $this->revisionManagementActive = $revisionManagementActive;

        return $this;
    }

    // TCMSFieldBoolean
    public function isIsCopyAllowed(): bool
    {
        return $this->isCopyAllowed;
    }

    public function setIsCopyAllowed(bool $isCopyAllowed): self
    {
        $this->isCopyAllowed = $isCopyAllowed;

        return $this;
    }

    // TCMSFieldBoolean
    public function isShowInTemplateEngine(): bool
    {
        return $this->showInTemplateEngine;
    }

    public function setShowInTemplateEngine(bool $showInTemplateEngine): self
    {
        $this->showInTemplateEngine = $showInTemplateEngine;

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

    // TCMSFieldBoolean
    public function isIsRestricted(): bool
    {
        return $this->isRestricted;
    }

    public function setIsRestricted(bool $isRestricted): self
    {
        $this->isRestricted = $isRestricted;

        return $this;
    }

    // TCMSFieldLookupMultiselectCheckboxes

    /**
     * @return Collection<int, CmsUsergroup>
     */
    public function getCmsUsergroupCollection(): Collection
    {
        return $this->cmsUsergroupCollection;
    }

    public function addCmsUsergroupCollection(CmsUsergroup $cmsUsergroupMlt): self
    {
        if (!$this->cmsUsergroupCollection->contains($cmsUsergroupMlt)) {
            $this->cmsUsergroupCollection->add($cmsUsergroupMlt);
            $cmsUsergroupMlt->set($this);
        }

        return $this;
    }

    public function removeCmsUsergroupCollection(CmsUsergroup $cmsUsergroupMlt): self
    {
        if ($this->cmsUsergroupCollection->removeElement($cmsUsergroupMlt)) {
            // set the owning side to null (unless already changed)
            if ($cmsUsergroupMlt->get() === $this) {
                $cmsUsergroupMlt->set(null);
            }
        }

        return $this;
    }

    // TCMSFieldLookupMultiselectCheckboxes

    /**
     * @return Collection<int, CmsPortal>
     */
    public function getCmsPortalCollection(): Collection
    {
        return $this->cmsPortalCollection;
    }

    public function addCmsPortalCollection(CmsPortal $cmsPortalMlt): self
    {
        if (!$this->cmsPortalCollection->contains($cmsPortalMlt)) {
            $this->cmsPortalCollection->add($cmsPortalMlt);
            $cmsPortalMlt->set($this);
        }

        return $this;
    }

    public function removeCmsPortalCollection(CmsPortal $cmsPortalMlt): self
    {
        if ($this->cmsPortalCollection->removeElement($cmsPortalMlt)) {
            // set the owning side to null (unless already changed)
            if ($cmsPortalMlt->get() === $this) {
                $cmsPortalMlt->set(null);
            }
        }

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

    // TCMSFieldLookupMultiselect

    /**
     * @return Collection<int, CmsTblConf>
     */
    public function getCmsTblConfCollection(): Collection
    {
        return $this->cmsTblConfCollection;
    }

    public function addCmsTblConfCollection(CmsTblConf $cmsTblConfMlt): self
    {
        if (!$this->cmsTblConfCollection->contains($cmsTblConfMlt)) {
            $this->cmsTblConfCollection->add($cmsTblConfMlt);
            $cmsTblConfMlt->set($this);
        }

        return $this;
    }

    public function removeCmsTblConfCollection(CmsTblConf $cmsTblConfMlt): self
    {
        if ($this->cmsTblConfCollection->removeElement($cmsTblConfMlt)) {
            // set the owning side to null (unless already changed)
            if ($cmsTblConfMlt->get() === $this) {
                $cmsTblConfMlt->set(null);
            }
        }

        return $this;
    }

    // TCMSFieldVarchar
    public function getClassname(): string
    {
        return $this->classname;
    }

    public function setClassname(string $classname): self
    {
        $this->classname = $classname;

        return $this;
    }
}
