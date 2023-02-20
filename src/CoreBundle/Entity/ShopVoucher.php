<?php
namespace ChameleonSystem\CoreBundle\Entity;

class ShopVoucher {
  public function __construct(
    private string|null $id = null,
    private int|null $cmsident = null,
          
    // TCMSFieldLookup
/** @var \ChameleonSystem\CoreBundle\Entity\ShopVoucherSeries|null - Belongs to voucher series */
private \ChameleonSystem\CoreBundle\Entity\ShopVoucherSeries|null $shopVoucherSeries = null,
/** @var null|string - Belongs to voucher series */
private ?string $shopVoucherSeriesId = null
, 
    // TCMSFieldVarchar
/** @var string - Code */
private string $code = '', 
    // TCMSFieldDateTime
/** @var \DateTime|null - Created on */
private \DateTime|null $datecreated = null, 
    // TCMSFieldDateTime
/** @var \DateTime|null - Used up on */
private \DateTime|null $dateUsedUp = null, 
    // TCMSFieldBoolean
/** @var bool - Is used up */
private bool $isUsedUp = false, 
    // TCMSFieldPropertyTable
/** @var \ChameleonSystem\CoreBundle\Entity\ShopVoucherUse[] - Voucher usages */
private \Doctrine\Common\Collections\Collection $shopVoucherUseCollection = new \Doctrine\Common\Collections\ArrayCollection()  ) {}

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
public function getShopVoucherSeries(): \ChameleonSystem\CoreBundle\Entity\ShopVoucherSeries|null
{
    return $this->shopVoucherSeries;
}
public function setShopVoucherSeries(\ChameleonSystem\CoreBundle\Entity\ShopVoucherSeries|null $shopVoucherSeries): self
{
    $this->shopVoucherSeries = $shopVoucherSeries;
    $this->shopVoucherSeriesId = $shopVoucherSeries?->getId();

    return $this;
}
public function getShopVoucherSeriesId(): ?string
{
    return $this->shopVoucherSeriesId;
}
public function setShopVoucherSeriesId(?string $shopVoucherSeriesId): self
{
    $this->shopVoucherSeriesId = $shopVoucherSeriesId;
    // todo - load new id
    //$this->shopVoucherSeriesId = $?->getId();

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


  
    // TCMSFieldDateTime
public function getDatecreated(): \DateTime|null
{
    return $this->datecreated;
}
public function setDatecreated(\DateTime|null $datecreated): self
{
    $this->datecreated = $datecreated;

    return $this;
}


  
    // TCMSFieldDateTime
public function getDateUsedUp(): \DateTime|null
{
    return $this->dateUsedUp;
}
public function setDateUsedUp(\DateTime|null $dateUsedUp): self
{
    $this->dateUsedUp = $dateUsedUp;

    return $this;
}


  
    // TCMSFieldBoolean
public function isIsUsedUp(): bool
{
    return $this->isUsedUp;
}
public function setIsUsedUp(bool $isUsedUp): self
{
    $this->isUsedUp = $isUsedUp;

    return $this;
}


  
    // TCMSFieldPropertyTable
public function getShopVoucherUseCollection(): \Doctrine\Common\Collections\Collection
{
    return $this->shopVoucherUseCollection;
}
public function setShopVoucherUseCollection(\Doctrine\Common\Collections\Collection $shopVoucherUseCollection): self
{
    $this->shopVoucherUseCollection = $shopVoucherUseCollection;

    return $this;
}


  
}
