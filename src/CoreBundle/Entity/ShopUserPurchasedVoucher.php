<?php
namespace ChameleonSystem\CoreBundle\Entity;

class ShopUserPurchasedVoucher {
  public function __construct(
    private string|null $id = null,
    private int|null $cmsident = null,
          
    // TCMSFieldLookup
/** @var \ChameleonSystem\CoreBundle\Entity\DataExtranetUser|null - Belongs to customer */
private \ChameleonSystem\CoreBundle\Entity\DataExtranetUser|null $dataExtranetUser = null,
/** @var null|string - Belongs to customer */
private ?string $dataExtranetUserId = null
,   
    // TCMSFieldLookup
/** @var \ChameleonSystem\CoreBundle\Entity\ShopVoucher|null - Voucher */
private \ChameleonSystem\CoreBundle\Entity\ShopVoucher|null $shopVoucher = null,
/** @var null|string - Voucher */
private ?string $shopVoucherId = null
, 
    // TCMSFieldDateTime
/** @var \DateTime|null - Bought on */
private \DateTime|null $datePurchased = null  ) {}

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
public function getDataExtranetUser(): \ChameleonSystem\CoreBundle\Entity\DataExtranetUser|null
{
    return $this->dataExtranetUser;
}
public function setDataExtranetUser(\ChameleonSystem\CoreBundle\Entity\DataExtranetUser|null $dataExtranetUser): self
{
    $this->dataExtranetUser = $dataExtranetUser;
    $this->dataExtranetUserId = $dataExtranetUser?->getId();

    return $this;
}
public function getDataExtranetUserId(): ?string
{
    return $this->dataExtranetUserId;
}
public function setDataExtranetUserId(?string $dataExtranetUserId): self
{
    $this->dataExtranetUserId = $dataExtranetUserId;
    // todo - load new id
    //$this->dataExtranetUserId = $?->getId();

    return $this;
}



  
    // TCMSFieldLookup
public function getShopVoucher(): \ChameleonSystem\CoreBundle\Entity\ShopVoucher|null
{
    return $this->shopVoucher;
}
public function setShopVoucher(\ChameleonSystem\CoreBundle\Entity\ShopVoucher|null $shopVoucher): self
{
    $this->shopVoucher = $shopVoucher;
    $this->shopVoucherId = $shopVoucher?->getId();

    return $this;
}
public function getShopVoucherId(): ?string
{
    return $this->shopVoucherId;
}
public function setShopVoucherId(?string $shopVoucherId): self
{
    $this->shopVoucherId = $shopVoucherId;
    // todo - load new id
    //$this->shopVoucherId = $?->getId();

    return $this;
}



  
    // TCMSFieldDateTime
public function getDatePurchased(): \DateTime|null
{
    return $this->datePurchased;
}
public function setDatePurchased(\DateTime|null $datePurchased): self
{
    $this->datePurchased = $datePurchased;

    return $this;
}


  
}
