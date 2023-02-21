<?php
namespace ChameleonSystem\CoreBundle\Entity;

use ChameleonSystem\CoreBundle\Entity\ShopPaymentHandler;
use ChameleonSystem\CoreBundle\Entity\CmsPortal;

class ShopPaymentHandlerParameter {
  public function __construct(
    private string $id,
    private int|null $cmsident = null,
        
    // TCMSFieldLookup
/** @var ShopPaymentHandler|null - Belongs to payment handler */
private ?ShopPaymentHandler $shopPaymentHandler = null
, 
    // TCMSFieldVarchar
/** @var string - Display name */
private string $name = '', 
    // TCMSFieldVarchar
/** @var string - System name */
private string $systemname = '', 
    // TCMSFieldLookup
/** @var CmsPortal|null - Applies to this portal only */
private ?CmsPortal $cmsPortal = null
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
public function getShopPaymentHandler(): ?ShopPaymentHandler
{
    return $this->shopPaymentHandler;
}

public function setShopPaymentHandler(?ShopPaymentHandler $shopPaymentHandler): self
{
    $this->shopPaymentHandler = $shopPaymentHandler;

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
public function getSystemname(): string
{
    return $this->systemname;
}
public function setSystemname(string $systemname): self
{
    $this->systemname = $systemname;

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


  
}
