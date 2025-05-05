<?php

namespace ChameleonSystem\DataAccessBundle\Entity\Core;

use ChameleonSystem\DataAccessBundle\Entity\CorePortal\CmsPortal;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;

class PkgExternalTracker
{
    public function __construct(
        private string $id,
        private ?int $cmsident = null,

        // TCMSFieldVarchar
        /** @var string - Name */
        private string $name = '',
        // TCMSFieldBoolean
        /** @var bool - Active */
        private bool $active = false,
        // TCMSFieldVarchar
        /** @var string - User / site code */
        private string $identifier = '',
        // TCMSFieldVarchar
        /** @var string - User / site code in DEMO MODE */
        private string $testIdentifier = '',
        // TCMSFieldVarchar
        /** @var string - Class */
        private string $class = '',
        // TCMSFieldVarchar
        /** @var string - Class subtype (path) */
        private string $classSubtype = '',
        // TCMSFieldOption
        /** @var string - Class type */
        private string $classType = 'Customer',
        // TCMSFieldLookupMultiselectCheckboxes
        /** @var Collection<int, CmsPortal> - Portal selection */
        private Collection $cmsPortalCollection = new ArrayCollection()
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

    // TCMSFieldBoolean
    public function isActive(): bool
    {
        return $this->active;
    }

    public function setActive(bool $active): self
    {
        $this->active = $active;

        return $this;
    }

    // TCMSFieldVarchar
    public function getIdentifier(): string
    {
        return $this->identifier;
    }

    public function setIdentifier(string $identifier): self
    {
        $this->identifier = $identifier;

        return $this;
    }

    // TCMSFieldVarchar
    public function getTestIdentifier(): string
    {
        return $this->testIdentifier;
    }

    public function setTestIdentifier(string $testIdentifier): self
    {
        $this->testIdentifier = $testIdentifier;

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
}
