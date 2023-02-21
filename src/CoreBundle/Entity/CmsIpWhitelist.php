<?php
namespace ChameleonSystem\CoreBundle\Entity;

use ChameleonSystem\CoreBundle\Entity\CmsConfig;

class CmsIpWhitelist {
  public function __construct(
    private string $id,
    private int|null $cmsident = null,
        
    // TCMSFieldLookup
/** @var CmsConfig|null - Belongs to cms settings */
private ?CmsConfig $cmsConfig = null
, 
    // TCMSFieldVarchar
/** @var string - IP */
private string $ip = ''  ) {}

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
public function getIp(): string
{
    return $this->ip;
}
public function setIp(string $ip): self
{
    $this->ip = $ip;

    return $this;
}


  
}
