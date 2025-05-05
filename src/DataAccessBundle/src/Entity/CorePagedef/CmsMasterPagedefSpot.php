<?php

namespace ChameleonSystem\DataAccessBundle\Entity\CorePagedef;

use ChameleonSystem\DataAccessBundle\Entity\Core\PkgCmsThemeBlock;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;

class CmsMasterPagedefSpot
{
    public function __construct(
        private string $id,
        private ?int $cmsident = null,

        // TCMSFieldLookupParentID
        /** @var CmsMasterPagedef|null - Belongs to the CMS page template */
        private ?CmsMasterPagedef $cmsMasterPagedef = null,
        // TCMSFieldLookupParentID
        /** @var PkgCmsThemeBlock|null - Belongs to theme block */
        private ?PkgCmsThemeBlock $pkgCmsThemeBlock = null,
        // TCMSFieldVarchar
        /** @var string - Name */
        private string $name = '',
        // TCMSFieldVarchar
        /** @var string - Model (class name) */
        private string $model = '',
        // TCMSFieldVarchar
        /** @var string - Module view */
        private string $view = '',
        // TCMSFieldBoolean
        /** @var bool - Static */
        private bool $static = true,
        // TCMSFieldPropertyTable
        /** @var Collection<int, CmsMasterPagedefSpotParameter> - Parameter */
        private Collection $cmsMasterPagedefSpotParameterCollection = new ArrayCollection(),
        // TCMSFieldPropertyTable
        /** @var Collection<int, CmsMasterPagedefSpotAccess> - Spot restrictions */
        private Collection $cmsMasterPagedefSpotAccessCollection = new ArrayCollection()
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
    public function getCmsMasterPagedef(): ?CmsMasterPagedef
    {
        return $this->cmsMasterPagedef;
    }

    public function setCmsMasterPagedef(?CmsMasterPagedef $cmsMasterPagedef): self
    {
        $this->cmsMasterPagedef = $cmsMasterPagedef;

        return $this;
    }

    // TCMSFieldLookupParentID
    public function getPkgCmsThemeBlock(): ?PkgCmsThemeBlock
    {
        return $this->pkgCmsThemeBlock;
    }

    public function setPkgCmsThemeBlock(?PkgCmsThemeBlock $pkgCmsThemeBlock): self
    {
        $this->pkgCmsThemeBlock = $pkgCmsThemeBlock;

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
    public function getModel(): string
    {
        return $this->model;
    }

    public function setModel(string $model): self
    {
        $this->model = $model;

        return $this;
    }

    // TCMSFieldVarchar
    public function getView(): string
    {
        return $this->view;
    }

    public function setView(string $view): self
    {
        $this->view = $view;

        return $this;
    }

    // TCMSFieldBoolean
    public function isStatic(): bool
    {
        return $this->static;
    }

    public function setStatic(bool $static): self
    {
        $this->static = $static;

        return $this;
    }

    // TCMSFieldPropertyTable

    /**
     * @return Collection<int, CmsMasterPagedefSpotParameter>
     */
    public function getCmsMasterPagedefSpotParameterCollection(): Collection
    {
        return $this->cmsMasterPagedefSpotParameterCollection;
    }

    public function addCmsMasterPagedefSpotParameterCollection(
        CmsMasterPagedefSpotParameter $cmsMasterPagedefSpotParameter
    ): self {
        if (!$this->cmsMasterPagedefSpotParameterCollection->contains($cmsMasterPagedefSpotParameter)) {
            $this->cmsMasterPagedefSpotParameterCollection->add($cmsMasterPagedefSpotParameter);
            $cmsMasterPagedefSpotParameter->setCmsMasterPagedefSpot($this);
        }

        return $this;
    }

    public function removeCmsMasterPagedefSpotParameterCollection(
        CmsMasterPagedefSpotParameter $cmsMasterPagedefSpotParameter
    ): self {
        if ($this->cmsMasterPagedefSpotParameterCollection->removeElement($cmsMasterPagedefSpotParameter)) {
            // set the owning side to null (unless already changed)
            if ($cmsMasterPagedefSpotParameter->getCmsMasterPagedefSpot() === $this) {
                $cmsMasterPagedefSpotParameter->setCmsMasterPagedefSpot(null);
            }
        }

        return $this;
    }

    // TCMSFieldPropertyTable

    /**
     * @return Collection<int, CmsMasterPagedefSpotAccess>
     */
    public function getCmsMasterPagedefSpotAccessCollection(): Collection
    {
        return $this->cmsMasterPagedefSpotAccessCollection;
    }

    public function addCmsMasterPagedefSpotAccessCollection(CmsMasterPagedefSpotAccess $cmsMasterPagedefSpotAccess
    ): self {
        if (!$this->cmsMasterPagedefSpotAccessCollection->contains($cmsMasterPagedefSpotAccess)) {
            $this->cmsMasterPagedefSpotAccessCollection->add($cmsMasterPagedefSpotAccess);
            $cmsMasterPagedefSpotAccess->setCmsMasterPagedefSpot($this);
        }

        return $this;
    }

    public function removeCmsMasterPagedefSpotAccessCollection(CmsMasterPagedefSpotAccess $cmsMasterPagedefSpotAccess
    ): self {
        if ($this->cmsMasterPagedefSpotAccessCollection->removeElement($cmsMasterPagedefSpotAccess)) {
            // set the owning side to null (unless already changed)
            if ($cmsMasterPagedefSpotAccess->getCmsMasterPagedefSpot() === $this) {
                $cmsMasterPagedefSpotAccess->setCmsMasterPagedefSpot(null);
            }
        }

        return $this;
    }
}
