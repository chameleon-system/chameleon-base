<?php

namespace ChameleonSystem\DataAccessBundle\Entity\Core;

use ChameleonSystem\DataAccessBundle\Entity\CorePortal\CmsPortal;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;

class PkgCmsTextBlock
{
    public function __construct(
        private string $id,
        private ?int $cmsident = null,

        // TCMSFieldLookupMultiselectCheckboxes
        /** @var Collection<int, CmsPortal> - Available in selected portals */
        private Collection $cmsPortalCollection = new ArrayCollection(),
        // TCMSFieldVarchar
        /** @var string - System name */
        private string $systemname = '',
        // TCMSFieldVarchar
        /** @var string - Name / title */
        private string $name = '',
        // TCMSFieldWYSIWYG
        /** @var string - Text */
        private string $content = ''
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
    public function getName(): string
    {
        return $this->name;
    }

    public function setName(string $name): self
    {
        $this->name = $name;

        return $this;
    }

    // TCMSFieldWYSIWYG
    public function getContent(): string
    {
        return $this->content;
    }

    public function setContent(string $content): self
    {
        $this->content = $content;

        return $this;
    }
}
