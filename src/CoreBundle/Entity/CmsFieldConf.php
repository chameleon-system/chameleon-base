<?php
namespace ChameleonSystem\CoreBundle\Entity;

use ChameleonSystem\CoreBundle\Entity\CmsTblConf;
use ChameleonSystem\CoreBundle\Entity\CmsFieldType;
use ChameleonSystem\CoreBundle\Entity\CmsTblFieldTab;

class CmsFieldConf {
  public function __construct(
    private string $id,
    private int|null $cmsident = null,
        
    // TCMSFieldLookup
/** @var CmsTblConf|null - Belongs to Table */
private ?CmsTblConf $cmsTblConf = null
, 
    // TCMSFieldVarchar
/** @var string - Field name */
private string $name = '', 
    // TCMSFieldVarchar
/** @var string - Translation */
private string $translation = '', 
    // TCMSFieldLookup
/** @var CmsFieldType|null - Field type */
private ?CmsFieldType $cmsFieldType = null
, 
    // TCMSFieldLookup
/** @var CmsTblFieldTab|null - Belongs to field-category / tab */
private ?CmsTblFieldTab $cmsTblField_ = null
, 
    // TCMSFieldVarchar
/** @var string - PHP class */
private string $fieldclass = '', 
    // TCMSFieldVarchar
/** @var string - Field extension subtype */
private string $fieldclassSubtype = '', 
    // TCMSFieldVarchar
/** @var string - Default value */
private string $fieldDefaultValue = '', 
    // TCMSFieldVarchar
/** @var string - Input field width */
private string $fieldWidth = '', 
    // TCMSFieldVarchar
/** @var string - Regular expression to validate the field */
private string $validationRegex = ''  ) {}

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


  
    // TCMSFieldLookup
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
public function getCmsTblField_(): ?CmsTblFieldTab
{
    return $this->cmsTblField_;
}

public function setCmsTblField_(?CmsTblFieldTab $cmsTblField_): self
{
    $this->cmsTblField_ = $cmsTblField_;

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


  
    // TCMSFieldVarchar
public function getFieldDefaultValue(): string
{
    return $this->fieldDefaultValue;
}
public function setFieldDefaultValue(string $fieldDefaultValue): self
{
    $this->fieldDefaultValue = $fieldDefaultValue;

    return $this;
}


  
    // TCMSFieldVarchar
public function getFieldWidth(): string
{
    return $this->fieldWidth;
}
public function setFieldWidth(string $fieldWidth): self
{
    $this->fieldWidth = $fieldWidth;

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
