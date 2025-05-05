<?php

namespace ChameleonSystem\DataAccessBundle\Entity\Core;

use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;

class CmsInterfaceManager
{
    public function __construct(
        private string $id,
        private ?int $cmsident = null,

        // TCMSFieldVarchar
        /** @var string - Name */
        private string $name = '',
        // TCMSFieldVarchar
        /** @var string - System name */
        private string $systemname = '',
        // TCMSFieldVarchar
        /** @var string - Used class */
        private string $class = '',
        // TCMSFieldOption
        /** @var string - Class type */
        private string $classType = 'Core',
        // TCMSFieldVarchar
        /** @var string - Class subtype */
        private string $classSubtype = '',
        // TCMSFieldText
        /** @var string - Description */
        private string $description = '',
        // TCMSFieldPropertyTable
        /** @var Collection<int, CmsInterfaceManagerParameter> - Parameter */
        private Collection $cmsInterfaceManagerParameterCollection = new ArrayCollection(),
        // TCMSFieldBoolean
        /** @var bool - Restrict to user groups */
        private bool $restrictToUserGroups = false,
        // TCMSFieldLookupMultiselectCheckboxes
        /** @var Collection<int, CmsUsergroup> - Available for the following groups */
        private Collection $cmsUsergroupCollection = new ArrayCollection()
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
    public function getSystemname(): string
    {
        return $this->systemname;
    }

    public function setSystemname(string $systemname): self
    {
        $this->systemname = $systemname;

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

    // TCMSFieldPropertyTable

    /**
     * @return Collection<int, CmsInterfaceManagerParameter>
     */
    public function getCmsInterfaceManagerParameterCollection(): Collection
    {
        return $this->cmsInterfaceManagerParameterCollection;
    }

    public function addCmsInterfaceManagerParameterCollection(CmsInterfaceManagerParameter $cmsInterfaceManagerParameter
    ): self {
        if (!$this->cmsInterfaceManagerParameterCollection->contains($cmsInterfaceManagerParameter)) {
            $this->cmsInterfaceManagerParameterCollection->add($cmsInterfaceManagerParameter);
            $cmsInterfaceManagerParameter->setCmsInterfaceManager($this);
        }

        return $this;
    }

    public function removeCmsInterfaceManagerParameterCollection(
        CmsInterfaceManagerParameter $cmsInterfaceManagerParameter
    ): self {
        if ($this->cmsInterfaceManagerParameterCollection->removeElement($cmsInterfaceManagerParameter)) {
            // set the owning side to null (unless already changed)
            if ($cmsInterfaceManagerParameter->getCmsInterfaceManager() === $this) {
                $cmsInterfaceManagerParameter->setCmsInterfaceManager(null);
            }
        }

        return $this;
    }

    // TCMSFieldBoolean
    public function isRestrictToUserGroups(): bool
    {
        return $this->restrictToUserGroups;
    }

    public function setRestrictToUserGroups(bool $restrictToUserGroups): self
    {
        $this->restrictToUserGroups = $restrictToUserGroups;

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
}
