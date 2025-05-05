<?php

namespace ChameleonSystem\DataAccessBundle\Entity\Core;

use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;

class CmsMigrationCounter
{
    public function __construct(
        private string $id,
        private ?int $cmsident = null,

        // TCMSFieldVarchar
        /** @var string - Name */
        private string $name = '',
        // TCMSFieldPropertyTable
        /** @var Collection<int, CmsMigrationFile> - Update data */
        private Collection $cmsMigrationFileCollection = new ArrayCollection()
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
     * @return Collection<int, CmsMigrationFile>
     */
    public function getCmsMigrationFileCollection(): Collection
    {
        return $this->cmsMigrationFileCollection;
    }

    public function addCmsMigrationFileCollection(CmsMigrationFile $cmsMigrationFile): self
    {
        if (!$this->cmsMigrationFileCollection->contains($cmsMigrationFile)) {
            $this->cmsMigrationFileCollection->add($cmsMigrationFile);
            $cmsMigrationFile->setCmsMigrationCounter($this);
        }

        return $this;
    }

    public function removeCmsMigrationFileCollection(CmsMigrationFile $cmsMigrationFile): self
    {
        if ($this->cmsMigrationFileCollection->removeElement($cmsMigrationFile)) {
            // set the owning side to null (unless already changed)
            if ($cmsMigrationFile->getCmsMigrationCounter() === $this) {
                $cmsMigrationFile->setCmsMigrationCounter(null);
            }
        }

        return $this;
    }
}
