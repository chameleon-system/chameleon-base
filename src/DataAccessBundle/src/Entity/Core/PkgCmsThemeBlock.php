<?php

namespace ChameleonSystem\DataAccessBundle\Entity\Core;

use ChameleonSystem\DataAccessBundle\Entity\CoreMedia\CmsMedia;
use ChameleonSystem\DataAccessBundle\Entity\CorePagedef\CmsMasterPagedefSpot;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;

class PkgCmsThemeBlock
{
    public function __construct(
        private string $id,
        private ?int $cmsident = null,

        // TCMSFieldVarchar
        /** @var string - Descriptive name */
        private string $name = '',
        // TCMSFieldVarchar
        /** @var string - System name */
        private string $systemName = '',
        // TCMSFieldPropertyTable
        /** @var Collection<int, CmsMasterPagedefSpot> - Spots */
        private Collection $cmsMasterPagedefSpotCollection = new ArrayCollection(),
        // TCMSFieldPropertyTable
        /** @var Collection<int, PkgCmsThemeBlockLayout> - Layouts */
        private Collection $pkgCmsThemeBlockLayoutCollection = new ArrayCollection(),
        // TCMSFieldLookup
        /** @var PkgCmsThemeBlockLayout|null - Default layout */
        private ?PkgCmsThemeBlockLayout $pkgCmsThemeBlockLayout = null,
        // TCMSFieldExtendedLookupMedia
        /** @var CmsMedia|null - Preview image */
        private ?CmsMedia $cmsMedia = null
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
            $cmsMasterPagedefSpot->setPkgCmsThemeBlock($this);
        }

        return $this;
    }

    public function removeCmsMasterPagedefSpotCollection(CmsMasterPagedefSpot $cmsMasterPagedefSpot): self
    {
        if ($this->cmsMasterPagedefSpotCollection->removeElement($cmsMasterPagedefSpot)) {
            // set the owning side to null (unless already changed)
            if ($cmsMasterPagedefSpot->getPkgCmsThemeBlock() === $this) {
                $cmsMasterPagedefSpot->setPkgCmsThemeBlock(null);
            }
        }

        return $this;
    }

    // TCMSFieldPropertyTable

    /**
     * @return Collection<int, PkgCmsThemeBlockLayout>
     */
    public function getPkgCmsThemeBlockLayoutCollection(): Collection
    {
        return $this->pkgCmsThemeBlockLayoutCollection;
    }

    public function addPkgCmsThemeBlockLayoutCollection(PkgCmsThemeBlockLayout $pkgCmsThemeBlockLayout): self
    {
        if (!$this->pkgCmsThemeBlockLayoutCollection->contains($pkgCmsThemeBlockLayout)) {
            $this->pkgCmsThemeBlockLayoutCollection->add($pkgCmsThemeBlockLayout);
            $pkgCmsThemeBlockLayout->setPkgCmsThemeBlock($this);
        }

        return $this;
    }

    public function removePkgCmsThemeBlockLayoutCollection(PkgCmsThemeBlockLayout $pkgCmsThemeBlockLayout): self
    {
        if ($this->pkgCmsThemeBlockLayoutCollection->removeElement($pkgCmsThemeBlockLayout)) {
            // set the owning side to null (unless already changed)
            if ($pkgCmsThemeBlockLayout->getPkgCmsThemeBlock() === $this) {
                $pkgCmsThemeBlockLayout->setPkgCmsThemeBlock(null);
            }
        }

        return $this;
    }

    // TCMSFieldLookup
    public function getPkgCmsThemeBlockLayout(): ?PkgCmsThemeBlockLayout
    {
        return $this->pkgCmsThemeBlockLayout;
    }

    public function setPkgCmsThemeBlockLayout(?PkgCmsThemeBlockLayout $pkgCmsThemeBlockLayout): self
    {
        $this->pkgCmsThemeBlockLayout = $pkgCmsThemeBlockLayout;

        return $this;
    }

    // TCMSFieldExtendedLookupMedia
    public function getCmsMedia(): ?CmsMedia
    {
        return $this->cmsMedia;
    }

    public function setCmsMedia(?CmsMedia $cmsMedia): self
    {
        $this->cmsMedia = $cmsMedia;

        return $this;
    }
}
