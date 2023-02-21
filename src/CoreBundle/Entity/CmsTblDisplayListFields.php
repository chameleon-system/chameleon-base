<?php
namespace ChameleonSystem\CoreBundle\Entity;

use ChameleonSystem\CoreBundle\Entity\CmsTblConf;

class CmsTblDisplayListFields {
  public function __construct(
    private string $id,
    private int|null $cmsident = null,
        
    // TCMSFieldVarchar
/** @var string - Field name */
private string $title = '', 
    // TCMSFieldLookup
/** @var CmsTblConf|null - Belongs to table */
private ?CmsTblConf $cmsTblConf = null
, 
    // TCMSFieldVarchar
/** @var string - Database field name */
private string $name = '', 
    // TCMSFieldVarchar
/** @var string - Database field name of translation */
private string $cmsTranslationFieldName = '', 
    // TCMSFieldVarchar
/** @var string - Field alias (abbreviated) */
private string $dbAlias = '', 
    // TCMSFieldVarchar
/** @var string - Column width */
private string $width = '-1', 
    // TCMSFieldVarchar
/** @var string - Call back function */
private string $callbackFnc = ''  ) {}

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
public function getTitle(): string
{
    return $this->title;
}
public function setTitle(string $title): self
{
    $this->title = $title;

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
public function getCmsTranslationFieldName(): string
{
    return $this->cmsTranslationFieldName;
}
public function setCmsTranslationFieldName(string $cmsTranslationFieldName): self
{
    $this->cmsTranslationFieldName = $cmsTranslationFieldName;

    return $this;
}


  
    // TCMSFieldVarchar
public function getDbAlias(): string
{
    return $this->dbAlias;
}
public function setDbAlias(string $dbAlias): self
{
    $this->dbAlias = $dbAlias;

    return $this;
}


  
    // TCMSFieldVarchar
public function getWidth(): string
{
    return $this->width;
}
public function setWidth(string $width): self
{
    $this->width = $width;

    return $this;
}


  
    // TCMSFieldVarchar
public function getCallbackFnc(): string
{
    return $this->callbackFnc;
}
public function setCallbackFnc(string $callbackFnc): self
{
    $this->callbackFnc = $callbackFnc;

    return $this;
}


  
}
