<?php
namespace ChameleonSystem\CoreBundle\Entity;

class PkgShopAffiliate {
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
/** @var string - URL parameter used to transfer the tracking code */
private string $urlParameterName = '', 
    // TCMSFieldNumber
/** @var int - Seconds, for which the code is still valid with inactive session */
private int $numberOfSecondsValid = 0, 
    // TCMSFieldVarchar
/** @var string - Class */
private string $class = '', 
    // TCMSFieldVarchar
/** @var string - Class subtype (path relative to ./classes) */
private string $classSubtype = '', 
    // TCMSFieldOption
/** @var string - Class type */
private string $classType = 'Customer', 
    // TCMSFieldText
/** @var string - Code to be integrated on order success page */
private string $orderSuccessCode = '', 
    // TCMSFieldPropertyTable
/** @var \ChameleonSystem\CoreBundle\Entity\PkgShopAffiliateParameter[] - Parameter */
private \Doctrine\Common\Collections\Collection $pkgShopAffiliateParameterCollection = new \Doctrine\Common\Collections\ArrayCollection()  ) {}

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
public function getUrlParameterName(): string
{
    return $this->urlParameterName;
}
public function setUrlParameterName(string $urlParameterName): self
{
    $this->urlParameterName = $urlParameterName;

    return $this;
}


  
    // TCMSFieldNumber
public function getNumberOfSecondsValid(): int
{
    return $this->numberOfSecondsValid;
}
public function setNumberOfSecondsValid(int $numberOfSecondsValid): self
{
    $this->numberOfSecondsValid = $numberOfSecondsValid;

    return $this;
}


  
    // TCMSFieldVarchar
public function getClass(): string
{
    return $this->class;
}
public function setClass(string $class): self
{
    $this->class = $class;

    return $this;
}


  
    // TCMSFieldVarchar
public function getClassSubtype(): string
{
    return $this->classSubtype;
}
public function setClassSubtype(string $classSubtype): self
{
    $this->classSubtype = $classSubtype;

    return $this;
}


  
    // TCMSFieldOption
public function getClassType(): string
{
    return $this->classType;
}
public function setClassType(string $classType): self
{
    $this->classType = $classType;

    return $this;
}


  
    // TCMSFieldText
public function getOrderSuccessCode(): string
{
    return $this->orderSuccessCode;
}
public function setOrderSuccessCode(string $orderSuccessCode): self
{
    $this->orderSuccessCode = $orderSuccessCode;

    return $this;
}


  
    // TCMSFieldPropertyTable
public function getPkgShopAffiliateParameterCollection(): \Doctrine\Common\Collections\Collection
{
    return $this->pkgShopAffiliateParameterCollection;
}
public function setPkgShopAffiliateParameterCollection(\Doctrine\Common\Collections\Collection $pkgShopAffiliateParameterCollection): self
{
    $this->pkgShopAffiliateParameterCollection = $pkgShopAffiliateParameterCollection;

    return $this;
}


  
}
