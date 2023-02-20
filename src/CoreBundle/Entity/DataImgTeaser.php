<?php
namespace ChameleonSystem\CoreBundle\Entity;

use ChameleonSystem\CoreBundle\Entity\CmsTplModuleInstance;

class DataImgTeaser {
  public function __construct(
    private string $id,
    private int|null $cmsident = null,
        
    // TCMSFieldLookupParentID
/** @var CmsTplModuleInstance|null - Belongs to module instance */
private ?CmsTplModuleInstance $cmsTplModuleInstance = null
, 
    // TCMSFieldVarchar
/** @var string - Title */
private string $name = '', 
    // TCMSFieldVarchar
/** @var string - ALT text */
private string $altText = '', 
    // TCMSFieldVarchar
/** @var string - Link */
private string $link = ''  ) {}

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
public function getCmsTplModuleInstance(): ?CmsTplModuleInstance
{
    return $this->cmsTplModuleInstance;
}

public function setCmsTplModuleInstance(?CmsTplModuleInstance $cmsTplModuleInstance): self
{
    $this->cmsTplModuleInstance = $cmsTplModuleInstance;

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
public function getAltText(): string
{
    return $this->altText;
}
public function setAltText(string $altText): self
{
    $this->altText = $altText;

    return $this;
}


  
    // TCMSFieldVarchar
public function getLink(): string
{
    return $this->link;
}
public function setLink(string $link): self
{
    $this->link = $link;

    return $this;
}


  
}
