<?php

namespace ChameleonSystem\DataAccessBundle\Entity\CoreMedia;

class CmsDocumentTree
{
    public function __construct(
        private string $id,
        private ?int $cmsident = null,

        // TCMSFieldVarchar
        /** @var string - Category name */
        private string $name = '',
        // TCMSFieldLookup
        /** @var CmsDocumentTree|null - Parent ID */
        private ?CmsDocumentTree $parent = null,
        // TCMSFieldNumber
        /** @var int - Depth */
        private int $depth = 0,
        // TCMSFieldBoolean
        /** @var bool - Hidden? */
        private bool $hidden = false,
        // TCMSFieldNumber
        /** @var int - Sort sequence */
        private int $entrySort = 0
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
    public function getParent(): ?CmsDocumentTree
    {
        return $this->parent;
    }

    public function setParent(?CmsDocumentTree $parent): self
    {
        $this->parent = $parent;

        return $this;
    }

    // TCMSFieldNumber
    public function getDepth(): int
    {
        return $this->depth;
    }

    public function setDepth(int $depth): self
    {
        $this->depth = $depth;

        return $this;
    }

    // TCMSFieldBoolean
    public function isHidden(): bool
    {
        return $this->hidden;
    }

    public function setHidden(bool $hidden): self
    {
        $this->hidden = $hidden;

        return $this;
    }

    // TCMSFieldNumber
    public function getEntrySort(): int
    {
        return $this->entrySort;
    }

    public function setEntrySort(int $entrySort): self
    {
        $this->entrySort = $entrySort;

        return $this;
    }
}
