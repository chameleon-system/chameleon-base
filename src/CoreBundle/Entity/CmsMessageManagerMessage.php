<?php
namespace ChameleonSystem\CoreBundle\Entity;

use ChameleonSystem\CoreBundle\Entity\CmsPortal;
use ChameleonSystem\CoreBundle\Entity\CmsMessageManagerMessageType;

class CmsMessageManagerMessage {
  public function __construct(
    private string $id,
    private int|null $cmsident = null,
        
    // TCMSFieldLookup
/** @var CmsPortal|null - Belongs to portal */
private ?CmsPortal $cmsPortal = null
, 
    // TCMSFieldVarchar
/** @var string - Code */
private string $name = '', 
    // TCMSFieldLookup
/** @var CmsMessageManagerMessageType|null - Message type */
private ?CmsMessageManagerMessageType $cmsMessageManagerMessageType = null
, 
    // TCMSFieldVarchar
/** @var string - View */
private string $messageView = 'standard'  ) {}

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
