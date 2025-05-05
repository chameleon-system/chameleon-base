<?php

namespace ChameleonSystem\MultiModuleBundle\Entity;

use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;

class PkgMultiModuleSet
{
    public function __construct(
        private string $id,
        private ?int $cmsident = null,

        // TCMSFieldVarchar
        /** @var string - Name of the set */
        private string $name = '',
        // TCMSFieldPropertyTable
        /** @var Collection<int, PkgMultiModuleSetItem> - Set consists of these modules */
        private Collection $pkgMultiModuleSetItemCollection = new ArrayCollection()
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

    // TCMSFieldPropertyTable

    /**
     * @return Collection<int, PkgMultiModuleSetItem>
     */
    public function getPkgMultiModuleSetItemCollection(): Collection
    {
        return $this->pkgMultiModuleSetItemCollection;
    }

    public function addPkgMultiModuleSetItemCollection(PkgMultiModuleSetItem $pkgMultiModuleSetItem): self
    {
        if (!$this->pkgMultiModuleSetItemCollection->contains($pkgMultiModuleSetItem)) {
            $this->pkgMultiModuleSetItemCollection->add($pkgMultiModuleSetItem);
            $pkgMultiModuleSetItem->setPkgMultiModuleSet($this);
        }

        return $this;
    }

    public function removePkgMultiModuleSetItemCollection(PkgMultiModuleSetItem $pkgMultiModuleSetItem): self
    {
        if ($this->pkgMultiModuleSetItemCollection->removeElement($pkgMultiModuleSetItem)) {
            // set the owning side to null (unless already changed)
            if ($pkgMultiModuleSetItem->getPkgMultiModuleSet() === $this) {
                $pkgMultiModuleSetItem->setPkgMultiModuleSet(null);
            }
        }

        return $this;
    }
}
