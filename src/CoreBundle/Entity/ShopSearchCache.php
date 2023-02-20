<?php
namespace ChameleonSystem\CoreBundle\Entity;

class ShopSearchCache {
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
/** @var string - Search key */
private string $searchkey = '', 
    // TCMSFieldDateTime
/** @var \DateTime|null - Last used */
private \DateTime|null $lastUsedDate = null, 
    // TCMSFieldPropertyTable
/** @var \ChameleonSystem\CoreBundle\Entity\ShopSearchCacheItem[] - Results */
private \Doctrine\Common\Collections\Collection $shopSearchCacheItemCollection = new \Doctrine\Common\Collections\ArrayCollection(), 
    // TCMSFieldText
/** @var string - Category hits */
private string $categoryHits = '', 
    // TCMSFieldNumber
/** @var int - Number of records found */
private int $numberOfRecordsFound = -1  ) {}

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
public function getSearchkey(): string
{
    return $this->searchkey;
}
public function setSearchkey(string $searchkey): self
{
    $this->searchkey = $searchkey;

    return $this;
}


  
    // TCMSFieldDateTime
public function getLastUsedDate(): \DateTime|null
{
    return $this->lastUsedDate;
}
public function setLastUsedDate(\DateTime|null $lastUsedDate): self
{
    $this->lastUsedDate = $lastUsedDate;

    return $this;
}


  
    // TCMSFieldPropertyTable
public function getShopSearchCacheItemCollection(): \Doctrine\Common\Collections\Collection
{
    return $this->shopSearchCacheItemCollection;
}
public function setShopSearchCacheItemCollection(\Doctrine\Common\Collections\Collection $shopSearchCacheItemCollection): self
{
    $this->shopSearchCacheItemCollection = $shopSearchCacheItemCollection;

    return $this;
}


  
    // TCMSFieldText
public function getCategoryHits(): string
{
    return $this->categoryHits;
}
public function setCategoryHits(string $categoryHits): self
{
    $this->categoryHits = $categoryHits;

    return $this;
}


  
    // TCMSFieldNumber
public function getNumberOfRecordsFound(): int
{
    return $this->numberOfRecordsFound;
}
public function setNumberOfRecordsFound(int $numberOfRecordsFound): self
{
    $this->numberOfRecordsFound = $numberOfRecordsFound;

    return $this;
}


  
}
