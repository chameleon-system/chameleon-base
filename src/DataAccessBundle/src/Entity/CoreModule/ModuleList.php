<?php

namespace ChameleonSystem\DataAccessBundle\Entity\CoreModule;

use ChameleonSystem\DataAccessBundle\Entity\CoreMedia\CmsDocument;
use ChameleonSystem\DataAccessBundle\Entity\CorePagedef\CmsTplModuleInstance;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;

class ModuleList
{
    public function __construct(
        private string $id,
        private ?int $cmsident = null,

        // TCMSFieldLookup
        /** @var CmsTplModuleInstance|null - Belongs to module */
        private ?CmsTplModuleInstance $cmsTplModuleInstance = null,
        // TCMSFieldVarchar
        /** @var string - Title */
        private string $name = '',
        // TCMSFieldVarchar
        /** @var string - Sub headline */
        private string $subHeadline = '',
        // TCMSFieldDateToday
        /** @var \DateTime|null - Date */
        private ?\DateTime $dateToday = null,
        // TCMSFieldLookup
        /** @var ModuleListCat|null - Category */
        private ?ModuleListCat $moduleListCat = null,
        // TCMSFieldWYSIWYG
        /** @var string - Introduction */
        private string $teaserText = '',
        // TCMSFieldWYSIWYG
        /** @var string - Description */
        private string $description = '',
        // TCMSFieldDownloads
        /** @var Collection<int, CmsDocument> - Document pool */
        private Collection $dataPoolCollection = new ArrayCollection(),
        // TCMSFieldPosition
        /** @var int - Position */
        private int $position = 0
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

    // TCMSFieldLookup
    public function getCmsTplModuleInstance(): ?CmsTplModuleInstance
    {
        return $this->cmsTplModuleInstance;
    }

    public function setCmsTplModuleInstance(?CmsTplModuleInstance $cmsTplModuleInstance): self
    {
        $this->cmsTplModuleInstance = $cmsTplModuleInstance;

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
    public function getSubHeadline(): string
    {
        return $this->subHeadline;
    }

    public function setSubHeadline(string $subHeadline): self
    {
        $this->subHeadline = $subHeadline;

        return $this;
    }

    // TCMSFieldDateToday
    public function getDateToday(): ?\DateTime
    {
        return $this->dateToday;
    }

    public function setDateToday(?\DateTime $dateToday): self
    {
        $this->dateToday = $dateToday;

        return $this;
    }

    // TCMSFieldLookup
    public function getModuleListCat(): ?ModuleListCat
    {
        return $this->moduleListCat;
    }

    public function setModuleListCat(?ModuleListCat $moduleListCat): self
    {
        $this->moduleListCat = $moduleListCat;

        return $this;
    }

    // TCMSFieldWYSIWYG
    public function getTeaserText(): string
    {
        return $this->teaserText;
    }

    public function setTeaserText(string $teaserText): self
    {
        $this->teaserText = $teaserText;

        return $this;
    }

    // TCMSFieldWYSIWYG
    public function getDescription(): string
    {
        return $this->description;
    }

    public function setDescription(string $description): self
    {
        $this->description = $description;

        return $this;
    }

    // TCMSFieldDownloads

    /**
     * @return Collection<int, CmsDocument>
     */
    public function getDataPoolCollection(): Collection
    {
        return $this->dataPoolCollection;
    }

    public function addDataPoolCollection(CmsDocument $dataPool): self
    {
        if (!$this->dataPoolCollection->contains($dataPool)) {
            $this->dataPoolCollection->add($dataPool);
            $dataPool->set($this);
        }

        return $this;
    }

    public function removeDataPoolCollection(CmsDocument $dataPool): self
    {
        if ($this->dataPoolCollection->removeElement($dataPool)) {
            // set the owning side to null (unless already changed)
            if ($dataPool->get() === $this) {
                $dataPool->set(null);
            }
        }

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
}
