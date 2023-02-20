<?php
namespace ChameleonSystem\CoreBundle\Entity;

class ShopPaymentHandlerParameter {
  public function __construct(
    private string|null $id = null,
    private int|null $cmsident = null,
          
    // TCMSFieldLookup
/** @var \ChameleonSystem\CoreBundle\Entity\ShopPaymentHandler|null - Belongs to payment handler */
private \ChameleonSystem\CoreBundle\Entity\ShopPaymentHandler|null $shopPaymentHandler = null,
/** @var null|string - Belongs to payment handler */
private ?string $shopPaymentHandlerId = null
,   
    // TCMSFieldLookup
/** @var \ChameleonSystem\CoreBundle\Entity\CmsPortal|null - Applies to this portal only */
private \ChameleonSystem\CoreBundle\Entity\CmsPortal|null $cmsPortal = null,
/** @var null|string - Applies to this portal only */
private ?string $cmsPortalId = null
, 
    // TCMSFieldVarchar
/** @var string - Display name */
private string $name = '', 
    // TCMSFieldOption
/** @var string - Type */
private string $type = 'common', 
    // TCMSFieldVarchar
/** @var string - System name */
private string $systemname = '', 
    // TCMSFieldWYSIWYG
/** @var string - Description */
private string $description = '', 
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
public function getShopPaymentHandler(): \ChameleonSystem\CoreBundle\Entity\ShopPaymentHandler|null
{
    return $this->shopPaymentHandler;
}
public function setShopPaymentHandler(\ChameleonSystem\CoreBundle\Entity\ShopPaymentHandler|null $shopPaymentHandler): self
{
    $this->shopPaymentHandler = $shopPaymentHandler;
    $this->shopPaymentHandlerId = $shopPaymentHandler?->getId();

    return $this;
}
public function getShopPaymentHandlerId(): ?string
{
    return $this->shopPaymentHandlerId;
}
public function setShopPaymentHandlerId(?string $shopPaymentHandlerId): self
{
    $this->shopPaymentHandlerId = $shopPaymentHandlerId;
    // todo - load new id
    //$this->shopPaymentHandlerId = $?->getId();

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


  
    // TCMSFieldOption
public function getType(): string
{
    return $this->type;
}
public function setType(string $type): self
{
    $this->type = $type;

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


  
    // TCMSFieldWYSIWYG
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
public function getValue(): string
{
    return $this->value;
}
public function setValue(string $value): self
{
    $this->value = $value;

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



  
}
