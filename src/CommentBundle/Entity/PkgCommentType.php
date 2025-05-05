<?php

namespace ChameleonSystem\CommentBundle\Entity;

use ChameleonSystem\DataAccessBundle\Entity\CoreTableConfiguration\CmsTblConf;

class PkgCommentType
{
    public function __construct(
        private string $id,
        private ?int $cmsident = null,

        // TCMSFieldVarchar
        /** @var string - Name */
        private string $name = '',
        // TCMSFieldVarchar
        /** @var string - Class to be used for pkg_comment */
        private string $pkgCommentClassName = '',
        // TCMSFieldVarchar
        /** @var string - Path to class for pkg_comment */
        private string $pkgCommentClassSubType = '',
        // TCMSFieldExtendedLookup
        /** @var CmsTblConf|null - Table */
        private ?CmsTblConf $cmsTblConf = null,
        // TCMSFieldOption
        /** @var string - Class type for pkg_comment */
        private string $pkgCommentClassType = 'Customer',
        // TCMSFieldVarchar
        /** @var string - Class name */
        private string $className = '',
        // TCMSFieldVarchar
        /** @var string - Class subtype */
        private string $classSubType = 'pkgComment/objects/db/TPkgCommentType',
        // TCMSFieldOption
        /** @var string - Class type */
        private string $classType = 'Core'
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
    public function getPkgCommentClassName(): string
    {
        return $this->pkgCommentClassName;
    }

    public function setPkgCommentClassName(string $pkgCommentClassName): self
    {
        $this->pkgCommentClassName = $pkgCommentClassName;

        return $this;
    }

    // TCMSFieldVarchar
    public function getPkgCommentClassSubType(): string
    {
        return $this->pkgCommentClassSubType;
    }

    public function setPkgCommentClassSubType(string $pkgCommentClassSubType): self
    {
        $this->pkgCommentClassSubType = $pkgCommentClassSubType;

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

    // TCMSFieldOption
    public function getPkgCommentClassType(): string
    {
        return $this->pkgCommentClassType;
    }

    public function setPkgCommentClassType(string $pkgCommentClassType): self
    {
        $this->pkgCommentClassType = $pkgCommentClassType;

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

    // TCMSFieldVarchar
    public function getClassSubType(): string
    {
        return $this->classSubType;
    }

    public function setClassSubType(string $classSubType): self
    {
        $this->classSubType = $classSubType;

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
