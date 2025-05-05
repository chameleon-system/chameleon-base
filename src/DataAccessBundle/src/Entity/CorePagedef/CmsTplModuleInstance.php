<?php

namespace ChameleonSystem\DataAccessBundle\Entity\CorePagedef;

use ChameleonSystem\DataAccessBundle\Entity\Core\CmsTplModule;
use ChameleonSystem\DataAccessBundle\Entity\CorePortal\CmsPortal;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;

class CmsTplModuleInstance
{
    public function __construct(
        private string $id,
        private ?int $cmsident = null,

        // TCMSFieldVarchar
        /** @var string - Instance name */
        private string $name = '',
        // TCMSFieldPropertyTable
        /** @var Collection<int, CmsTplPageCmsMasterPagedefSpot> - CMS pages dynamic spots */
        private Collection $cmsTplPageCmsMasterPagedefSpotCollection = new ArrayCollection(),
        // TCMSFieldLookup
        /** @var CmsPortal|null - was created in portal */
        private ?CmsPortal $cmsPortal = null,
        // TCMSFieldVarchar
        /** @var string - File name of the module template */
        private string $template = '',
        // TCMSFieldLookup
        /** @var CmsTplModule|null - Module ID */
        private ?CmsTplModule $cmsTplModule = null
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
     * @return Collection<int, CmsTplPageCmsMasterPagedefSpot>
     */
    public function getCmsTplPageCmsMasterPagedefSpotCollection(): Collection
    {
        return $this->cmsTplPageCmsMasterPagedefSpotCollection;
    }

    public function addCmsTplPageCmsMasterPagedefSpotCollection(
        CmsTplPageCmsMasterPagedefSpot $cmsTplPageCmsMasterPagedefSpot
    ): self {
        if (!$this->cmsTplPageCmsMasterPagedefSpotCollection->contains($cmsTplPageCmsMasterPagedefSpot)) {
            $this->cmsTplPageCmsMasterPagedefSpotCollection->add($cmsTplPageCmsMasterPagedefSpot);
            $cmsTplPageCmsMasterPagedefSpot->setCmsTplModuleInstance($this);
        }

        return $this;
    }

    public function removeCmsTplPageCmsMasterPagedefSpotCollection(
        CmsTplPageCmsMasterPagedefSpot $cmsTplPageCmsMasterPagedefSpot
    ): self {
        if ($this->cmsTplPageCmsMasterPagedefSpotCollection->removeElement($cmsTplPageCmsMasterPagedefSpot)) {
            // set the owning side to null (unless already changed)
            if ($cmsTplPageCmsMasterPagedefSpot->getCmsTplModuleInstance() === $this) {
                $cmsTplPageCmsMasterPagedefSpot->setCmsTplModuleInstance(null);
            }
        }

        return $this;
    }

    // TCMSFieldLookup
    public function getCmsPortal(): ?CmsPortal
    {
        return $this->cmsPortal;
    }

    public function setCmsPortal(?CmsPortal $cmsPortal): self
    {
        $this->cmsPortal = $cmsPortal;

        return $this;
    }

    // TCMSFieldVarchar
    public function getTemplate(): string
    {
        return $this->template;
    }

    public function setTemplate(string $template): self
    {
        $this->template = $template;

        return $this;
    }

    // TCMSFieldLookup
    public function getCmsTplModule(): ?CmsTplModule
    {
        return $this->cmsTplModule;
    }

    public function setCmsTplModule(?CmsTplModule $cmsTplModule): self
    {
        $this->cmsTplModule = $cmsTplModule;

        return $this;
    }
}
