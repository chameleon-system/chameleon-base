<?php
namespace ChameleonSystem\CoreBundle\Entity;

use ChameleonSystem\CoreBundle\Entity\ShopPaymentHandlerGroup;
use ChameleonSystem\CoreBundle\Entity\ShopPaymentHandler;
use ChameleonSystem\CoreBundle\Entity\ShopVat;
use ChameleonSystem\CoreBundle\Entity\CmsMedia;

class ShopPaymentMethod {
  public function __construct(
    private string $id,
    private int|null $cmsident = null,
        
    // TCMSFieldLookup
/** @var ShopPaymentHandlerGroup|null - Belongs to payment provider */
private ?ShopPaymentHandlerGroup $shopPaymentHandlerGroup = null
, 
    // TCMSFieldVarchar
/** @var string - Name */
private string $name = '', 
    // TCMSFieldVarchar
/** @var string - Internal system name */
private string $nameInternal = '', 
    // TCMSFieldLookup
/** @var ShopPaymentHandler|null - Payment handler */
private ?ShopPaymentHandler $shopPaymentHandler = null
, 
    // TCMSFieldLookup
/** @var ShopVat|null - VAT group */
private ?ShopVat $shopVat = null
, 
    // TCMSFieldLookup
/** @var CmsMedia|null - Icon */
private ?CmsMedia $cmsMedia = null
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
public function getShopPaymentHandlerGroup(): ?ShopPaymentHandlerGroup
{
    return $this->shopPaymentHandlerGroup;
}

public function setShopPaymentHandlerGroup(?ShopPaymentHandlerGroup $shopPaymentHandlerGroup): self
{
    $this->shopPaymentHandlerGroup = $shopPaymentHandlerGroup;

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
public function getNameInternal(): string
{
    return $this->nameInternal;
}
public function setNameInternal(string $nameInternal): self
{
    $this->nameInternal = $nameInternal;

    return $this;
}


  
    // TCMSFieldLookup
public function getShopPaymentHandler(): ?ShopPaymentHandler
{
    return $this->shopPaymentHandler;
}

public function setShopPaymentHandler(?ShopPaymentHandler $shopPaymentHandler): self
{
    $this->shopPaymentHandler = $shopPaymentHandler;

    return $this;
}


  
    // TCMSFieldLookup
public function getShopVat(): ?ShopVat
{
    return $this->shopVat;
}

public function setShopVat(?ShopVat $shopVat): self
{
    $this->shopVat = $shopVat;

    return $this;
}


  
    // TCMSFieldLookup
public function getCmsMedia(): ?CmsMedia
{
    return $this->cmsMedia;
}

public function setCmsMedia(?CmsMedia $cmsMedia): self
{
    $this->cmsMedia = $cmsMedia;

    return $this;
}


  
}
