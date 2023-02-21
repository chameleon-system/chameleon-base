<?php
namespace ChameleonSystem\CoreBundle\Entity;

use ChameleonSystem\CoreBundle\Entity\CmsMessageManagerMessageType;
use ChameleonSystem\CoreBundle\Entity\CmsConfig;

class CmsMessageManagerBackendMessage {
  public function __construct(
    private string $id,
    private int|null $cmsident = null,
        
    // TCMSFieldLookup
/** @var CmsMessageManagerMessageType|null - Message type */
private ?CmsMessageManagerMessageType $cmsMessageManagerMessageType = null
, 
    // TCMSFieldLookup
/** @var CmsConfig|null - Belongs to CMS config */
private ?CmsConfig $cmsConfig = null
, 
    // TCMSFieldVarchar
/** @var string - Code */
private string $name = ''  ) {}

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
public function getCmsMessageManagerMessageType(): ?CmsMessageManagerMessageType
{
    return $this->cmsMessageManagerMessageType;
}

public function setCmsMessageManagerMessageType(?CmsMessageManagerMessageType $cmsMessageManagerMessageType): self
{
    $this->cmsMessageManagerMessageType = $cmsMessageManagerMessageType;

    return $this;
}


  
    // TCMSFieldLookup
public function getCmsConfig(): ?CmsConfig
{
    return $this->cmsConfig;
}

public function setCmsConfig(?CmsConfig $cmsConfig): self
{
    $this->cmsConfig = $cmsConfig;

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


  
}
