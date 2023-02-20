<?php
namespace ChameleonSystem\CoreBundle\Entity;

use ChameleonSystem\CoreBundle\Entity\ShopPaymentHandler;

class ShopPaymentHandlerParameter {
  public function __construct(
    private string $id,
    private int|null $cmsident = null,
        
    // TCMSFieldLookupParentID
/** @var ShopPaymentHandler|null - Belongs to payment handler */
private ?ShopPaymentHandler $shopPaymentHandler = null
, 
    // TCMSFieldVarchar
/** @var string - Display name */
private string $name = '', 
    // TCMSFieldVarchar
/** @var string - System name */
private string $systemname = ''  ) {}

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
    // TCMSFieldLookupParentID
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


  
}
