<?php

namespace ChameleonSystem\DataAccessBundle\Entity\CoreTableConfiguration;

class CmsFieldType
{
    public function __construct(
        private string $id,
        private ?int $cmsident = null,

        // TCMSFieldVarchar
        /** @var string - Name */
        private string $trans = '',
        // TCMSFieldBoolean
        /** @var bool - Auto increment */
        private bool $forceAutoIncrement = false,
        // TCMSFieldVarchar
        /** @var string - PHP class subtype */
        private string $classSubtype = '',
        // TCMSFieldVarcharUnique
        /** @var string - Field code name */
        private string $constname = '',
        // TCMSFieldVarchar
        /** @var string - MySQL data type */
        private string $mysqlType = '',
        // TCMSFieldVarchar
        /** @var string - MySQL field length or value list (ENUM) */
        private string $lengthSet = '',
        // TCMSFieldOption
        /** @var string - Base type */
        private string $baseType = 'standard',
        // TCMSFieldWYSIWYG
        /** @var string - Help text */
        private string $helpText = '',
        // TCMSFieldVarchar
        /** @var string - Default value */
        private string $mysqlStandardValue = '',
        // TCMSFieldVarchar
        /** @var string - PHP class */
        private string $fieldclass = '',
        // TCMSFieldOption
        /** @var string - PHP class type */
        private string $classType = 'Core',
        // TCMSFieldBoolean
        /** @var bool - Field contains images */
        private bool $containsImages = false,
        // TCMSFieldOption
        /** @var string - Field index */
        private string $indextype = 'none'
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
    public function getTrans(): string
    {
        return $this->trans;
    }

    public function setTrans(string $trans): self
    {
        $this->trans = $trans;

        return $this;
    }

    // TCMSFieldBoolean
    public function isForceAutoIncrement(): bool
    {
        return $this->forceAutoIncrement;
    }

    public function setForceAutoIncrement(bool $forceAutoIncrement): self
    {
        $this->forceAutoIncrement = $forceAutoIncrement;

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

    // TCMSFieldVarcharUnique
    public function getConstname(): string
    {
        return $this->constname;
    }

    public function setConstname(string $constname): self
    {
        $this->constname = $constname;

        return $this;
    }

    // TCMSFieldVarchar
    public function getMysqlType(): string
    {
        return $this->mysqlType;
    }

    public function setMysqlType(string $mysqlType): self
    {
        $this->mysqlType = $mysqlType;

        return $this;
    }

    // TCMSFieldVarchar
    public function getLengthSet(): string
    {
        return $this->lengthSet;
    }

    public function setLengthSet(string $lengthSet): self
    {
        $this->lengthSet = $lengthSet;

        return $this;
    }

    // TCMSFieldOption
    public function getBaseType(): string
    {
        return $this->baseType;
    }

    public function setBaseType(string $baseType): self
    {
        $this->baseType = $baseType;

        return $this;
    }

    // TCMSFieldWYSIWYG
    public function getHelpText(): string
    {
        return $this->helpText;
    }

    public function setHelpText(string $helpText): self
    {
        $this->helpText = $helpText;

        return $this;
    }

    // TCMSFieldVarchar
    public function getMysqlStandardValue(): string
    {
        return $this->mysqlStandardValue;
    }

    public function setMysqlStandardValue(string $mysqlStandardValue): self
    {
        $this->mysqlStandardValue = $mysqlStandardValue;

        return $this;
    }

    // TCMSFieldVarchar
    public function getFieldclass(): string
    {
        return $this->fieldclass;
    }

    public function setFieldclass(string $fieldclass): self
    {
        $this->fieldclass = $fieldclass;

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

    // TCMSFieldBoolean
    public function isContainsImages(): bool
    {
        return $this->containsImages;
    }

    public function setContainsImages(bool $containsImages): self
    {
        $this->containsImages = $containsImages;

        return $this;
    }

    // TCMSFieldOption
    public function getIndextype(): string
    {
        return $this->indextype;
    }

    public function setIndextype(string $indextype): self
    {
        $this->indextype = $indextype;

        return $this;
    }
}
