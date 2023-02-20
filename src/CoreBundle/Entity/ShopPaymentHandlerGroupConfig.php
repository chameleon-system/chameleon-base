<?php
namespace ChameleonSystem\CoreBundle\Entity;

class ShopPaymentHandlerGroupConfig {
  public function __construct(
    private string|null $id = null,
    private int|null $cmsident = null,
          
    // TCMSFieldLookup
/** @var \ChameleonSystem\CoreBundle\Entity\ShopPaymentHandlerGroup|null - Belongs to */
private \ChameleonSystem\CoreBundle\Entity\ShopPaymentHandlerGroup|null $shopPaymentHandlerGroup = null,
/** @var null|string - Belongs to */
private ?string $shopPaymentHandlerGroupId = null
,   
    // TCMSFieldLookup
/** @var \ChameleonSystem\CoreBundle\Entity\CmsPortal|null - Portal */
private \ChameleonSystem\CoreBundle\Entity\CmsPortal|null $cmsPortal = null,
/** @var null|string - Portal */
private ?string $cmsPortalId = null
, 
    // TCMSFieldVarchar
/** @var string - Name */
private string $name = '', 
    // TCMSFieldOption
/** @var string - Type */
private string $type = 'common', 
    // TCMSFieldText
/** @var string - Value */
private string $value = '', 
    // TCMSFieldWYSIWYG
/** @var string - Description */
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
public function getShopPaymentHandlerGroup(): \ChameleonSystem\CoreBundle\Entity\ShopPaymentHandlerGroup|null
{
    return $this->shopPaymentHandlerGroup;
}
public function setShopPaymentHandlerGroup(\ChameleonSystem\CoreBundle\Entity\ShopPaymentHandlerGroup|null $shopPaymentHandlerGroup): self
{
    $this->shopPaymentHandlerGroup = $shopPaymentHandlerGroup;
    $this->shopPaymentHandlerGroupId = $shopPaymentHandlerGroup?->getId();

    return $this;
}
public function getShopPaymentHandlerGroupId(): ?string
{
    return $this->shopPaymentHandlerGroupId;
}
public function setShopPaymentHandlerGroupId(?string $shopPaymentHandlerGroupId): self
{
    $this->shopPaymentHandlerGroupId = $shopPaymentHandlerGroupId;
    // todo - load new id
    //$this->shopPaymentHandlerGroupId = $?->getId();

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


  
}
