<?php
namespace ChameleonSystem\CoreBundle\Entity;

use ChameleonSystem\CoreBundle\Entity\ShopPaymentHandlerGroup;

class PkgShopPaymentIpnStatus {
  public function __construct(
    private string $id,
    private int|null $cmsident = null,
        
    // TCMSFieldLookupParentID
/** @var ShopPaymentHandlerGroup|null - Belongs to the configuration of */
private ?ShopPaymentHandlerGroup $shopPaymentHandlerGroup = null
, 
    // TCMSFieldVarchar
/** @var string - Name */
private string $name = '', 
    // TCMSFieldVarchar
/** @var string - Code (of the provider) */
private string $code = ''  ) {}

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
public function getCode(): string
{
    return $this->code;
}
public function setCode(string $code): self
{
    $this->code = $code;

    return $this;
}


  
}
