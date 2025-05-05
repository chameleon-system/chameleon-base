<?php

namespace ChameleonSystem\DataAccessBundle\Entity\Core;

class PkgCmsClassManagerExtension
{
    public function __construct(
        private string $id,
        private ?int $cmsident = null,

        // TCMSFieldLookupParentID
        /** @var PkgCmsClassManager|null - Belongs to */
        private ?PkgCmsClassManager $pkgCmsClassManager = null,
        // TCMSFieldVarchar
        /** @var string - Class */
        private string $class = '',
        // TCMSFieldVarchar
        /** @var string - Path relative to library/classes */
        private string $classSubtype = '',
        // TCMSFieldOption
        /** @var string - Class type */
        private string $classType = 'Customer',
        // TCMSFieldPosition
        /** @var int - Sorting */
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

    // TCMSFieldLookupParentID
    public function getPkgCmsClassManager(): ?PkgCmsClassManager
    {
        return $this->pkgCmsClassManager;
    }

    public function setPkgCmsClassManager(?PkgCmsClassManager $pkgCmsClassManager): self
    {
        $this->pkgCmsClassManager = $pkgCmsClassManager;

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
