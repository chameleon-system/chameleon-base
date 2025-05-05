<?php

namespace ChameleonSystem\DataAccessBundle\Entity\Core;

use ChameleonSystem\DataAccessBundle\Entity\CoreTableConfiguration\CmsTblConf;

class PkgGenericTableExport
{
    public function __construct(
        private string $id,
        private ?int $cmsident = null,

        // TCMSFieldVarchar
        /** @var string - Name of the profile */
        private string $name = '',
        // TCMSFieldVarchar
        /** @var string - System name */
        private string $systemName = '',
        // TCMSFieldText
        /** @var string - Query */
        private string $restriction = '',
        // TCMSFieldVarchar
        /** @var string - Template to be used (twig) */
        private string $view = '',
        // TCMSFieldVarchar
        /** @var string - Header template to be used (twig) */
        private string $headerView = '',
        // TCMSFieldVarchar
        /** @var string - Path to template */
        private string $viewPath = '',
        // TCMSFieldVarchar
        /** @var string - File name for export */
        private string $exportFilename = '',
        // TCMSFieldExtendedLookup
        /** @var CmsTblConf|null - Source table */
        private ?CmsTblConf $cmsTblConf = null,
        // TCMSFieldText
        /** @var string - Mapper configuration */
        private string $mapperConfig = '',
        // TCMSFieldVarchar
        /** @var string - Class */
        private string $class = '',
        // TCMSFieldVarchar
        /** @var string - Class path */
        private string $classSubtype = '',
        // TCMSFieldOption
        /** @var string - Class type */
        private string $classType = 'Customer'
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
    public function getSystemName(): string
    {
        return $this->systemName;
    }

    public function setSystemName(string $systemName): self
    {
        $this->systemName = $systemName;

        return $this;
    }

    // TCMSFieldText
    public function getRestriction(): string
    {
        return $this->restriction;
    }

    public function setRestriction(string $restriction): self
    {
        $this->restriction = $restriction;

        return $this;
    }

    // TCMSFieldVarchar
    public function getView(): string
    {
        return $this->view;
    }

    public function setView(string $view): self
    {
        $this->view = $view;

        return $this;
    }

    // TCMSFieldVarchar
    public function getHeaderView(): string
    {
        return $this->headerView;
    }

    public function setHeaderView(string $headerView): self
    {
        $this->headerView = $headerView;

        return $this;
    }

    // TCMSFieldVarchar
    public function getViewPath(): string
    {
        return $this->viewPath;
    }

    public function setViewPath(string $viewPath): self
    {
        $this->viewPath = $viewPath;

        return $this;
    }

    // TCMSFieldVarchar
    public function getExportFilename(): string
    {
        return $this->exportFilename;
    }

    public function setExportFilename(string $exportFilename): self
    {
        $this->exportFilename = $exportFilename;

        return $this;
    }

    // TCMSFieldExtendedLookup
    public function getCmsTblConf(): ?CmsTblConf
    {
        return $this->cmsTblConf;
    }

    public function setCmsTblConf(?CmsTblConf $cmsTblConf): self
    {
        $this->cmsTblConf = $cmsTblConf;

        return $this;
    }

    // TCMSFieldText
    public function getMapperConfig(): string
    {
        return $this->mapperConfig;
    }

    public function setMapperConfig(string $mapperConfig): self
    {
        $this->mapperConfig = $mapperConfig;

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
}
