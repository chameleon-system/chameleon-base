<?php
namespace ChameleonSystem\CoreBundle\Entity;

class CmsMessageManagerBackendMessage {
  public function __construct(
    private string|null $id = null,
    private int|null $cmsident = null,
          
    // TCMSFieldLookup
/** @var \ChameleonSystem\CoreBundle\Entity\CmsMessageManagerMessageType|null - Message type */
private \ChameleonSystem\CoreBundle\Entity\CmsMessageManagerMessageType|null $cmsMessageManagerMessageType = null,
/** @var null|string - Message type */
private ?string $cmsMessageManagerMessageTypeId = null
,   
    // TCMSFieldLookup
/** @var \ChameleonSystem\CoreBundle\Entity\CmsConfig|null - Belongs to CMS config */
private \ChameleonSystem\CoreBundle\Entity\CmsConfig|null $cmsConfig = null,
/** @var null|string - Belongs to CMS config */
private ?string $cmsConfigId = null
, 
    // TCMSFieldText
/** @var string - Message */
private string $message = '', 
    // TCMSFieldVarchar
/** @var string - Code */
private string $name = '', 
    // TCMSFieldText
/** @var string - Message description */
private string $description = ''  ) {}

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
public function getCmsMessageManagerMessageType(): \ChameleonSystem\CoreBundle\Entity\CmsMessageManagerMessageType|null
{
    return $this->cmsMessageManagerMessageType;
}
public function setCmsMessageManagerMessageType(\ChameleonSystem\CoreBundle\Entity\CmsMessageManagerMessageType|null $cmsMessageManagerMessageType): self
{
    $this->cmsMessageManagerMessageType = $cmsMessageManagerMessageType;
    $this->cmsMessageManagerMessageTypeId = $cmsMessageManagerMessageType?->getId();

    return $this;
}
public function getCmsMessageManagerMessageTypeId(): ?string
{
    return $this->cmsMessageManagerMessageTypeId;
}
public function setCmsMessageManagerMessageTypeId(?string $cmsMessageManagerMessageTypeId): self
{
    $this->cmsMessageManagerMessageTypeId = $cmsMessageManagerMessageTypeId;
    // todo - load new id
    //$this->cmsMessageManagerMessageTypeId = $?->getId();

    return $this;
}



  
    // TCMSFieldText
public function getMessage(): string
{
    return $this->message;
}
public function setMessage(string $message): self
{
    $this->message = $message;

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
public function getDescription(): string
{
    return $this->description;
}
public function setDescription(string $description): self
{
    $this->description = $description;

    return $this;
}


  
}
