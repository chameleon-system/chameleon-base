<?php
namespace ChameleonSystem\CoreBundle\Entity;

use ChameleonSystem\CoreBundle\Entity\CmsPortal;
use ChameleonSystem\CoreBundle\Entity\CmsUser;

class CmsUrlAlias {
  public function __construct(
    private string $id,
    private int|null $cmsident = null,
        
    // TCMSFieldLookup
/** @var CmsPortal|null - Belongs to portal */
private ?CmsPortal $cmsPortal = null
, 
    // TCMSFieldVarchar
/** @var string - Name / notes */
private string $name = '', 
    // TCMSFieldVarchar
/** @var string - Source */
private string $sourceUrl = '', 
    // TCMSFieldVarchar
/** @var string - Target */
private string $targetUrl = '', 
    // TCMSFieldLookup
/** @var CmsUser|null - Created by */
private ?CmsUser $cmsUser = null
  ) {}

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
public function getSourceUrl(): string
{
    return $this->sourceUrl;
}
public function setSourceUrl(string $sourceUrl): self
{
    $this->sourceUrl = $sourceUrl;

    return $this;
}


  
    // TCMSFieldVarchar
public function getTargetUrl(): string
{
    return $this->targetUrl;
}
public function setTargetUrl(string $targetUrl): self
{
    $this->targetUrl = $targetUrl;

    return $this;
}


  
    // TCMSFieldLookup
public function getCmsUser(): ?CmsUser
{
    return $this->cmsUser;
}

public function setCmsUser(?CmsUser $cmsUser): self
{
    $this->cmsUser = $cmsUser;

    return $this;
}


  
}
