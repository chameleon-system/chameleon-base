<?php

namespace ChameleonSystem\DataAccessBundle\Entity\CoreTableConfiguration;

use ChameleonSystem\DataAccessBundle\Entity\Core\CmsUsergroup;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;

class CmsFieldConf
{
    public function __construct(
        private string $id,
        private ?int $cmsident = null,

        // TCMSFieldLookupParentID
        /** @var CmsTblConf|null - Belongs to Table */
        private ?CmsTblConf $cmsTblConf = null,
        // TCMSFieldVarchar
        /** @var string - Field name */
        private string $name = '',
        // TCMSFieldVarchar
        /** @var string - Translation */
        private string $translation = '',
        // TCMSFieldLookupFieldTypes
        /** @var CmsFieldType|null - Field type */
        private ?CmsFieldType $cmsFieldType = null,
        // TCMSFieldLookup
        /** @var CmsTblFieldTab|null - Belongs to field-category / tab */
        private ?CmsTblFieldTab $cmsTblFieldTab = null,
        // TCMSFieldBoolean
        /** @var bool - Mandatory field */
        private bool $isrequired = false,
        // TCMSFieldVarchar
        /** @var string - PHP class */
        private string $fieldclass = '',
        // TCMSFieldVarchar
        /** @var string - Field extension subtype */
        private string $fieldclassSubtype = '',
        // TCMSFieldOption
        /** @var string - PHP class type */
        private string $classType = 'Core',
        // TCMSFieldOption
        /** @var string - Display mode */
        private string $modifier = 'none',
        // TCMSTextFieldVarcharDefaultValue
        /** @var string - Default value */
        private string $fieldDefaultValue = '',
        // TCMSFieldText
        /** @var string - Field length, value list */
        private string $lengthSet = '',
        // TCMSFieldText
        /** @var string - Field type configuration */
        private string $fieldtypeConfig = '',
        // TCMSFieldBoolean
        /** @var bool - Restrict field access */
        private bool $restrictToGroups = false,
        // TCMSFieldLookupMultiselectCheckboxes
        /** @var Collection<int, CmsUsergroup> - Allowed user groups */
        private Collection $cmsUsergroupCollection = new ArrayCollection(),
        // TCMSFieldNumber
        /** @var int - Input field width */
        private int $fieldWidth = 0,
        // TCMSFieldPosition
        /** @var int - Position */
        private int $position = 0,
        // TCMSFieldText
        /** @var string - Help text */
        private string $helptext = '',
        // TCMSFieldColorpicker
        /** @var string - Line color */
        private string $rowHexcolor = '',
        // TCMSFieldBoolean
        /** @var bool - Multilanguage field (relevant when field-based translations are active) */
        private bool $isTranslatable = false,
        // TCMSFieldVarchar
        /** @var string - Regular expression to validate the field */
        private string $validationRegex = ''
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
    public function getCmsTblConf(): ?CmsTblConf
    {
        return $this->cmsTblConf;
    }

    public function setCmsTblConf(?CmsTblConf $cmsTblConf): self
    {
        $this->cmsTblConf = $cmsTblConf;

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
    public function getTranslation(): string
    {
        return $this->translation;
    }

    public function setTranslation(string $translation): self
    {
        $this->translation = $translation;

        return $this;
    }

    // TCMSFieldLookupFieldTypes
    public function getCmsFieldType(): ?CmsFieldType
    {
        return $this->cmsFieldType;
    }

    public function setCmsFieldType(?CmsFieldType $cmsFieldType): self
    {
        $this->cmsFieldType = $cmsFieldType;

        return $this;
    }

    // TCMSFieldLookup
    public function getCmsTblFieldTab(): ?CmsTblFieldTab
    {
        return $this->cmsTblFieldTab;
    }

    public function setCmsTblFieldTab(?CmsTblFieldTab $cmsTblFieldTab): self
    {
        $this->cmsTblFieldTab = $cmsTblFieldTab;

        return $this;
    }

    // TCMSFieldBoolean
    public function isIsrequired(): bool
    {
        return $this->isrequired;
    }

    public function setIsrequired(bool $isrequired): self
    {
        $this->isrequired = $isrequired;

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

    // TCMSFieldVarchar
    public function getFieldclassSubtype(): string
    {
        return $this->fieldclassSubtype;
    }

    public function setFieldclassSubtype(string $fieldclassSubtype): self
    {
        $this->fieldclassSubtype = $fieldclassSubtype;

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

    // TCMSFieldOption
    public function getModifier(): string
    {
        return $this->modifier;
    }

    public function setModifier(string $modifier): self
    {
        $this->modifier = $modifier;

        return $this;
    }

    // TCMSTextFieldVarcharDefaultValue
    public function getFieldDefaultValue(): string
    {
        return $this->fieldDefaultValue;
    }

    public function setFieldDefaultValue(string $fieldDefaultValue): self
    {
        $this->fieldDefaultValue = $fieldDefaultValue;

        return $this;
    }

    // TCMSFieldText
    public function getLengthSet(): string
    {
        return $this->lengthSet;
    }

    public function setLengthSet(string $lengthSet): self
    {
        $this->lengthSet = $lengthSet;

        return $this;
    }

    // TCMSFieldText
    public function getFieldtypeConfig(): string
    {
        return $this->fieldtypeConfig;
    }

    public function setFieldtypeConfig(string $fieldtypeConfig): self
    {
        $this->fieldtypeConfig = $fieldtypeConfig;

        return $this;
    }

    // TCMSFieldBoolean
    public function isRestrictToGroups(): bool
    {
        return $this->restrictToGroups;
    }

    public function setRestrictToGroups(bool $restrictToGroups): self
    {
        $this->restrictToGroups = $restrictToGroups;

        return $this;
    }

    // TCMSFieldLookupMultiselectCheckboxes

    /**
     * @return Collection<int, CmsUsergroup>
     */
    public function getCmsUsergroupCollection(): Collection
    {
        return $this->cmsUsergroupCollection;
    }

    public function addCmsUsergroupCollection(CmsUsergroup $cmsUsergroupMlt): self
    {
        if (!$this->cmsUsergroupCollection->contains($cmsUsergroupMlt)) {
            $this->cmsUsergroupCollection->add($cmsUsergroupMlt);
            $cmsUsergroupMlt->set($this);
        }

        return $this;
    }

    public function removeCmsUsergroupCollection(CmsUsergroup $cmsUsergroupMlt): self
    {
        if ($this->cmsUsergroupCollection->removeElement($cmsUsergroupMlt)) {
            // set the owning side to null (unless already changed)
            if ($cmsUsergroupMlt->get() === $this) {
                $cmsUsergroupMlt->set(null);
            }
        }

        return $this;
    }

    // TCMSFieldNumber
    public function getFieldWidth(): int
    {
        return $this->fieldWidth;
    }

    public function setFieldWidth(int $fieldWidth): self
    {
        $this->fieldWidth = $fieldWidth;

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

    // TCMSFieldText
    public function getHelptext(): string
    {
        return $this->helptext;
    }

    public function setHelptext(string $helptext): self
    {
        $this->helptext = $helptext;

        return $this;
    }

    // TCMSFieldColorpicker
    public function getRowHexcolor(): string
    {
        return $this->rowHexcolor;
    }

    public function setRowHexcolor(string $rowHexcolor): self
    {
        $this->rowHexcolor = $rowHexcolor;

        return $this;
    }

    // TCMSFieldBoolean
    public function isIsTranslatable(): bool
    {
        return $this->isTranslatable;
    }

    public function setIsTranslatable(bool $isTranslatable): self
    {
        $this->isTranslatable = $isTranslatable;

        return $this;
    }

    // TCMSFieldVarchar
    public function getValidationRegex(): string
    {
        return $this->validationRegex;
    }

    public function setValidationRegex(string $validationRegex): self
    {
        $this->validationRegex = $validationRegex;

        return $this;
    }
}
