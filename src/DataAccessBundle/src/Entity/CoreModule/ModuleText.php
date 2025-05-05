<?php

namespace ChameleonSystem\DataAccessBundle\Entity\CoreModule;

use ChameleonSystem\DataAccessBundle\Entity\CoreMedia\CmsDocument;
use ChameleonSystem\DataAccessBundle\Entity\CoreMedia\CmsMedia;
use ChameleonSystem\DataAccessBundle\Entity\CorePagedef\CmsTplModuleInstance;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;

class ModuleText
{
    public function __construct(
        private string $id,
        private ?int $cmsident = null,

        // TCMSFieldLookup
        /** @var CmsTplModuleInstance|null - Module instance */
        private ?CmsTplModuleInstance $cmsTplModuleInstance = null,
        // TCMSFieldVarchar
        /** @var string - Headline */
        private string $name = '',
        // TCMSFieldVarchar
        /** @var string - Sub headline */
        private string $subheadline = '',
        // TCMSFieldExtendedLookupMedia
        /** @var CmsMedia|null - Optional icon */
        private ?CmsMedia $icon = null,
        // TCMSFieldWYSIWYG
        /** @var string - Content */
        private string $content = '',
        // TCMSFieldDownloads
        /** @var Collection<int, CmsDocument> - Download files */
        private Collection $dataPoolCollection = new ArrayCollection()
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
    public function getSubheadline(): string
    {
        return $this->subheadline;
    }

    public function setSubheadline(string $subheadline): self
    {
        $this->subheadline = $subheadline;

        return $this;
    }

    // TCMSFieldExtendedLookupMedia
    public function getIcon(): ?CmsMedia
    {
        return $this->icon;
    }

    public function setIcon(?CmsMedia $icon): self
    {
        $this->icon = $icon;

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
}
