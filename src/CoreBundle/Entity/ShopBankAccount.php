<?php
namespace ChameleonSystem\CoreBundle\Entity;

class ShopBankAccount {
  public function __construct(
    private string|null $id = null,
    private int|null $cmsident = null,
          
    // TCMSFieldLookup
/** @var \ChameleonSystem\CoreBundle\Entity\Shop|null - Belongs to shop */
private \ChameleonSystem\CoreBundle\Entity\Shop|null $shop = null,
/** @var null|string - Belongs to shop */
private ?string $shopId = null
, 
    // TCMSFieldVarchar
/** @var string - Name */
private string $name = '', 
    // TCMSFieldVarchar
/** @var string - Account owner */
private string $accountOwner = '', 
    // TCMSFieldVarchar
/** @var string - Bank name */
private string $bankname = '', 
    // TCMSFieldVarchar
/** @var string - Bank code */
private string $bankcode = '', 
    // TCMSFieldVarchar
/** @var string - Account number */
private string $accountNumber = '', 
    // TCMSFieldVarchar
/** @var string - BIC code */
private string $bicCode = '', 
    // TCMSFieldVarchar
/** @var string - IBAN number */
private string $ibannumber = '', 
    // TCMSFieldPosition
/** @var int - Position */
private int $position = 0  ) {}

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
public function getShop(): \ChameleonSystem\CoreBundle\Entity\Shop|null
{
    return $this->shop;
}
public function setShop(\ChameleonSystem\CoreBundle\Entity\Shop|null $shop): self
{
    $this->shop = $shop;
    $this->shopId = $shop?->getId();

    return $this;
}
public function getShopId(): ?string
{
    return $this->shopId;
}
public function setShopId(?string $shopId): self
{
    $this->shopId = $shopId;
    // todo - load new id
    //$this->shopId = $?->getId();

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
public function getAccountOwner(): string
{
    return $this->accountOwner;
}
public function setAccountOwner(string $accountOwner): self
{
    $this->accountOwner = $accountOwner;

    return $this;
}


  
    // TCMSFieldVarchar
public function getBankname(): string
{
    return $this->bankname;
}
public function setBankname(string $bankname): self
{
    $this->bankname = $bankname;

    return $this;
}


  
    // TCMSFieldVarchar
public function getBankcode(): string
{
    return $this->bankcode;
}
public function setBankcode(string $bankcode): self
{
    $this->bankcode = $bankcode;

    return $this;
}


  
    // TCMSFieldVarchar
public function getAccountNumber(): string
{
    return $this->accountNumber;
}
public function setAccountNumber(string $accountNumber): self
{
    $this->accountNumber = $accountNumber;

    return $this;
}


  
    // TCMSFieldVarchar
public function getBicCode(): string
{
    return $this->bicCode;
}
public function setBicCode(string $bicCode): self
{
    $this->bicCode = $bicCode;

    return $this;
}


  
    // TCMSFieldVarchar
public function getIbannumber(): string
{
    return $this->ibannumber;
}
public function setIbannumber(string $ibannumber): self
{
    $this->ibannumber = $ibannumber;

    return $this;
}


  
    // TCMSFieldPosition
public function getPosition(): int
{
    return $this->position;
}
public function setPosition(int $position): self
{
    $this->position = $position;

    return $this;
}


  
}
