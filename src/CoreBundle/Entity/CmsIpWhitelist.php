<?php
namespace ChameleonSystem\CoreBundle\Entity;

class CmsIpWhitelist {
  public function __construct(
    private string|null $id = null,
    private int|null $cmsident = null,
          
    // TCMSFieldLookup
/** @var \ChameleonSystem\CoreBundle\Entity\CmsConfig|null - Belongs to cms settings */
private \ChameleonSystem\CoreBundle\Entity\CmsConfig|null $cmsConfig = null,
/** @var null|string - Belongs to cms settings */
private ?string $cmsConfigId = null
, 
    // TCMSFieldVarchar
/** @var string - IP */
private string $ip = ''  ) {}

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
public function getCmsConfig(): \ChameleonSystem\CoreBundle\Entity\CmsConfig|null
{
    return $this->cmsConfig;
}
public function setCmsConfig(\ChameleonSystem\CoreBundle\Entity\CmsConfig|null $cmsConfig): self
{
    $this->cmsConfig = $cmsConfig;
    $this->cmsConfigId = $cmsConfig?->getId();

    return $this;
}
public function getCmsConfigId(): ?string
{
    return $this->cmsConfigId;
}
public function setCmsConfigId(?string $cmsConfigId): self
{
    $this->cmsConfigId = $cmsConfigId;
    // todo - load new id
    //$this->cmsConfigId = $?->getId();

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
