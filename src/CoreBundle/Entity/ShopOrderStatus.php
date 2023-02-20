<?php
namespace ChameleonSystem\CoreBundle\Entity;

class ShopOrderStatus {
  public function __construct(
    private string|null $id = null,
    private int|null $cmsident = null,
          
    // TCMSFieldLookup
/** @var \ChameleonSystem\CoreBundle\Entity\ShopOrder|null - Belongs to order */
private \ChameleonSystem\CoreBundle\Entity\ShopOrder|null $shopOrder = null,
/** @var null|string - Belongs to order */
private ?string $shopOrderId = null
,   
    // TCMSFieldLookup
/** @var \ChameleonSystem\CoreBundle\Entity\ShopOrderStatusCode|null - Status code */
private \ChameleonSystem\CoreBundle\Entity\ShopOrderStatusCode|null $shopOrderStatusCode = null,
/** @var null|string - Status code */
private ?string $shopOrderStatusCodeId = null
, 
    // TCMSFieldDateTimeNow
/** @var \DateTime|null - Date */
private \DateTime|null $statusDate = null, 
    // TCMSFieldBlob
/** @var string - Data */
private string $data = '', 
    // TCMSFieldWYSIWYG
/** @var string - Additional info */
private string $info = '', 
    // TCMSFieldPropertyTable
/** @var \ChameleonSystem\CoreBundle\Entity\ShopOrderStatusItem[] - Order status items */
private \Doctrine\Common\Collections\Collection $shopOrderStatusItemCollection = new \Doctrine\Common\Collections\ArrayCollection()  ) {}

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
public function getShopOrder(): \ChameleonSystem\CoreBundle\Entity\ShopOrder|null
{
    return $this->shopOrder;
}
public function setShopOrder(\ChameleonSystem\CoreBundle\Entity\ShopOrder|null $shopOrder): self
{
    $this->shopOrder = $shopOrder;
    $this->shopOrderId = $shopOrder?->getId();

    return $this;
}
public function getShopOrderId(): ?string
{
    return $this->shopOrderId;
}
public function setShopOrderId(?string $shopOrderId): self
{
    $this->shopOrderId = $shopOrderId;
    // todo - load new id
    //$this->shopOrderId = $?->getId();

    return $this;
}



  
    // TCMSFieldDateTimeNow
public function getStatusDate(): \DateTime|null
{
    return $this->statusDate;
}
public function setStatusDate(\DateTime|null $statusDate): self
{
    $this->statusDate = $statusDate;

    return $this;
}


  
    // TCMSFieldLookup
public function getShopOrderStatusCode(): \ChameleonSystem\CoreBundle\Entity\ShopOrderStatusCode|null
{
    return $this->shopOrderStatusCode;
}
public function setShopOrderStatusCode(\ChameleonSystem\CoreBundle\Entity\ShopOrderStatusCode|null $shopOrderStatusCode): self
{
    $this->shopOrderStatusCode = $shopOrderStatusCode;
    $this->shopOrderStatusCodeId = $shopOrderStatusCode?->getId();

    return $this;
}
public function getShopOrderStatusCodeId(): ?string
{
    return $this->shopOrderStatusCodeId;
}
public function setShopOrderStatusCodeId(?string $shopOrderStatusCodeId): self
{
    $this->shopOrderStatusCodeId = $shopOrderStatusCodeId;
    // todo - load new id
    //$this->shopOrderStatusCodeId = $?->getId();

    return $this;
}



  
    // TCMSFieldBlob
public function getData(): string
{
    return $this->data;
}
public function setData(string $data): self
{
    $this->data = $data;

    return $this;
}


  
    // TCMSFieldWYSIWYG
public function getInfo(): string
{
    return $this->info;
}
public function setInfo(string $info): self
{
    $this->info = $info;

    return $this;
}


  
    // TCMSFieldPropertyTable
public function getShopOrderStatusItemCollection(): \Doctrine\Common\Collections\Collection
{
    return $this->shopOrderStatusItemCollection;
}
public function setShopOrderStatusItemCollection(\Doctrine\Common\Collections\Collection $shopOrderStatusItemCollection): self
{
    $this->shopOrderStatusItemCollection = $shopOrderStatusItemCollection;

    return $this;
}


  
}
