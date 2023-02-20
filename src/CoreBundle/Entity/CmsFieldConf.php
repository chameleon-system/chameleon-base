<?php
namespace ChameleonSystem\CoreBundle\Entity;

class CmsFieldConf {
  public function __construct(
    private string|null $id = null,
    private int|null $cmsident = null,
          
    // TCMSFieldLookup
/** @var \ChameleonSystem\CoreBundle\Entity\CmsTblConf|null - Belongs to Table */
private \ChameleonSystem\CoreBundle\Entity\CmsTblConf|null $cmsTblConf = null,
/** @var null|string - Belongs to Table */
private ?string $cmsTblConfId = null
,   
    // TCMSFieldLookup
/** @var \ChameleonSystem\CoreBundle\Entity\CmsFieldType|null - Field type */
private \ChameleonSystem\CoreBundle\Entity\CmsFieldType|null $cmsFieldType = null,
/** @var null|string - Field type */
private ?string $cmsFieldTypeId = null
,   
    // TCMSFieldLookup
/** @var \ChameleonSystem\CoreBundle\Entity\CmsTblFieldTab|null - Belongs to field-category / tab */
private \ChameleonSystem\CoreBundle\Entity\CmsTblFieldTab|null $cmsTblFieldTab = null,
/** @var null|string - Belongs to field-category / tab */
private ?string $cmsTblFieldTabId = null
, 
    // TCMSFieldVarchar
/** @var string - Field name */
private string $name = '', 
    // TCMSFieldVarchar
/** @var string - Translation */
private string $translation = '', 
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
/** @var \ChameleonSystem\CoreBundle\Entity\CmsUsergroup[] - Allowed user groups */
private \Doctrine\Common\Collections\Collection $cmsUsergroupMlt = new \Doctrine\Common\Collections\ArrayCollection(), 
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
private string $validationRegex = ''  ) {}

  public function getId(): ?string
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
public function getCmsTblConf(): \ChameleonSystem\CoreBundle\Entity\CmsTblConf|null
{
    return $this->cmsTblConf;
}
public function setCmsTblConf(\ChameleonSystem\CoreBundle\Entity\CmsTblConf|null $cmsTblConf): self
{
    $this->cmsTblConf = $cmsTblConf;
    $this->cmsTblConfId = $cmsTblConf?->getId();

    return $this;
}
public function getCmsTblConfId(): ?string
{
    return $this->cmsTblConfId;
}
public function setCmsTblConfId(?string $cmsTblConfId): self
{
    $this->cmsTblConfId = $cmsTblConfId;
    // todo - load new id
    //$this->cmsTblConfId = $?->getId();

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


  
    // TCMSFieldLookup
public function getCmsFieldType(): \ChameleonSystem\CoreBundle\Entity\CmsFieldType|null
{
    return $this->cmsFieldType;
}
public function setCmsFieldType(\ChameleonSystem\CoreBundle\Entity\CmsFieldType|null $cmsFieldType): self
{
    $this->cmsFieldType = $cmsFieldType;
    $this->cmsFieldTypeId = $cmsFieldType?->getId();

    return $this;
}
public function getCmsFieldTypeId(): ?string
{
    return $this->cmsFieldTypeId;
}
public function setCmsFieldTypeId(?string $cmsFieldTypeId): self
{
    $this->cmsFieldTypeId = $cmsFieldTypeId;
    // todo - load new id
    //$this->cmsFieldTypeId = $?->getId();

    return $this;
}



  
    // TCMSFieldLookup
public function getCmsTblFieldTab(): \ChameleonSystem\CoreBundle\Entity\CmsTblFieldTab|null
{
    return $this->cmsTblFieldTab;
}
public function setCmsTblFieldTab(\ChameleonSystem\CoreBundle\Entity\CmsTblFieldTab|null $cmsTblFieldTab): self
{
    $this->cmsTblFieldTab = $cmsTblFieldTab;
    $this->cmsTblFieldTabId = $cmsTblFieldTab?->getId();

    return $this;
}
public function getCmsTblFieldTabId(): ?string
{
    return $this->cmsTblFieldTabId;
}
public function setCmsTblFieldTabId(?string $cmsTblFieldTabId): self
{
    $this->cmsTblFieldTabId = $cmsTblFieldTabId;
    // todo - load new id
    //$this->cmsTblFieldTabId = $?->getId();

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
public function getCmsUsergroupMlt(): \Doctrine\Common\Collections\Collection
{
    return $this->cmsUsergroupMlt;
}
public function setCmsUsergroupMlt(\Doctrine\Common\Collections\Collection $cmsUsergroupMlt): self
{
    $this->cmsUsergroupMlt = $cmsUsergroupMlt;

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
