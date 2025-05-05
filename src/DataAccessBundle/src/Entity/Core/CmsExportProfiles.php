<?php

namespace ChameleonSystem\DataAccessBundle\Entity\Core;

use ChameleonSystem\DataAccessBundle\Entity\CorePortal\CmsPortal;
use ChameleonSystem\DataAccessBundle\Entity\CoreTableConfiguration\CmsTblConf;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;

class CmsExportProfiles
{
    public function __construct(
        private string $id,
        private ?int $cmsident = null,

        // TCMSFieldVarchar
        /** @var string - Profile name */
        private string $name = '',
        // TCMSFieldLookup
        /** @var CmsPortal|null - Editorial department */
        private ?CmsPortal $cmsPortal = null,
        // TCMSFieldOption
        /** @var string - Export format */
        private string $exportType = 'TABs',
        // TCMSFieldLookup
        /** @var CmsTblConf|null - Table */
        private ?CmsTblConf $cmsTblConf = null,
        // TCMSFieldPropertyTable
        /** @var Collection<int, CmsExportProfilesFields> - Fields to be exported */
        private Collection $cmsExportProfilesFieldsCollection = new ArrayCollection()
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

    // TCMSFieldOption
    public function getExportType(): string
    {
        return $this->exportType;
    }

    public function setExportType(string $exportType): self
    {
        $this->exportType = $exportType;

        return $this;
    }

    // TCMSFieldLookup
    public function getCmsTblConf(): ?CmsTblConf
    {
        return $this->cmsTblConf;
    }

    public function setCmsTblConf(?CmsTblConf $cmsTblConf): self
    {
        $this->cmsTblConf = $cmsTblConf;

        return $this;
    }

    // TCMSFieldPropertyTable

    /**
     * @return Collection<int, CmsExportProfilesFields>
     */
    public function getCmsExportProfilesFieldsCollection(): Collection
    {
        return $this->cmsExportProfilesFieldsCollection;
    }

    public function addCmsExportProfilesFieldsCollection(CmsExportProfilesFields $cmsExportProfilesFields): self
    {
        if (!$this->cmsExportProfilesFieldsCollection->contains($cmsExportProfilesFields)) {
            $this->cmsExportProfilesFieldsCollection->add($cmsExportProfilesFields);
            $cmsExportProfilesFields->setCmsExportProfiles($this);
        }

        return $this;
    }

    public function removeCmsExportProfilesFieldsCollection(CmsExportProfilesFields $cmsExportProfilesFields): self
    {
        if ($this->cmsExportProfilesFieldsCollection->removeElement($cmsExportProfilesFields)) {
            // set the owning side to null (unless already changed)
            if ($cmsExportProfilesFields->getCmsExportProfiles() === $this) {
                $cmsExportProfilesFields->setCmsExportProfiles(null);
            }
        }

        return $this;
    }
}
