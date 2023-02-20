<?php
namespace ChameleonSystem\CoreBundle\Entity;

class CmsMessageManagerMessage {
  public function __construct(
    private string|null $id = null,
    private int|null $cmsident = null,
          
    // TCMSFieldLookup
/** @var \ChameleonSystem\CoreBundle\Entity\CmsPortal|null - Belongs to portal */
private \ChameleonSystem\CoreBundle\Entity\CmsPortal|null $cmsPortal = null,
/** @var null|string - Belongs to portal */
private ?string $cmsPortalId = null
,   
    // TCMSFieldLookup
/** @var \ChameleonSystem\CoreBundle\Entity\CmsMessageManagerMessageType|null - Message type */
private \ChameleonSystem\CoreBundle\Entity\CmsMessageManagerMessageType|null $cmsMessageManagerMessageType = null,
/** @var null|string - Message type */
private ?string $cmsMessageManagerMessageTypeId = null
, 
    // TCMSFieldVarchar
/** @var string - Code */
private string $name = '', 
    // TCMSFieldText
/** @var string - Message description */
private string $description = '', 
    // TCMSFieldText
/** @var string - Message */
private string $message = '', 
    // TCMSFieldOption
/** @var string - Type */
private string $messageLocationType = 'Core', 
    // TCMSFieldVarchar
/** @var string - View */
private string $messageView = 'standard'  ) {}

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
public function getCmsPortal(): \ChameleonSystem\CoreBundle\Entity\CmsPortal|null
{
    return $this->cmsPortal;
}
public function setCmsPortal(\ChameleonSystem\CoreBundle\Entity\CmsPortal|null $cmsPortal): self
{
    $this->cmsPortal = $cmsPortal;
    $this->cmsPortalId = $cmsPortal?->getId();

    return $this;
}
public function getCmsPortalId(): ?string
{
    return $this->cmsPortalId;
}
public function setCmsPortalId(?string $cmsPortalId): self
{
    $this->cmsPortalId = $cmsPortalId;
    // todo - load new id
    //$this->cmsPortalId = $?->getId();

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
public function getDescription(): string
{
    return $this->description;
}
public function setDescription(string $description): self
{
    $this->description = $description;

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


  
    // TCMSFieldOption
public function getMessageLocationType(): string
{
    return $this->messageLocationType;
}
public function setMessageLocationType(string $messageLocationType): self
{
    $this->messageLocationType = $messageLocationType;

    return $this;
}


  
    // TCMSFieldVarchar
public function getMessageView(): string
{
    return $this->messageView;
}
public function setMessageView(string $messageView): self
{
    $this->messageView = $messageView;

    return $this;
}


  
}
