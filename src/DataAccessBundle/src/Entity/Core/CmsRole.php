<?php

namespace ChameleonSystem\DataAccessBundle\Entity\Core;

use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;

class CmsRole
{
    public function __construct(
        private string $id,
        private ?int $cmsident = null,

        // TCMSFieldLookupMultiselectCheckboxes
        /** @var Collection<int, CmsRole> - Is subordinate role of */
        private Collection $cmsRoleCollection = new ArrayCollection(),
        // TCMSFieldBoolean
        /** @var bool - Is selectable */
        private bool $isChooseable = true,
        // TCMSFieldVarchar
        /** @var string - CMS role abbreviation */
        private string $name = '',
        // TCMSFieldLookupMultiselectCheckboxes
        /** @var Collection<int, CmsRight> - CMS user rights */
        private Collection $cmsRightCollection = new ArrayCollection(),
        // TCMSFieldBoolean
        /** @var bool - Required by the system */
        private bool $isSystem = false,
        // TCMSFieldVarchar
        /** @var string - German translation */
        private string $trans = ''
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
    // TCMSFieldLookupMultiselectCheckboxes

    /**
     * @return Collection<int, CmsRole>
     */
    public function getCmsRoleCollection(): Collection
    {
        return $this->cmsRoleCollection;
    }

    public function addCmsRoleCollection(CmsRole $cmsRoleMlt): self
    {
        if (!$this->cmsRoleCollection->contains($cmsRoleMlt)) {
            $this->cmsRoleCollection->add($cmsRoleMlt);
            $cmsRoleMlt->set($this);
        }

        return $this;
    }

    public function removeCmsRoleCollection(CmsRole $cmsRoleMlt): self
    {
        if ($this->cmsRoleCollection->removeElement($cmsRoleMlt)) {
            // set the owning side to null (unless already changed)
            if ($cmsRoleMlt->get() === $this) {
                $cmsRoleMlt->set(null);
            }
        }

        return $this;
    }

    // TCMSFieldBoolean
    public function isIsChooseable(): bool
    {
        return $this->isChooseable;
    }

    public function setIsChooseable(bool $isChooseable): self
    {
        $this->isChooseable = $isChooseable;

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

    // TCMSFieldLookupMultiselectCheckboxes

    /**
     * @return Collection<int, CmsRight>
     */
    public function getCmsRightCollection(): Collection
    {
        return $this->cmsRightCollection;
    }

    public function addCmsRightCollection(CmsRight $cmsRightMlt): self
    {
        if (!$this->cmsRightCollection->contains($cmsRightMlt)) {
            $this->cmsRightCollection->add($cmsRightMlt);
            $cmsRightMlt->set($this);
        }

        return $this;
    }

    public function removeCmsRightCollection(CmsRight $cmsRightMlt): self
    {
        if ($this->cmsRightCollection->removeElement($cmsRightMlt)) {
            // set the owning side to null (unless already changed)
            if ($cmsRightMlt->get() === $this) {
                $cmsRightMlt->set(null);
            }
        }

        return $this;
    }

    // TCMSFieldBoolean
    public function isIsSystem(): bool
    {
        return $this->isSystem;
    }

    public function setIsSystem(bool $isSystem): self
    {
        $this->isSystem = $isSystem;

        return $this;
    }

    // TCMSFieldVarchar
    public function getTrans(): string
    {
        return $this->trans;
    }

    public function setTrans(string $trans): self
    {
        $this->trans = $trans;

        return $this;
    }
}
