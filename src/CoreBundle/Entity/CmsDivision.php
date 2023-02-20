<?php
namespace ChameleonSystem\CoreBundle\Entity;

use ChameleonSystem\CoreBundle\Entity\CmsPortal;

class CmsDivision {
  public function __construct(
    private string $id,
    private int|null $cmsident = null,
        
    // TCMSFieldLookupParentID
/** @var CmsPortal|null - Belongs to portal / website */
private ?CmsPortal $cmsPortal = null
, 
    // TCMSFieldVarchar
/** @var string - Area name */
private string $name = '', 
    // TCMSFieldVarchar
/** @var string - IVW code */
private string $ivwCode = '', 
    // TCMSFieldVarchar
/** @var string - Stop hover menu at this level */
private string $menuStopLevel = '0'  ) {}

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
public function getCmsPortal(): ?CmsPortal
{
    return $this->cmsPortal;
}

public function setCmsPortal(?CmsPortal $cmsPortal): self
{
    $this->cmsPortal = $cmsPortal;

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
public function getIvwCode(): string
{
    return $this->ivwCode;
}
public function setIvwCode(string $ivwCode): self
{
    $this->ivwCode = $ivwCode;

    return $this;
}


  
    // TCMSFieldVarchar
public function getMenuStopLevel(): string
{
    return $this->menuStopLevel;
}
public function setMenuStopLevel(string $menuStopLevel): self
{
    $this->menuStopLevel = $menuStopLevel;

    return $this;
}


  
}
