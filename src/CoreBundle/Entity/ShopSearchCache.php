<?php
namespace ChameleonSystem\CoreBundle\Entity;

use ChameleonSystem\CoreBundle\Entity\Shop;
use ChameleonSystem\CoreBundle\Entity\ShopSearchCacheItem;
use Doctrine\Common\Collections\Collection;
use Doctrine\Common\Collections\ArrayCollection;

class ShopSearchCache {
  public function __construct(
    private string $id,
    private int|null $cmsident = null,
        
    // TCMSFieldLookup
/** @var Shop|null - Belongs to shop */
private ?Shop $shop = null
, 
    // TCMSFieldVarchar
/** @var string - Search key */
private string $searchkey = '', 
    // TCMSFieldPropertyTable
/** @var Collection<int, ShopSearchCacheItem> - Results */
private Collection $shopSearchCacheItemCollection = new ArrayCollection()
, 
    // TCMSFieldVarchar
/** @var string - Number of records found */
private string $numberOfRecordsFound = '-1'  ) {}

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
public function getShop(): ?Shop
{
    return $this->shop;
}

public function setShop(?Shop $shop): self
{
    $this->shop = $shop;

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


  
    // TCMSFieldPropertyTable
/**
* @return Collection<int, ShopSearchCacheItem>
*/
public function getShopSearchCacheItemCollection(): Collection
{
    return $this->shopSearchCacheItemCollection;
}

public function addShopSearchCacheItemCollection(ShopSearchCacheItem $shopSearchCacheItem): self
{
    if (!$this->shopSearchCacheItemCollection->contains($shopSearchCacheItem)) {
        $this->shopSearchCacheItemCollection->add($shopSearchCacheItem);
        $shopSearchCacheItem->setShopSearchCache($this);
    }

    return $this;
}

public function removeShopSearchCacheItemCollection(ShopSearchCacheItem $shopSearchCacheItem): self
{
    if ($this->shopSearchCacheItemCollection->removeElement($shopSearchCacheItem)) {
        // set the owning side to null (unless already changed)
        if ($shopSearchCacheItem->getShopSearchCache() === $this) {
            $shopSearchCacheItem->setShopSearchCache(null);
        }
    }

    return $this;
}


  
    // TCMSFieldVarchar
public function getNumberOfRecordsFound(): string
{
    return $this->numberOfRecordsFound;
}
public function setNumberOfRecordsFound(string $numberOfRecordsFound): self
{
    $this->numberOfRecordsFound = $numberOfRecordsFound;

    return $this;
}


  
}
