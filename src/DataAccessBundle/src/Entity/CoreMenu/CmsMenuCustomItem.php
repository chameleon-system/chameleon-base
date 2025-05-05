<?php

namespace ChameleonSystem\DataAccessBundle\Entity\CoreMenu;

use ChameleonSystem\DataAccessBundle\Entity\Core\CmsRight;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;

class CmsMenuCustomItem
{
    public function __construct(
        private string $id,
        private ?int $cmsident = null,

        // TCMSFieldVarchar
        /** @var string - Name */
        private string $name = '',
        // TCMSFieldVarchar
        /** @var string - Target URL */
        private string $url = '',
        // TCMSFieldLookupMultiselectCheckboxes
        /** @var Collection<int, CmsRight> - Required rights */
        private Collection $cmsRightCollection = new ArrayCollection()
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
    public function getUrl(): string
    {
        return $this->url;
    }

    public function setUrl(string $url): self
    {
        $this->url = $url;

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
}
