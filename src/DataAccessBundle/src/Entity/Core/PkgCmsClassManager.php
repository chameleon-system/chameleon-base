<?php

namespace ChameleonSystem\DataAccessBundle\Entity\Core;

use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;

class PkgCmsClassManager
{
    public function __construct(
        private string $id,
        private ?int $cmsident = null,

        // TCMSFieldVarchar
        /** @var string - Class names of the virtual entry class */
        private string $nameOfEntryPoint = '',
        // TCMSFieldVarchar
        /** @var string - Terminate inheritance with this class */
        private string $exitClass = '',
        // TCMSFieldVarchar
        /** @var string - End item class: path */
        private string $exitClassSubtype = '',
        // TCMSFieldOption
        /** @var string - End item class: type */
        private string $exitClassType = 'Core',
        // TCMSFieldPropertyTable
        /** @var Collection<int, PkgCmsClassManagerExtension> - Classes administered by the inheritance manager */
        private Collection $pkgCmsClassManagerExtensionCollection = new ArrayCollection()
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
    public function getNameOfEntryPoint(): string
    {
        return $this->nameOfEntryPoint;
    }

    public function setNameOfEntryPoint(string $nameOfEntryPoint): self
    {
        $this->nameOfEntryPoint = $nameOfEntryPoint;

        return $this;
    }

    // TCMSFieldVarchar
    public function getExitClass(): string
    {
        return $this->exitClass;
    }

    public function setExitClass(string $exitClass): self
    {
        $this->exitClass = $exitClass;

        return $this;
    }

    // TCMSFieldVarchar
    public function getExitClassSubtype(): string
    {
        return $this->exitClassSubtype;
    }

    public function setExitClassSubtype(string $exitClassSubtype): self
    {
        $this->exitClassSubtype = $exitClassSubtype;

        return $this;
    }

    // TCMSFieldOption
    public function getExitClassType(): string
    {
        return $this->exitClassType;
    }

    public function setExitClassType(string $exitClassType): self
    {
        $this->exitClassType = $exitClassType;

        return $this;
    }

    // TCMSFieldPropertyTable

    /**
     * @return Collection<int, PkgCmsClassManagerExtension>
     */
    public function getPkgCmsClassManagerExtensionCollection(): Collection
    {
        return $this->pkgCmsClassManagerExtensionCollection;
    }

    public function addPkgCmsClassManagerExtensionCollection(PkgCmsClassManagerExtension $pkgCmsClassManagerExtension
    ): self {
        if (!$this->pkgCmsClassManagerExtensionCollection->contains($pkgCmsClassManagerExtension)) {
            $this->pkgCmsClassManagerExtensionCollection->add($pkgCmsClassManagerExtension);
            $pkgCmsClassManagerExtension->setPkgCmsClassManager($this);
        }

        return $this;
    }

    public function removePkgCmsClassManagerExtensionCollection(PkgCmsClassManagerExtension $pkgCmsClassManagerExtension
    ): self {
        if ($this->pkgCmsClassManagerExtensionCollection->removeElement($pkgCmsClassManagerExtension)) {
            // set the owning side to null (unless already changed)
            if ($pkgCmsClassManagerExtension->getPkgCmsClassManager() === $this) {
                $pkgCmsClassManagerExtension->setPkgCmsClassManager(null);
            }
        }

        return $this;
    }
}
