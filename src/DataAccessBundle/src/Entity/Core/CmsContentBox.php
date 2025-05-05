<?php

namespace ChameleonSystem\DataAccessBundle\Entity\Core;

class CmsContentBox
{
    public function __construct(
        private string $id,
        private ?int $cmsident = null,

        // TCMSFieldVarchar
        /** @var string - Widget class */
        private string $className = '',
        // TCMSFieldOption
        /** @var string - Widget class type */
        private string $classType = 'Core',
        // TCMSFieldVarchar
        /** @var string - Widget class subfolder */
        private string $classPath = 'Core',
        // TCMSFieldVarchar
        /** @var string - System name */
        private string $systemName = '',
        // TCMSFieldVarchar
        /** @var string - Name */
        private string $name = '',
        // TCMSFieldColorpicker
        /** @var string - Headline color */
        private string $headlinecolHexcolor = '9CBBDE',
        // TCMSFieldOption
        /** @var string - Display in column */
        private string $showInCol = 'left'
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
    public function getClassName(): string
    {
        return $this->className;
    }

    public function setClassName(string $className): self
    {
        $this->className = $className;

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

    // TCMSFieldVarchar
    public function getClassPath(): string
    {
        return $this->classPath;
    }

    public function setClassPath(string $classPath): self
    {
        $this->classPath = $classPath;

        return $this;
    }

    // TCMSFieldVarchar
    public function getSystemName(): string
    {
        return $this->systemName;
    }

    public function setSystemName(string $systemName): self
    {
        $this->systemName = $systemName;

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

    // TCMSFieldColorpicker
    public function getHeadlinecolHexcolor(): string
    {
        return $this->headlinecolHexcolor;
    }

    public function setHeadlinecolHexcolor(string $headlinecolHexcolor): self
    {
        $this->headlinecolHexcolor = $headlinecolHexcolor;

        return $this;
    }

    // TCMSFieldOption
    public function getShowInCol(): string
    {
        return $this->showInCol;
    }

    public function setShowInCol(string $showInCol): self
    {
        $this->showInCol = $showInCol;

        return $this;
    }
}
