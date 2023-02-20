<?php
namespace ChameleonSystem\CoreBundle\Entity;

class CmsConfigParameter {
  public function __construct(
    private string|null $id = null,
    private int|null $cmsident = null,
          
    // TCMSFieldLookup
/** @var \ChameleonSystem\CoreBundle\Entity\CmsConfig|null - Belongs to CMS config */
private \ChameleonSystem\CoreBundle\Entity\CmsConfig|null $cmsConfig = null,
/** @var null|string - Belongs to CMS config */
private ?string $cmsConfigId = null
, 
    // TCMSFieldVarchar
/** @var string - System name */
private string $systemname = '', 
    // TCMSFieldVarchar
/** @var string - Name / description */
private string $name = '', 
    // TCMSFieldText
/** @var string - Value */
private string $value = ''  ) {}

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
public function getSystemname(): string
{
    return $this->systemname;
}
public function setSystemname(string $systemname): self
{
    $this->systemname = $systemname;

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


  
    // TCMSFieldText
public function getValue(): string
{
    return $this->value;
}
public function setValue(string $value): self
{
    $this->value = $value;

    return $this;
}


  
}
