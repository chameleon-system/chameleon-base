<?php
namespace ChameleonSystem\CoreBundle\Entity;

class ShopVoucherSeries {
  public function __construct(
    private string|null $id = null,
    private int|null $cmsident = null,
          
    // TCMSFieldLookup
/** @var \ChameleonSystem\CoreBundle\Entity\ShopVoucherSeriesSponsor|null - Voucher sponsor */
private \ChameleonSystem\CoreBundle\Entity\ShopVoucherSeriesSponsor|null $shopVoucherSeriesSponsor = null,
/** @var null|string - Voucher sponsor */
private ?string $shopVoucherSeriesSponsorId = null
,   
    // TCMSFieldLookup
/** @var \ChameleonSystem\CoreBundle\Entity\ShopVat|null - VAT group */
private \ChameleonSystem\CoreBundle\Entity\ShopVat|null $shopVat = null,
/** @var null|string - VAT group */
private ?string $shopVatId = null
, 
    // TCMSFieldVarchar
/** @var string - Name */
private string $name = '', 
    // TCMSFieldPrice
/** @var float - Value */
private float $value = 0, 
    // TCMSFieldOption
/** @var string - Value type */
private string $valueType = 'absolut', 
    // TCMSFieldBoolean
/** @var bool - Free shipping */
private bool $freeShipping = false, 
    // TCMSFieldBoolean
/** @var bool - Active */
private bool $active = false, 
    // TCMSFieldDateTime
/** @var \DateTime|null - Active from */
private \DateTime|null $activeFrom = null, 
    // TCMSFieldDateTime
/** @var \DateTime|null - Active until */
private \DateTime|null $activeTo = null, 
    // TCMSFieldPrice
/** @var float - Minimum order value */
private float $restrictToValue = 0, 
    // TCMSFieldBoolean
/** @var bool - Allow with other series only */
private bool $restrictToOtherSeries = true, 
    // TCMSFieldBoolean
/** @var bool - Do not allow in combination with other vouchers */
private bool $allowNoOtherVouchers = true, 
    // TCMSFieldBoolean
/** @var bool - Allow one voucher per customer only */
private bool $restrictToOnePerUser = false, 
    // TCMSFieldBoolean
/** @var bool - Only allow at first order of a customer */
private bool $restrictToFirstOrder = false, 
    // TCMSFieldLookupMultiselect
/** @var \ChameleonSystem\CoreBundle\Entity\DataExtranetUser[] - Restrict to following customers */
private \Doctrine\Common\Collections\Collection $dataExtranetUserMlt = new \Doctrine\Common\Collections\ArrayCollection(), 
    // TCMSFieldLookupMultiselect
/** @var \ChameleonSystem\CoreBundle\Entity\DataExtranetGroup[] - Restrict to following customer groups */
private \Doctrine\Common\Collections\Collection $dataExtranetGroupMlt = new \Doctrine\Common\Collections\ArrayCollection(), 
    // TCMSFieldLookupMultiselect
/** @var \ChameleonSystem\CoreBundle\Entity\ShopManufacturer[] - Restrict to products from this manufacturer */
private \Doctrine\Common\Collections\Collection $shopManufacturerMlt = new \Doctrine\Common\Collections\ArrayCollection(), 
    // TCMSFieldLookupMultiselect
/** @var \ChameleonSystem\CoreBundle\Entity\ShopArticleGroup[] - Restrict to products from these product groups */
private \Doctrine\Common\Collections\Collection $shopArticleGroupMlt = new \Doctrine\Common\Collections\ArrayCollection(), 
    // TCMSFieldLookupMultiselect
/** @var \ChameleonSystem\CoreBundle\Entity\ShopCategory[] - Restrict to products from these product categories */
private \Doctrine\Common\Collections\Collection $shopCategoryMlt = new \Doctrine\Common\Collections\ArrayCollection(), 
    // TCMSFieldLookupMultiselect
/** @var \ChameleonSystem\CoreBundle\Entity\ShopArticle[] - Restrict to these products */
private \Doctrine\Common\Collections\Collection $shopArticleMlt = new \Doctrine\Common\Collections\ArrayCollection(), 
    // TCMSFieldPropertyTable
/** @var \ChameleonSystem\CoreBundle\Entity\ShopVoucher[] - Vouchers belonging to the series */
private \Doctrine\Common\Collections\Collection $shopVoucherCollection = new \Doctrine\Common\Collections\ArrayCollection()  ) {}

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
public function getShopVoucherSeriesSponsor(): \ChameleonSystem\CoreBundle\Entity\ShopVoucherSeriesSponsor|null
{
    return $this->shopVoucherSeriesSponsor;
}
public function setShopVoucherSeriesSponsor(\ChameleonSystem\CoreBundle\Entity\ShopVoucherSeriesSponsor|null $shopVoucherSeriesSponsor): self
{
    $this->shopVoucherSeriesSponsor = $shopVoucherSeriesSponsor;
    $this->shopVoucherSeriesSponsorId = $shopVoucherSeriesSponsor?->getId();

    return $this;
}
public function getShopVoucherSeriesSponsorId(): ?string
{
    return $this->shopVoucherSeriesSponsorId;
}
public function setShopVoucherSeriesSponsorId(?string $shopVoucherSeriesSponsorId): self
{
    $this->shopVoucherSeriesSponsorId = $shopVoucherSeriesSponsorId;
    // todo - load new id
    //$this->shopVoucherSeriesSponsorId = $?->getId();

    return $this;
}



  
    // TCMSFieldPrice
public function getValue(): float
{
    return $this->value;
}
public function setValue(float $value): self
{
    $this->value = $value;

    return $this;
}


  
    // TCMSFieldOption
public function getValueType(): string
{
    return $this->valueType;
}
public function setValueType(string $valueType): self
{
    $this->valueType = $valueType;

    return $this;
}


  
    // TCMSFieldLookup
public function getShopVat(): \ChameleonSystem\CoreBundle\Entity\ShopVat|null
{
    return $this->shopVat;
}
public function setShopVat(\ChameleonSystem\CoreBundle\Entity\ShopVat|null $shopVat): self
{
    $this->shopVat = $shopVat;
    $this->shopVatId = $shopVat?->getId();

    return $this;
}
public function getShopVatId(): ?string
{
    return $this->shopVatId;
}
public function setShopVatId(?string $shopVatId): self
{
    $this->shopVatId = $shopVatId;
    // todo - load new id
    //$this->shopVatId = $?->getId();

    return $this;
}



  
    // TCMSFieldBoolean
public function isFreeShipping(): bool
{
    return $this->freeShipping;
}
public function setFreeShipping(bool $freeShipping): self
{
    $this->freeShipping = $freeShipping;

    return $this;
}


  
    // TCMSFieldBoolean
public function isActive(): bool
{
    return $this->active;
}
public function setActive(bool $active): self
{
    $this->active = $active;

    return $this;
}


  
    // TCMSFieldDateTime
public function getActiveFrom(): \DateTime|null
{
    return $this->activeFrom;
}
public function setActiveFrom(\DateTime|null $activeFrom): self
{
    $this->activeFrom = $activeFrom;

    return $this;
}


  
    // TCMSFieldDateTime
public function getActiveTo(): \DateTime|null
{
    return $this->activeTo;
}
public function setActiveTo(\DateTime|null $activeTo): self
{
    $this->activeTo = $activeTo;

    return $this;
}


  
    // TCMSFieldPrice
public function getRestrictToValue(): float
{
    return $this->restrictToValue;
}
public function setRestrictToValue(float $restrictToValue): self
{
    $this->restrictToValue = $restrictToValue;

    return $this;
}


  
    // TCMSFieldBoolean
public function isRestrictToOtherSeries(): bool
{
    return $this->restrictToOtherSeries;
}
public function setRestrictToOtherSeries(bool $restrictToOtherSeries): self
{
    $this->restrictToOtherSeries = $restrictToOtherSeries;

    return $this;
}


  
    // TCMSFieldBoolean
public function isAllowNoOtherVouchers(): bool
{
    return $this->allowNoOtherVouchers;
}
public function setAllowNoOtherVouchers(bool $allowNoOtherVouchers): self
{
    $this->allowNoOtherVouchers = $allowNoOtherVouchers;

    return $this;
}


  
    // TCMSFieldBoolean
public function isRestrictToOnePerUser(): bool
{
    return $this->restrictToOnePerUser;
}
public function setRestrictToOnePerUser(bool $restrictToOnePerUser): self
{
    $this->restrictToOnePerUser = $restrictToOnePerUser;

    return $this;
}


  
    // TCMSFieldBoolean
public function isRestrictToFirstOrder(): bool
{
    return $this->restrictToFirstOrder;
}
public function setRestrictToFirstOrder(bool $restrictToFirstOrder): self
{
    $this->restrictToFirstOrder = $restrictToFirstOrder;

    return $this;
}


  
    // TCMSFieldLookupMultiselect
public function getDataExtranetUserMlt(): \Doctrine\Common\Collections\Collection
{
    return $this->dataExtranetUserMlt;
}
public function setDataExtranetUserMlt(\Doctrine\Common\Collections\Collection $dataExtranetUserMlt): self
{
    $this->dataExtranetUserMlt = $dataExtranetUserMlt;

    return $this;
}


  
    // TCMSFieldLookupMultiselect
public function getDataExtranetGroupMlt(): \Doctrine\Common\Collections\Collection
{
    return $this->dataExtranetGroupMlt;
}
public function setDataExtranetGroupMlt(\Doctrine\Common\Collections\Collection $dataExtranetGroupMlt): self
{
    $this->dataExtranetGroupMlt = $dataExtranetGroupMlt;

    return $this;
}


  
    // TCMSFieldLookupMultiselect
public function getShopManufacturerMlt(): \Doctrine\Common\Collections\Collection
{
    return $this->shopManufacturerMlt;
}
public function setShopManufacturerMlt(\Doctrine\Common\Collections\Collection $shopManufacturerMlt): self
{
    $this->shopManufacturerMlt = $shopManufacturerMlt;

    return $this;
}


  
    // TCMSFieldLookupMultiselect
public function getShopArticleGroupMlt(): \Doctrine\Common\Collections\Collection
{
    return $this->shopArticleGroupMlt;
}
public function setShopArticleGroupMlt(\Doctrine\Common\Collections\Collection $shopArticleGroupMlt): self
{
    $this->shopArticleGroupMlt = $shopArticleGroupMlt;

    return $this;
}


  
    // TCMSFieldLookupMultiselect
public function getShopCategoryMlt(): \Doctrine\Common\Collections\Collection
{
    return $this->shopCategoryMlt;
}
public function setShopCategoryMlt(\Doctrine\Common\Collections\Collection $shopCategoryMlt): self
{
    $this->shopCategoryMlt = $shopCategoryMlt;

    return $this;
}


  
    // TCMSFieldLookupMultiselect
public function getShopArticleMlt(): \Doctrine\Common\Collections\Collection
{
    return $this->shopArticleMlt;
}
public function setShopArticleMlt(\Doctrine\Common\Collections\Collection $shopArticleMlt): self
{
    $this->shopArticleMlt = $shopArticleMlt;

    return $this;
}


  
    // TCMSFieldPropertyTable
public function getShopVoucherCollection(): \Doctrine\Common\Collections\Collection
{
    return $this->shopVoucherCollection;
}
public function setShopVoucherCollection(\Doctrine\Common\Collections\Collection $shopVoucherCollection): self
{
    $this->shopVoucherCollection = $shopVoucherCollection;

    return $this;
}


  
}
