<?php
namespace ChameleonSystem\CoreBundle\Entity;

use ChameleonSystem\CoreBundle\Entity\ShopOrder;
use ChameleonSystem\CoreBundle\Entity\ShopOrderBundleArticle;
use Doctrine\Common\Collections\Collection;
use Doctrine\Common\Collections\ArrayCollection;

class ShopOrderItem {
  public function __construct(
    private string $id,
    private int|null $cmsident = null,
        
    // TCMSFieldVarchar
/** @var string - Variant */
private string $nameVariantInfo = '', 
    // TCMSFieldLookupParentID
/** @var ShopOrder|null - Belongs to order */
private ?ShopOrder $shopOrder = null
, 
    // TCMSFieldVarchar
/** @var string - sBasketItemKey is the key for the position in the consumer basket */
private string $basketItemKey = '', 
    // TCMSFieldVarchar
/** @var string - Name */
private string $name = '', 
    // TCMSFieldVarchar
/** @var string - Article number */
private string $articlenumber = '', 
    // TCMSFieldVarchar
/** @var string - Manufacturer / brand name */
private string $shopManufacturerName = '', 
    // TCMSFieldVarchar
/** @var string - Stock at time of order */
private string $stock = '', 
    // TCMSFieldVarchar
/** @var string - Subtitle */
private string $subtitle = '', 
    // TCMSFieldVarchar
/** @var string - Amount of pages */
private string $pages = '', 
    // TCMSFieldVarchar
/** @var string - USP */
private string $usp = '', 
    // TCMSFieldPropertyTable
/** @var Collection<int, shopOrderBundleArticle> - Articles in order that belong to this bundle */
private Collection $shopOrderBundleArticleCollection = new ArrayCollection()
  ) {}

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
    // TCMSFieldVarchar
public function getNameVariantInfo(): string
{
    return $this->nameVariantInfo;
}
public function setNameVariantInfo(string $nameVariantInfo): self
{
    $this->nameVariantInfo = $nameVariantInfo;

    return $this;
}


  
    // TCMSFieldLookupParentID
public function getShopOrder(): ?ShopOrder
{
    return $this->shopOrder;
}

public function setShopOrder(?ShopOrder $shopOrder): self
{
    $this->shopOrder = $shopOrder;

    return $this;
}


  
    // TCMSFieldVarchar
public function getBasketItemKey(): string
{
    return $this->basketItemKey;
}
public function setBasketItemKey(string $basketItemKey): self
{
    $this->basketItemKey = $basketItemKey;

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
public function getArticlenumber(): string
{
    return $this->articlenumber;
}
public function setArticlenumber(string $articlenumber): self
{
    $this->articlenumber = $articlenumber;

    return $this;
}


  
    // TCMSFieldVarchar
public function getShopManufacturerName(): string
{
    return $this->shopManufacturerName;
}
public function setShopManufacturerName(string $shopManufacturerName): self
{
    $this->shopManufacturerName = $shopManufacturerName;

    return $this;
}


  
    // TCMSFieldVarchar
public function getStock(): string
{
    return $this->stock;
}
public function setStock(string $stock): self
{
    $this->stock = $stock;

    return $this;
}


  
    // TCMSFieldVarchar
public function getSubtitle(): string
{
    return $this->subtitle;
}
public function setSubtitle(string $subtitle): self
{
    $this->subtitle = $subtitle;

    return $this;
}


  
    // TCMSFieldVarchar
public function getPages(): string
{
    return $this->pages;
}
public function setPages(string $pages): self
{
    $this->pages = $pages;

    return $this;
}


  
    // TCMSFieldVarchar
public function getUsp(): string
{
    return $this->usp;
}
public function setUsp(string $usp): self
{
    $this->usp = $usp;

    return $this;
}


  
    // TCMSFieldPropertyTable
/**
* @return Collection<int, shopOrderBundleArticle>
*/
public function getShopOrderBundleArticleCollection(): Collection
{
    return $this->shopOrderBundleArticleCollection;
}

public function addShopOrderBundleArticleCollection(shopOrderBundleArticle $shopOrderBundleArticle): self
{
    if (!$this->shopOrderBundleArticleCollection->contains($shopOrderBundleArticle)) {
        $this->shopOrderBundleArticleCollection->add($shopOrderBundleArticle);
        $shopOrderBundleArticle->setShopOrderItem($this);
    }

    return $this;
}

public function removeShopOrderBundleArticleCollection(shopOrderBundleArticle $shopOrderBundleArticle): self
{
    if ($this->shopOrderBundleArticleCollection->removeElement($shopOrderBundleArticle)) {
        // set the owning side to null (unless already changed)
        if ($shopOrderBundleArticle->getShopOrderItem() === $this) {
            $shopOrderBundleArticle->setShopOrderItem(null);
        }
    }

    return $this;
}


  
}
