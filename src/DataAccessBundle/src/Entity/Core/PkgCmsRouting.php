<?php

namespace ChameleonSystem\DataAccessBundle\Entity\Core;

use ChameleonSystem\DataAccessBundle\Entity\CorePortal\CmsPortal;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;

class PkgCmsRouting
{
    public function __construct(
        private string $id,
        private ?int $cmsident = null,

        // TCMSFieldVarcharUnique
        /** @var string - System name */
        private string $name = '',
        // TCMSFieldVarchar
        /** @var string - Brief description */
        private string $shortDescription = '',
        // TCMSFieldOption
        /** @var string - Type of resource */
        private string $type = 'yaml',
        // TCMSFieldVarchar
        /** @var string - Resource */
        private string $resource = '',
        // TCMSFieldPosition
        /** @var int - Position */
        private int $position = 0,
        // TCMSFieldVarchar
        /** @var string - System page */
        private string $systemPageName = '',
        // TCMSFieldLookupMultiselectCheckboxes
        /** @var Collection<int, CmsPortal> - Restrict to the following portals */
        private Collection $cmsPortalCollection = new ArrayCollection(),
        // TCMSFieldBoolean
        /** @var bool - Active */
        private bool $active = true
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

    // TCMSFieldVarcharUnique
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
    public function getShortDescription(): string
    {
        return $this->shortDescription;
    }

    public function setShortDescription(string $shortDescription): self
    {
        $this->shortDescription = $shortDescription;

        return $this;
    }

    // TCMSFieldOption
    public function getType(): string
    {
        return $this->type;
    }

    public function setType(string $type): self
    {
        $this->type = $type;

        return $this;
    }

    // TCMSFieldVarchar
    public function getResource(): string
    {
        return $this->resource;
    }

    public function setResource(string $resource): self
    {
        $this->resource = $resource;

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
    public function getSystemPageName(): string
    {
        return $this->systemPageName;
    }

    public function setSystemPageName(string $systemPageName): self
    {
        $this->systemPageName = $systemPageName;

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
}
