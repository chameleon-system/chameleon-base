<?php

namespace ChameleonSystem\DataAccessBundle\Entity\CorePagedef;

use ChameleonSystem\DataAccessBundle\Entity\Core\PkgCmsThemeBlock;
use ChameleonSystem\DataAccessBundle\Entity\CorePortal\CmsPortal;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;

class CmsMasterPagedef
{
    public function __construct(
        private string $id,
        private ?int $cmsident = null,

        // TCMSFieldVarchar
        /** @var string - Name */
        private string $name = '',
        // TCMSFieldText
        /** @var string - Description */
        private string $description = '',
        // TCMSFieldVarchar
        /** @var string - Layout */
        private string $layout = '',
        // TCMSFieldPropertyTable
        /** @var Collection<int, CmsMasterPagedefSpot> - Spots */
        private Collection $cmsMasterPagedefSpotCollection = new ArrayCollection(),
        // TCMSFieldLookupMultiselect
        /** @var Collection<int, PkgCmsThemeBlock> - Theme blocks */
        private Collection $pkgCmsThemeBlockCollection = new ArrayCollection(),
        // TCMSFieldText
        /** @var string - Action-Plugins */
        private string $actionPluginList = '',
        // TCMSFieldBoolean
        /** @var bool - Restrict to certain portals only */
        private bool $restrictToPortals = false,
        // TCMSFieldLookupMultiselectCheckboxes
        /** @var Collection<int, CmsPortal> - CMS module extension */
        private Collection $cmsPortalCollection = new ArrayCollection(),
        // TCMSFieldVarchar
        /** @var string - WYSIWYG CSS URL */
        private string $wysiwygCssUrl = '',
        // TCMSFieldPosition
        /** @var int - */
        private int $position = 0
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
    public function getLayout(): string
    {
        return $this->layout;
    }

    public function setLayout(string $layout): self
    {
        $this->layout = $layout;

        return $this;
    }

    // TCMSFieldPropertyTable

    /**
     * @return Collection<int, CmsMasterPagedefSpot>
     */
    public function getCmsMasterPagedefSpotCollection(): Collection
    {
        return $this->cmsMasterPagedefSpotCollection;
    }

    public function addCmsMasterPagedefSpotCollection(CmsMasterPagedefSpot $cmsMasterPagedefSpot): self
    {
        if (!$this->cmsMasterPagedefSpotCollection->contains($cmsMasterPagedefSpot)) {
            $this->cmsMasterPagedefSpotCollection->add($cmsMasterPagedefSpot);
            $cmsMasterPagedefSpot->setCmsMasterPagedef($this);
        }

        return $this;
    }

    public function removeCmsMasterPagedefSpotCollection(CmsMasterPagedefSpot $cmsMasterPagedefSpot): self
    {
        if ($this->cmsMasterPagedefSpotCollection->removeElement($cmsMasterPagedefSpot)) {
            // set the owning side to null (unless already changed)
            if ($cmsMasterPagedefSpot->getCmsMasterPagedef() === $this) {
                $cmsMasterPagedefSpot->setCmsMasterPagedef(null);
            }
        }

        return $this;
    }

    // TCMSFieldLookupMultiselect

    /**
     * @return Collection<int, PkgCmsThemeBlock>
     */
    public function getPkgCmsThemeBlockCollection(): Collection
    {
        return $this->pkgCmsThemeBlockCollection;
    }

    public function addPkgCmsThemeBlockCollection(PkgCmsThemeBlock $pkgCmsThemeBlockMlt): self
    {
        if (!$this->pkgCmsThemeBlockCollection->contains($pkgCmsThemeBlockMlt)) {
            $this->pkgCmsThemeBlockCollection->add($pkgCmsThemeBlockMlt);
            $pkgCmsThemeBlockMlt->set($this);
        }

        return $this;
    }

    public function removePkgCmsThemeBlockCollection(PkgCmsThemeBlock $pkgCmsThemeBlockMlt): self
    {
        if ($this->pkgCmsThemeBlockCollection->removeElement($pkgCmsThemeBlockMlt)) {
            // set the owning side to null (unless already changed)
            if ($pkgCmsThemeBlockMlt->get() === $this) {
                $pkgCmsThemeBlockMlt->set(null);
            }
        }

        return $this;
    }

    // TCMSFieldText
    public function getActionPluginList(): string
    {
        return $this->actionPluginList;
    }

    public function setActionPluginList(string $actionPluginList): self
    {
        $this->actionPluginList = $actionPluginList;

        return $this;
    }

    // TCMSFieldBoolean
    public function isRestrictToPortals(): bool
    {
        return $this->restrictToPortals;
    }

    public function setRestrictToPortals(bool $restrictToPortals): self
    {
        $this->restrictToPortals = $restrictToPortals;

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
    public function getWysiwygCssUrl(): string
    {
        return $this->wysiwygCssUrl;
    }

    public function setWysiwygCssUrl(string $wysiwygCssUrl): self
    {
        $this->wysiwygCssUrl = $wysiwygCssUrl;

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
}
